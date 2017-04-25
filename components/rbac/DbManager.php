<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\components\rbac;

use Yii;
use yii\caching\Cache;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Expression;
use yii\base\InvalidCallException;
use yii\base\InvalidParamException;
use yii\di\Instance;

/**
 * DbManager represents an authorization manager that stores authorization information in database.
 *
 * The database connection is specified by [[db]]. The database schema could be initialized by applying migration:
 *
 * ```
 * yii migrate --migrationPath=@yii/rbac/migrations/
 * ```
 *
 * If you don't want to use migration and need SQL instead, files for all databases are in migrations directory.
 *
 * You may change the names of the tables used to store the authorization and rule data by setting [[itemTable]],
 * [[itemChildTable]], [[assignmentTable]] and [[ruleTable]].
 *
 * For more details and usage information on DbManager, see the [guide article on security authorization](guide:security-authorization).
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @author Alexander Kochetov <creocoder@gmail.com>
 * @since 2.0
 */
class DbManager extends BaseManager
{
    /**
     * @var Connection|array|string the DB connection object or the application component ID of the DB connection.
     * After the DbManager object is created, if you want to change this property, you should only assign it
     * with a DB connection object.
     * Starting from version 2.0.2, this can also be a configuration array for creating the object.
     */
    public $db = 'db';
    /**
     * @var string the name of the table storing authorization items. Defaults to "auth_item".
     */
    public $itemTable = '{{%auth_item}}';
    /**
     * @var string the name of the table storing authorization item hierarchy. Defaults to "auth_item_child".
     */
    public $itemChildTable = '{{%auth_item_child}}';
    /**
     * @var string the name of the table storing authorization item assignments. Defaults to "auth_assignment".
     */
    public $assignmentTable = '{{%auth_assignment}}';
    /**
     * @var string the name of the table storing rules. Defaults to "auth_rule".
     */
    public $ruleTable = '{{%auth_rule}}';
    /**
     * @var string 菜单表
     */
    public $menuTable = '{{%auth_menu}}';
    /**
     * @var string 菜单操作关联表
     */
    public $menuChildTable = '{{%auth_menu_child}}';
    /**
     * @var string 应用表
     */
    public $applicationTable = '{{%auth_application}}';

    /**
     * @var Cache|array|string the cache used to improve RBAC performance. This can be one of the following:
     *
     * - an application component ID (e.g. `cache`)
     * - a configuration array
     * - a [[\yii\caching\Cache]] object
     *
     * When this is not set, it means caching is not enabled.
     *
     * Note that by enabling RBAC cache, all auth items, rules and auth item parent-child relationships will
     * be cached and loaded into memory. This will improve the performance of RBAC permission check. However,
     * it does require extra memory and as a result may not be appropriate if your RBAC system contains too many
     * auth items. You should seek other RBAC implementations (e.g. RBAC based on Redis storage) in this case.
     *
     * Also note that if you modify RBAC items, rules or parent-child relationships from outside of this component,
     * you have to manually call [[invalidateCache()]] to ensure data consistency.
     *
     * @since 2.0.3
     */
    public $cache='cache';
    /**
     * @var string the key used to store RBAC data in cache
     * @see cache
     * @since 2.0.3
     */
    public $cacheKey = 'rbac';
    /**
     * @var Item[] all auth items (name => Item)
     */
    protected $items;
    /**
     * @var Rule[] all auth rules (name => Rule)
     */
    protected $rules;
    /**
     * @var array auth item parent-child relationships (childName => list of parents)
     */
    protected $parents;
    /**
     * @var Application[] all auth applications (name => Application)
     */
    protected $applications;
    /**
     * @var Menu[] all auth menus (name => Menu)
     */
    protected $menus;
    /**
     * @var MenuList[] all menulist (appname => Menulist)
     */
    protected $menulist;


    /**
     * Initializes the application component.
     * This method overrides the parent implementation by establishing the database connection.
     */
    public function init()
    {
        parent::init();
        $this->db = Instance::ensure($this->db, Connection::className());
        if ($this->cache !== null) {
            $this->cache = Instance::ensure($this->cache, Cache::className());
        }
    }

    /**
     * @inheritdoc
     */
    public function checkAccess($userId, $permissionName, $params = [])
    {
        $assignments = $this->getAssignments($userId);

        if ($this->hasNoAssignments($assignments)) {
            return false;
        }

        $this->loadFromCache();
        if ($this->items !== null) {
            return $this->checkAccessFromCache($userId, $permissionName, $params, $assignments);
        } else {
            return $this->checkAccessRecursive($userId, $permissionName, $params, $assignments);
        }
    }

    /**
     * Performs access check for the specified user based on the data loaded from cache.
     * This method is internally called by [[checkAccess()]] when [[cache]] is enabled.
     * @param string|int $user the user ID. This should can be either an integer or a string representing
     * the unique identifier of a user. See [[\yii\web\User::id]].
     * @param string $itemName the name of the operation that need access check
     * @param array $params name-value pairs that would be passed to rules associated
     * with the tasks and roles assigned to the user. A param with name 'user' is added to this array,
     * which holds the value of `$userId`.
     * @param Assignment[] $assignments the assignments to the specified user
     * @return bool whether the operations can be performed by the user.
     * @since 2.0.3
     */
    protected function checkAccessFromCache($user, $itemName, $params, $assignments)
    {
        if (!isset($this->items[$itemName])) {
            return false;
        }

        $item = $this->items[$itemName];

        Yii::trace($item instanceof Role ? "Checking role: $itemName" : "Checking permission: $itemName", __METHOD__);

        if (!$this->executeRule($user, $item, $params)) {
            return false;
        }

        if (isset($assignments[$itemName]) || in_array($itemName, $this->defaultRoles)) {
            return true;
        }

        if (!empty($this->parents[$itemName])) {
            foreach ($this->parents[$itemName] as $parent) {
                if ($this->checkAccessFromCache($user, $parent, $params, $assignments)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Performs access check for the specified user.
     * This method is internally called by [[checkAccess()]].
     * @param string|int $user the user ID. This should can be either an integer or a string representing
     * the unique identifier of a user. See [[\yii\web\User::id]].
     * @param string $itemName the name of the operation that need access check
     * @param array $params name-value pairs that would be passed to rules associated
     * with the tasks and roles assigned to the user. A param with name 'user' is added to this array,
     * which holds the value of `$userId`.
     * @param Assignment[] $assignments the assignments to the specified user
     * @return bool whether the operations can be performed by the user.
     */
    protected function checkAccessRecursive($user, $itemName, $params, $assignments)
    {
        if (($item = $this->getItem(['name' => $itemName])) === null) {
            return false;
        }

        Yii::trace($item instanceof Role ? "Checking role: $itemName" : "Checking permission: $itemName", __METHOD__);

        if (!$this->executeRule($user, $item, $params)) {
            return false;
        }

        if (isset($assignments[$itemName]) || in_array($itemName, $this->defaultRoles)) {
            return true;
        }

        $query = new Query;
        $parents = $query->select(['parent'])
            ->from($this->itemChildTable)
            ->where(['child' => $itemName])
            ->column($this->db);
        foreach ($parents as $parent) {
            if ($this->checkAccessRecursive($user, $parent, $params, $assignments)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns a value indicating whether the database supports cascading update and delete.
     * The default implementation will return false for SQLite database and true for all other databases.
     * @return bool whether the database supports cascading update and delete.
     */
    protected function supportsCascadeUpdate()
    {
        return strncmp($this->db->getDriverName(), 'sqlite', 6) !== 0;
    }

    /**
     * @inheritdoc
     */
    protected function getItem($params=[])
    {
        if (empty($params['name'])) {
            return null;
        }

        if (isset($params['name']) && !empty($params['name']) && !empty($this->items[$params['name']])) {
            return $this->items[$params['name']];
        }

        $row = (new Query)->from($this->itemTable)
            ->where($params)
            ->one($this->db);

        if ($row === false) {
            return null;
        }

        return $this->populateItem($row);
    }

    /**
     * 获取指定应用的全部角色或权限
     * @param $type 1:角色 2权限
     * @param $application 指定的应用
     */
    protected function getItems($params=[])
    {
        if (empty($params) && $this->items !== null) {
            return $this->items;
        }
        $query = (new Query)
            ->from($this->itemTable)
            ->where($params);

        $items = [];
        foreach ($query->all($this->db) as $row) {
            $items[$row['name']] = $this->populateItem($row);
        }

        return $items;
    }


    /**
     * @inheritdoc
     */
    protected function addItem($item)
    {
        $time = time();
        if ($item->createdAt === null) {
            $item->createdAt = $time;
        }
        if ($item->updatedAt === null) {
            $item->updatedAt = $time;
        }
        $this->db->createCommand()
            ->insert($this->itemTable, [
                'name' => $item->name,
                'type' => $item->type,
                'description' => $item->description,
                'rule_name' => $item->ruleName,
                'data' => $item->data === null ? null : serialize($item->data),
                'created_at' => $item->createdAt,
                'updated_at' => $item->updatedAt,
                'app_name' => $item->appName,
            ])->execute();

        $this->invalidateCache(['item']);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function removeItem($item)
    {
        if (!$this->supportsCascadeUpdate()) {
            $this->db->createCommand()
                ->delete($this->itemChildTable, ['or', '[[parent]]=:name', '[[child]]=:name'], [':name' => $item->name])
                ->execute();
            $this->db->createCommand()
                ->delete($this->assignmentTable, ['item_name' => $item->name])
                ->execute();
        }

        $this->db->createCommand()
            ->delete($this->itemTable, ['name' => $item->name])
            ->execute();

        $this->invalidateCache(['item','parent']);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function updateItem($name, $item)
    {
        if ($item->name !== $name && !$this->supportsCascadeUpdate()) {
            $this->db->createCommand()
                ->update($this->itemChildTable, ['parent' => $item->name], ['parent' => $name])
                ->execute();
            $this->db->createCommand()
                ->update($this->itemChildTable, ['child' => $item->name], ['child' => $name])
                ->execute();
            $this->db->createCommand()
                ->update($this->assignmentTable, ['item_name' => $item->name], ['item_name' => $name])
                ->execute();
        }

        $item->updatedAt = time();

        $this->db->createCommand()
            ->update($this->itemTable, [
                'name' => $item->name,
                'description' => $item->description,
                'rule_name' => $item->ruleName,
                'data' => $item->data === null ? null : serialize($item->data),
                'updated_at' => $item->updatedAt,
                'app_name' => $item->appName,
            ], [
                'name' => $name,
            ])->execute();

        $this->invalidateCache(['item','parent']);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getRule($name)
    {
        if ($this->rules !== null) {
            return isset($this->rules[$name]) ? $this->rules[$name] : null;
        }

        $row = (new Query)->select(['data'])
            ->from($this->ruleTable)
            ->where(['name' => $name])
            ->one($this->db);
        if ($row === false) {
            return null;
        }
        $data = $row['data'];
        if (is_resource($data)) {
            $data = stream_get_contents($data);
        }
        return unserialize($data);

    }

    /**
     * @inheritdoc
     */
    public function getRules()
    {
        if ($this->rules !== null) {
            return $this->rules;
        }

        $query = (new Query)->from($this->ruleTable);

        $rules = [];
        foreach ($query->all($this->db) as $row) {
            $data = $row['data'];
            if (is_resource($data)) {
               $data = stream_get_contents($data);
            }
            $rules[$row['name']] = unserialize($data);
        }

        return $rules;
    }

    /**
     * @inheritdoc
     */
    protected function addRule($rule)
    {
        $time = time();
        if ($rule->createdAt === null) {
            $rule->createdAt = $time;
        }
        if ($rule->updatedAt === null) {
            $rule->updatedAt = $time;
        }
        $this->db->createCommand()
            ->insert($this->ruleTable, [
                'name' => $rule->name,
                'data' => serialize($rule),
                'created_at' => $rule->createdAt,
                'updated_at' => $rule->updatedAt,
            ])->execute();

        $this->invalidateCache(['rule']);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function updateRule($name, $rule)
    {
        if ($rule->name !== $name && !$this->supportsCascadeUpdate()) {
            $this->db->createCommand()
                ->update($this->itemTable, ['rule_name' => $rule->name], ['rule_name' => $name])
                ->execute();
        }

        $rule->updatedAt = time();

        $this->db->createCommand()
            ->update($this->ruleTable, [
                'name' => $rule->name,
                'data' => serialize($rule),
                'updated_at' => $rule->updatedAt,
            ], [
                'name' => $name,
            ])->execute();

        $this->invalidateCache(['rule']);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function removeRule($rule)
    {
        if (!$this->supportsCascadeUpdate()) {
            $this->db->createCommand()
                ->update($this->itemTable, ['rule_name' => null], ['rule_name' => $rule->name])
                ->execute();
        }

        $this->db->createCommand()
            ->delete($this->ruleTable, ['name' => $rule->name])
            ->execute();

        $this->invalidateCache(['rule']);

        return true;
    }

    /**
     * 填充一个从数据库中获取到的认证权限
     * @param array $row the data from the auth item table
     * @return Item the populated auth item instance (either Role or Permission)
     */
    protected function populateItem($row)
    {
        $class = $row['type'] == Item::TYPE_PERMISSION ? Permission::className() : Role::className();

        if (!isset($row['data']) || ($data = @unserialize($row['data'])) === false) {
            $data = null;
        }

        return new $class([
            'name' => $row['name'],
            'type' => $row['type'],
            'description' => $row['description'],
            'ruleName' => $row['rule_name'],
            'data' => $data,
            'createdAt' => $row['created_at'],
            'updatedAt' => $row['updated_at'],
            'appName' => $row['app_name'],
        ]);
    }

    /**
     * 填充一个从数据库中获取到的应用
     * @param array $row the data from the auth item table
     * @return Application the populated auth application instance 
     */
    protected function populateApplication($row)
    {
        return new Application([
            'name' => $row['name'],
            'createdAt' => $row['created_at'],
            'updatedAt' => $row['updated_at'],
            'userId' => $row['user_id'],
        ]);
    }

    /**
     * 填充一个从数据库中获取到的菜单
     * @param array $row 菜单表的一条数据
     * @return Menu the populated auth menu instance 
     */
    protected function populateMenu($row)
    {
        $class = $row['type'] == Menu::TYPE_NAVIGATION ? Navigation::className() : Operate::className();

        return new $class([
            'name' => $row['name'],
            'type' => $row['type'],
            'style' => $row['style'],
            'url' => $row['url'],
            'pic' => $row['pic'],
            'description' => $row['description'],
            'sort' => $row['sort'],
            'createdAt' => $row['created_at'],
            'updatedAt' => $row['updated_at'],
            'appName' => $row['app_name'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getRolesByUser($userId)
    {
        if (!isset($userId) || $userId === '') {
            return [];
        }

        $query = (new Query)->select('b.*')
            ->from(['a' => $this->assignmentTable, 'b' => $this->itemTable])
            ->where('{{a}}.[[item_name]]={{b}}.[[name]]')
            ->andWhere(['a.user_id' => (string) $userId])
            ->andWhere(['b.type' => Item::TYPE_ROLE]);

        $roles = [];
        foreach ($query->all($this->db) as $row) {
            $roles[$row['name']] = $this->populateItem($row);
        }
        return $roles;
    }

    /**
     * @inheritdoc
     */
    public function getChildRoles($roleName)
    {
        $role = $this->getRole($roleName);

        if (is_null($role)) {
            throw new InvalidParamException("Role \"$roleName\" not found.");
        }

        $result = [];
        $this->getChildrenRecursive($roleName, $this->getChildrenList(), $result);

        $roles = [$roleName => $role];

        $roles += array_filter($this->getRoles(), function (Role $roleItem) use ($result) {
            return array_key_exists($roleItem->name, $result);
        });

        return $roles;
    }

    public function getChildMenus($menuName)
    {
        $menu = $this->getMenu($menuName);

        if (is_null($menu)) {
            throw new InvalidParamException("Menu \"$menuName\" not found.");
        }

        $result = [];
        $this->getChildrenRecursive($menuName, $this->getChildrenMenuList(), $result);

        $menus = [$menuName => $menu];

        $roles += array_filter($this->getRoles(), function (Role $roleItem) use ($result) {
            return array_key_exists($roleItem->name, $result);
        });

        return $roles;
    }

    /**
     * @inheritdoc
     */
    public function getPermissionsByRole($roleName)
    {
        $childrenList = $this->getChildrenList();
        $result = [];
        $this->getChildrenRecursive($roleName, $childrenList, $result);
        if (empty($result)) {
            return [];
        }
        $query = (new Query)->from($this->itemTable)->where([
            'type' => Item::TYPE_PERMISSION,
            'name' => array_keys($result),
        ]);
        $permissions = [];
        foreach ($query->all($this->db) as $row) {
            $permissions[$row['name']] = $this->populateItem($row);
        }
        return $permissions;
    }

    /**
     * @inheritdoc
     */
    public function getPermissionsByUser($userId)
    {
        if (empty($userId)) {
            return [];
        }

        $directPermission = $this->getDirectPermissionsByUser($userId);
        $inheritedPermission = $this->getInheritedPermissionsByUser($userId);

        return array_merge($directPermission, $inheritedPermission);
    }

    /**
     * Returns all permissions that are directly assigned to user.
     * @param string|int $userId the user ID (see [[\yii\web\User::id]])
     * @return Permission[] all direct permissions that the user has. The array is indexed by the permission names.
     * @since 2.0.7
     */
    protected function getDirectPermissionsByUser($userId)
    {
        $query = (new Query)->select('b.*')
            ->from(['a' => $this->assignmentTable, 'b' => $this->itemTable])
            ->where('{{a}}.[[item_name]]={{b}}.[[name]]')
            ->andWhere(['a.user_id' => (string) $userId])
            ->andWhere(['b.type' => Item::TYPE_PERMISSION]);

        $permissions = [];
        foreach ($query->all($this->db) as $row) {
            $permissions[$row['name']] = $this->populateItem($row);
        }
        return $permissions;
    }

    /**
     * Returns all permissions that the user inherits from the roles assigned to him.
     * @param string|int $userId the user ID (see [[\yii\web\User::id]])
     * @return Permission[] all inherited permissions that the user has. The array is indexed by the permission names.
     * @since 2.0.7
     */
    protected function getInheritedPermissionsByUser($userId)
    {
        $query = (new Query)->select('item_name')
            ->from($this->assignmentTable)
            ->where(['user_id' => (string) $userId]);

        $childrenList = $this->getChildrenList();
        $result = [];
        foreach ($query->column($this->db) as $roleName) {
            $this->getChildrenRecursive($roleName, $childrenList, $result);
        }

        if (empty($result)) {
            return [];
        }

        $query = (new Query)->from($this->itemTable)->where([
            'type' => Item::TYPE_PERMISSION,
            'name' => array_keys($result),
        ]);
        $permissions = [];
        foreach ($query->all($this->db) as $row) {
            $permissions[$row['name']] = $this->populateItem($row);
        }
        return $permissions;
    }

    /**
     * Returns the children for every parent.
     * @return array the children list. Each array key is a parent item name,
     * and the corresponding array value is a list of child item names.
     */
    protected function getChildrenList()
    {
        $query = (new Query)->from($this->itemChildTable);
        $parents = [];
        foreach ($query->all($this->db) as $row) {
            $parents[$row['parent']][] = $row['child'];
        }
        return $parents;
    }

    /**
     * Returns the children for every parent.
     * @return array the children list. Each array key is a parent item name,
     * and the corresponding array value is a list of child item names.
     */
    protected function getChildrenMenuList()
    {
        $query = (new Query)->from($this->menuChildTable);
        $parents = [];
        foreach ($query->all($this->db) as $row) {
            $parents[$row['parent']][] = $row['child'];
        }
        return $parents;
    }

    /**
     * Recursively finds all children and grand children of the specified item.
     * @param string $name the name of the item whose children are to be looked for.
     * @param array $childrenList the child list built via [[getChildrenList()]]
     * @param array $result the children and grand children (in array keys)
     */
    protected function getChildrenRecursive($name, $childrenList, &$result)
    {
        if (isset($childrenList[$name])) {
            foreach ($childrenList[$name] as $child) {
                $result[$child] = true;
                $this->getChildrenRecursive($child, $childrenList, $result);
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getAssignment($roleName, $userId)
    {
        if (empty($userId)) {
            return null;
        }

        $row = (new Query)->from($this->assignmentTable)
            ->where(['user_id' => (string) $userId, 'item_name' => $roleName])
            ->one($this->db);

        if ($row === false) {
            return null;
        }

        return new Assignment([
            'userId' => $row['user_id'],
            'roleName' => $row['item_name'],
            'createdAt' => $row['created_at'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getAssignments($userId)
    {
        if (empty($userId)) {
            return [];
        }

        $query = (new Query)
            ->from($this->assignmentTable)
            ->where(['user_id' => (string) $userId]);

        $assignments = [];
        foreach ($query->all($this->db) as $row) {
            $assignments[$row['item_name']] = new Assignment([
                'userId' => $row['user_id'],
                'roleName' => $row['item_name'],
                'createdAt' => $row['created_at'],
            ]);
        }

        return $assignments;
    }

    /**
     * @inheritdoc
     * @since 2.0.8
     */
    public function canAddChild($parent, $child)
    {
        return !$this->detectLoop($parent, $child);
    }

    public function canAddMenuChild($parent, $child)
    {
        return !$this->detectMenuLoop($parent, $child);
    }

    /**
     * @inheritdoc
     */
    public function addChild($parent, $child)
    {
        if ($parent->name === $child->name) {
            throw new InvalidParamException("Cannot add '{$parent->name}' as a child of itself.");
        }

        if ($parent instanceof Permission && $child instanceof Role) {
            throw new InvalidParamException('Cannot add a role as a child of a permission.');
        }

        //禁掉权限继承，不让权限包含权限
        if ($parent instanceof Permission && $child instanceof Permission) {
            throw new InvalidParamException('Cannot add a permission as a child of a permission.');
        }

        if ($this->detectLoop($parent, $child)) {
            throw new InvalidCallException("Cannot add '{$child->name}' as a child of '{$parent->name}'. A loop has been detected.");
        }

        $this->db->createCommand()
            ->insert($this->itemChildTable, ['parent' => $parent->name, 'child' => $child->name])
            ->execute();

        $this->invalidateCache(['parent']);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function addMenuChild($parent, $child)
    {
        $a=microtime();
        if(!$parent || !$child){
            throw new InvalidParamException('parent or child can not is null.');
        }

        if ($parent->name === $child->name) {
            throw new InvalidParamException("Cannot add '{$parent->name}' as a child of itself.");
        }

        if ($parent instanceof Operate && $child instanceof Navigation) {
            throw new InvalidParamException('Cannot add a operate as a child of a navigation.');
        }

        if ($this->detectMenuLoop($parent, $child)) {
            throw new InvalidCallException("Cannot add '{$child->name}' as a child of '{$parent->name}'. A loop has been detected.");
        }

        $this->db->createCommand()
            ->insert($this->menuChildTable, ['parent' => $parent->name, 'child' => $child->name])
            ->execute();
        //当菜单添加子菜单后，就变更为下拉菜单
        if($child instanceof Navigation){
            $parent->style = Menu::TYPE_MENU_DOWN;
            $this->updateMenu($parent->name,$parent);
        }
        $this->invalidateCache(['menulist']);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function removeChild($parent, $child)
    {
        $result = $this->db->createCommand()
            ->delete($this->itemChildTable, ['parent' => $parent->name, 'child' => $child->name])
            ->execute() > 0;

        $this->invalidateCache(['parent']);

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function removeMenuChild($parent, $child)
    {
        $result = $this->db->createCommand()
            ->delete($this->menuChildTable, ['parent' => $parent->name, 'child' => $child->name])
            ->execute() > 0;
        /**
         * @todo:当菜单不再含有子菜单的时候，需要将菜单类型改为跳转菜单
         */
        $this->invalidateCache(['menulist']);

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function removeChildren($parent)
    {
        $result = $this->db->createCommand()
            ->delete($this->itemChildTable, ['parent' => $parent->name])
            ->execute() > 0;

        $this->invalidateCache(['parent']);

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function removeMenuChildren($parent)
    {
        $result = $this->db->createCommand()
            ->delete($this->menuChildTable, ['parent' => $parent->name])
            ->execute() > 0;

        $this->invalidateCache(['menulist']);

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function hasChild($parent, $child)
    {
        return (new Query)
            ->from($this->itemChildTable)
            ->where(['parent' => $parent->name, 'child' => $child->name])
            ->one($this->db) !== false;
    }

    /**
     * @inheritdoc
     */
    public function hasMenuChild($parent, $child)
    {
        return (new Query)
            ->from($this->menuChildTable)
            ->where(['parent' => $parent->name, 'child' => $child->name])
            ->one($this->db) !== false;
    }

    /**
     * @inheritdoc
     */
    public function getChildren($name)
    {
        $query = (new Query)
            ->select(['name', 'type', 'description', 'rule_name', 'data', 'created_at', 'updated_at', 'app_name'])
            ->from([$this->itemTable, $this->itemChildTable])
            ->where(['parent' => $name, 'name' => new Expression('[[child]]')]);

        $children = [];
        foreach ($query->all($this->db) as $row) {
            $children[$row['name']] = $this->populateItem($row);
        }

        return $children;
    }

    /**
     * @inheritdoc
     */
    public function getMenuChildren($name)
    {
        $query = (new Query)
            ->select(['name', 'type', 'style', 'url', 'pic', 'description', 'sort', 'created_at', 'updated_at', 'app_name'])
            ->from([$this->menuTable, $this->menuChildTable])
            ->where(['parent' => $name, 'name' => new Expression('[[child]]')]);

        $children = [];
        foreach ($query->all($this->db) as $row) {
            $children[$row['name']] = $this->populateMenu($row);
        }

        return $children;
    }

    /**
     * Checks whether there is a loop in the authorization item hierarchy.
     * @param Item $parent the parent item
     * @param Item $child the child item to be added to the hierarchy
     * @return bool whether a loop exists
     */
    protected function detectLoop($parent, $child)
    {
        if ($child->name === $parent->name) {
            return true;
        }
        foreach ($this->getChildren($child->name) as $grandchild) {
            if ($this->detectLoop($parent, $grandchild)) {
                return true;
            }
        }
        return false;
    }

    protected function detectMenuLoop($parent, $child)
    {
        if ($child->name === $parent->name) {
            return true;
        }
        foreach ($this->getMenuChildren($child->name) as $grandchild) {
            if ($this->detectMenuLoop($parent, $grandchild)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function assign($role, $userId)
    {
        $assignment = new Assignment([
            'userId' => $userId,
            'roleName' => $role->name,
            'createdAt' => time(),
        ]);

        $this->db->createCommand()
            ->insert($this->assignmentTable, [
                'user_id' => $assignment->userId,
                'item_name' => $assignment->roleName,
                'created_at' => $assignment->createdAt,
            ])->execute();

        return $assignment;
    }

    /**
     * @inheritdoc
     */
    public function revoke($role, $userId)
    {
        if (empty($userId)) {
            return false;
        }

        return $this->db->createCommand()
            ->delete($this->assignmentTable, ['user_id' => (string) $userId, 'item_name' => $role->name])
            ->execute() > 0;
    }

    /**
     * @inheritdoc
     */
    public function revokeAll($userId)
    {
        if (empty($userId)) {
            return false;
        }

        return $this->db->createCommand()
            ->delete($this->assignmentTable, ['user_id' => (string) $userId])
            ->execute() > 0;
    }

    /**
     * @inheritdoc
     */
    public function removeAll()
    {
        $this->removeAllAssignments();
        $this->db->createCommand()->delete($this->itemChildTable)->execute();
        $this->db->createCommand()->delete($this->itemTable)->execute();
        $this->db->createCommand()->delete($this->ruleTable)->execute();
        $this->invalidateCache(['itme','rule','parent']);
    }

    /**
     * @inheritdoc
     */
    public function removeAllPermissions()
    {
        $this->removeAllItems(Item::TYPE_PERMISSION);
    }

    /**
     * @inheritdoc
     */
    public function removeAllRoles()
    {
        $this->removeAllItems(Item::TYPE_ROLE);
    }

    /**
     * Removes all auth items of the specified type.
     * @param int $type the auth item type (either Item::TYPE_PERMISSION or Item::TYPE_ROLE)
     */
    protected function removeAllItems($type)
    {
        if (!$this->supportsCascadeUpdate()) {
            $names = (new Query)
                ->select(['name'])
                ->from($this->itemTable)
                ->where(['type' => $type])
                ->column($this->db);
            if (empty($names)) {
                return;
            }
            $key = $type == Item::TYPE_PERMISSION ? 'child' : 'parent';
            $this->db->createCommand()
                ->delete($this->itemChildTable, [$key => $names])
                ->execute();
            $this->db->createCommand()
                ->delete($this->assignmentTable, ['item_name' => $names])
                ->execute();
        }
        $this->db->createCommand()
            ->delete($this->itemTable, ['type' => $type])
            ->execute();

        $this->invalidateCache(['item']);
    }

    /**
     * @inheritdoc
     */
    public function removeAllRules()
    {
        if (!$this->supportsCascadeUpdate()) {
            $this->db->createCommand()
                ->update($this->itemTable, ['rule_name' => null])
                ->execute();
        }

        $this->db->createCommand()->delete($this->ruleTable)->execute();

        $this->invalidateCache(['item','rule']);
    }

    /**
     * @inheritdoc
     */
    public function removeAllAssignments()
    {
        $this->db->createCommand()->delete($this->assignmentTable)->execute();
    }

    public function invalidateCache($params=[])
    {
        if ($this->cache !== null) {
            if(!empty($params)){
                $data=$this->cache->get($this->cacheKey);
                foreach ($params as $key => $value) {
                    switch ($value) {
                        case 'item':
                            $this->items = null;
                            $this->parents = null;
                            if(isset($data['items']) || isset($data['parents'])){
                                if(isset($data['items'])){
                                    unset($data['items']);
                                }
                                if(isset($data['parents'])){
                                    unset($data['parents']);
                                }
                            }
                            break;
                        case 'rule':
                            $this->rules = null;
                            if(isset($data['rules'])){
                                unset($data['rules']);
                            }
                            break;
                        case 'parent':
                            $this->parents = null;
                            if(isset($data['parents'])){
                                unset($data['parents']);
                            }
                            break;
                        case 'application':
                            $this->applications=null;
                            if(isset($data['applications'])){
                                unset($data['applications']);
                            }
                            break;
                        case 'menu':
                            $this->menus=null;
                            if(isset($data['menus'])){
                                unset($data['menus']);
                            }
                            break;
                        case 'menulist':
                            $this->menulist=null;
                            if(isset($data['menulist'])){
                                unset($data['menulist']);
                            }
                            break;
                        default:
                            break;
                    }
                }
                $this->cache->set($this->cacheKey, $data);
            }else{
                $this->cache->delete($this->cacheKey);
                $this->items = null;
                $this->rules = null;
                $this->parents = null;
                $this->applications=null;
                $this->menus=null;
                $this->menulist=null;
            }
        }
    }

    public function loadFromCache()
    {
        if (($this->items !== null && $this->menus !== null && $this->applications !== null && $this->rules !== null && $this->parents !== null && $this->menulist !== null) || !$this->cache instanceof Cache) {
            return;
        }

        $data = $this->cache->get($this->cacheKey);
        
        if($this->items === null){
            $this->items = isset($data['items']) ? $data['items'] : $this->getItems();
        }
        
        if($this->rules === null){
            $this->rules = isset($data['rules']) ? $data['rules'] : $this->getRules();
        }
        
        if($this->parents === null){
            if(isset($data['parents'])){
                $this->parents = $data['parents'];
            }else{
                $query = (new Query)->from($this->itemChildTable);
                $this->parents = [];
                foreach ($query->all($this->db) as $row) {
                    if (isset($this->items[$row['child']])) {
                        $this->parents[$row['child']][] = $row['parent'];
                    }
                }
            }
        }

        if($this->applications === null){
            $this->applications = isset($data['applications']) ? $data['applications'] : $this->getApplications();
        }

        if($this->menus === null){
            $this->menus = isset($data['menus']) ? $data['menus'] : $this->getMenus();
        }

        if($this->menulist === null){
            if(isset($data['menulist'])){
                $this->menulist = $data['menulist'];
            }else{
                $this->menulist = [];
                foreach ($this->getApplications() as $row) {
                    $this->menulist[$row->name]=$this->getMenusByApplicationName($row->name);
                }
            }
        }

        $newdata=[
            'items' => $this->items, 
            'rules' => $this->rules, 
            'parents' => $this->parents, 
            'applications' => $this->applications, 
            'menus' => $this->menus, 
            'menulist' => $this->menulist
        ];

        $this->cache->set($this->cacheKey, $newdata);
    }

    /**
     * Returns all role assignment information for the specified role.
     * @param string $roleName
     * @return Assignment[] the assignments. An empty array will be
     * returned if role is not assigned to any user.
     * @since 2.0.7
     */
    public function getUserIdsByRole($roleName)
    {
        if (empty($roleName)) {
            return [];
        }

        return (new Query)->select('[[user_id]]')
            ->from($this->assignmentTable)
            ->where(['item_name' => $roleName])->column($this->db);
    }

    /**
     * 添加一个应用
     * $application 新的应用对象
     */
    protected function addApplication($application)
    {
        $time = time();
        if ($application->createdAt === null) {
            $application->createdAt = $time;
        }
        if ($application->updatedAt === null) {
            $application->updatedAt = $time;
        }
        $this->db->createCommand()
            ->insert($this->applicationTable, [
                'name' => $application->name,
                'created_at' => $application->createdAt,
                'updated_at' => $application->updatedAt,
                'user_id' => $application->userId,
            ])->execute();

        $this->invalidateCache(['application']);

        return true;
    }

    /**
     * 更新一个应用
     * @param $name 要更新的对象名
     * @param $application 应用对象
     */
    protected function updateApplication($name, $application)
    {
        if ($application->name !== $name && !$this->supportsCascadeUpdate()) {
            $this->db->createCommand()
                ->update($this->itemTable, ['app_name' => $application->name], ['app_name' => $name])
                ->execute();
            $this->db->createCommand()
                ->update($this->menuTable, ['app_name' => $application->name], ['app_name' => $name])
                ->execute();
        }

        $application->updatedAt = time();

        $this->db->createCommand()
            ->update($this->applicationTable, [
                'name' => $application->name,
                'updated_at' => $application->updatedAt,
                'user_id' => $application->userId,
            ], [
                'name' => $name,
            ])->execute();

        $this->invalidateCache();//应用变更，可能引起其他表变更

        return true;
    }

    /**
     * 删除一个应用
     * @param $application 要删除的应用对象
     */
    protected function removeApplication($application)
    {
        if (!$this->supportsCascadeUpdate()) {
            $this->db->createCommand()
                ->delete($this->itemTable, ['app_name' => $application->name])
                ->execute();
            $this->db->createCommand()
                ->delete($this->menuTable, ['app_name' => $application->name])
                ->execute();
        }

        $this->db->createCommand()
            ->delete($this->applicationTable, ['name' => $application->name])
            ->execute();

        $this->invalidateCache();//应用删除，可能引起其他表删除

        return true;
    }

    /**
     * 获取一个应用
     * @param $params array 参数
     */
    protected function getApplication($params=[])
    {
        if (empty($params)) {
            return null;
        }

        if (isset($params['name']) && !empty($params['name']) && !empty($this->applications[$params['name']])) {
            return $this->applications[$params['name']];
        }

        $row = (new Query)->from($this->applicationTable)
            ->where($params)
            ->one($this->db);

        if ($row === false) {
            return null;
        }

        return $this->populateApplication($row);
    }

    /**
     * 获取多个应用
     */
    protected function getApplications($params=[])
    {
        if (empty($params) && $this->applications !== null) {
            return $this->applications;
        }
        $query = (new Query)
            ->from($this->applicationTable)
            ->where($params);

        $applications = [];
        foreach ($query->all($this->db) as $row) {
            $applications[$row['name']] = $this->populateApplication($row);
        }

        return $applications;
    }


    /**
     * 添加一个菜单
     * $menu 要添加的菜单对象
     */
    protected function addMenu($menu)
    {
        $time = time();
        if ($menu->createdAt === null) {
            $menu->createdAt = $time;
        }
        if ($menu->updatedAt === null) {
            $menu->updatedAt = $time;
        }
        $this->db->createCommand()
            ->insert($this->menuTable, [
                'name' => $menu->name,
                'type' => $menu->type,
                'style' => $menu->style,
                'url' => $menu->url,
                'pic' => $menu->pic,
                'description' => $menu->description,
                'sort' => $menu->sort,
                'created_at' => $menu->createdAt,
                'updated_at' => $menu->updatedAt,
                'app_name' => $menu->appName,
            ])->execute();

        $this->invalidateCache(['menu','menulist']);

        return true;
    }

    /**
     * 更新一个菜单
     * @param $name 要更新的菜单名
     * @param $menu 菜单对象
     */
    protected function updateMenu($name, $menu)
    {
        if ($menu->name !== $name && !$this->supportsCascadeUpdate()) {
            $this->db->createCommand()
                ->update($this->menuChildTable, ['parent' => $menu->name], ['parent' => $name])
                ->execute();
            $this->db->createCommand()
                ->update($this->menuChildTable, ['child' => $menu->name], ['child' => $name])
                ->execute();
        }
        $menu->updatedAt = time();

        $this->db->createCommand()
            ->update($this->menuTable, [
                'name' => $menu->name,
                'type' => $menu->type,
                'style' => $menu->style,
                'url' => $menu->url,
                'pic' => $menu->pic,
                'description' => $menu->description,
                'sort' => $menu->sort,
                'updated_at' => $menu->updatedAt,
                'app_name' => $menu->appName,
            ], [
                'name' => $name,
            ])->execute();

        $this->invalidateCache(['menu','menulist']);

        return true;
    }

    /**
     * 删除一个菜单
     * @param $menu 要删除菜单对象
     */
    protected function removeMenu($menu)
    {
        if (!$this->supportsCascadeUpdate()) {
            $this->db->createCommand()
                ->delete($this->menuChildTable, ['or', '[[parent]]=:name', '[[child]]=:name'], [':name' => $menu->name])
                ->execute();
        }
        $this->db->createCommand()
            ->delete($this->menuTable, ['name' => $menu->name])
            ->execute();

        $this->invalidateCache(['menu','menulist']);

        return true;
    }

    

    /**
     * @inheritdoc
     */
    protected function getMenu($params=[])
    {
        if (empty($params['name'])) {
            return null;
        }

        if (isset($params['name']) && !empty($params['name']) && !empty($this->menus[$params['name']])) {
            return $this->menus[$params['name']];
        }

        $row = (new Query)->from($this->menuTable)
            ->where($params)
            ->orderBy(['sort' => SORT_DESC])
            ->one($this->db);

        if ($row === false) {
            return null;
        }

        return $this->populateMenu($row);
    }


    /**
     * @inheritdoc
     */
    protected function getMenus($params=[])
    {
        if (empty($params) && $this->menus !== null) {
            return $this->menus;
        }

        $query = (new Query)
            ->from($this->menuTable)
            ->where($params)
            ->orderBy(['sort' => SORT_DESC]);

        $menus = [];
        foreach ($query->all($this->db) as $row) {
            $menus[$row['name']] = $this->populateMenu($row);
        }

        return $menus;
    }

    /**
     * 递归获取菜单下的子菜单和操作
     */
    /**
     * @inheritdoc
     */
    protected function getChildMenusRecursive($menu,$childrenList,&$result)
    {
        /**
         *@todo::递归查找对性能损耗太大，后期改为从缓存获取
         */
        if($menuchildrens=isset($childrenList[$menu->name]) ? $childrenList[$menu->name] : []){
            foreach($menuchildrens as $row){
                $child=$this->getMenu(['name' => $row]);
                if(!$child){
                    continue;
                }
                $result[$menu->name]['childlist'][$row]['self']=$child;
                if($child->type == Menu::TYPE_NAVIGATION){
                    $this->getChildMenusRecursive($child,$childrenList,$result[$menu->name]['childlist']);
                }
            }
        }
    }

    /**
     * 获取指定应用下的全部菜单和操作
     */
    protected function getMenusByApplicationName($appName)
    {
        // if(isset($this->menulist[$appName])){
        //     return $this->menulist[$appName];
        // }
        $menus=$this->getMenus([
            'app_name' => $appName,
            'type' => Menu::TYPE_NAVIGATION,
        ]);
        //获取子菜单列表（包括操作和菜单）
        $allchildrenlist=$this->getAllChildrenList();

        $menulist=[];
        foreach ($menus as $row) {
            if(!in_array($row->name, $allchildrenlist)){
                $menulist[$row->name]['self']=$row;
            }
        }
        foreach ($menulist as $row) {
            $this->getChildMenusRecursive($row['self'],$this->getChildrenMenuList(),$menulist);
        }
        return $menulist;
    }

    protected function getAllChildrenList(){
        $query = (new Query)
            ->select('child')
            ->from($this->menuChildTable);

        $menulist = [];
        foreach($query->all($this->db) as $row){
            $menulist[]=$row['child'];
        }
        return $menulist;
    }
}
