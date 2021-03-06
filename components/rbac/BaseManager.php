<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\components\rbac;

use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use app\components\rbac\ManagerInterface;

/**
 * BaseManager is a base class implementing [[ManagerInterface]] for RBAC management.
 *
 * For more details and usage information on DbManager, see the [guide article on security authorization](guide:security-authorization).
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
abstract class BaseManager extends Component implements ManagerInterface
{
    /**
     * @var array a list of role names that are assigned to every user automatically without calling [[assign()]].
     */
    public $defaultRoles = [];

    /**
     * Returns the named auth item.
     * @param string $name the auth item name.
     * @return Item the auth item corresponding to the specified name. Null is returned if no such item.
     */
    abstract protected function getItem($params);

    /**
     * Returns the items of the specified type.
     * @param int $type the auth item type (either [[Item::TYPE_ROLE]] or [[Item::TYPE_PERMISSION]]
     * @param string $application 应用
     * @return Item[] the auth items of the specified type.
     */
    abstract protected function getItems($params);

    /**
     * Adds an auth item to the RBAC system.
     * @param Item $item the item to add
     * @return bool whether the auth item is successfully added to the system
     * @throws \Exception if data validation or saving fails (such as the name of the role or permission is not unique)
     */
    abstract protected function addItem($item);

    /**
     * Removes an auth item from the RBAC system.
     * @param Item $item the item to remove
     * @return bool whether the role or permission is successfully removed
     * @throws \Exception if data validation or saving fails (such as the name of the role or permission is not unique)
     */
    abstract protected function removeItem($item);

     /**
     * Updates an auth item in the RBAC system.
     * @param string $name the name of the item being updated
     * @param Item $item the updated item
     * @return bool whether the auth item is successfully updated
     * @throws \Exception if data validation or saving fails (such as the name of the role or permission is not unique)
     */
    abstract protected function updateItem($name, $item);

    /**
     * Adds a rule to the RBAC system.
     * @param Rule $rule the rule to add
     * @return bool whether the rule is successfully added to the system
     * @throws \Exception if data validation or saving fails (such as the name of the rule is not unique)
     */
    abstract protected function addRule($rule);

    /**
     * Removes a rule from the RBAC system.
     * @param Rule $rule the rule to remove
     * @return bool whether the rule is successfully removed
     * @throws \Exception if data validation or saving fails (such as the name of the rule is not unique)
     */
    abstract protected function removeRule($rule);

    /**
     * Updates a rule to the RBAC system.
     * @param string $name the name of the rule being updated
     * @param Rule $rule the updated rule
     * @return bool whether the rule is successfully updated
     * @throws \Exception if data validation or saving fails (such as the name of the rule is not unique)
     */
    abstract protected function updateRule($name, $rule);

    /**
     * 根据条件返回一个应用
     * @param string $params 检索的条件.
     * @return 返回一个指定条件的应用，如果没有这样的应用就返回null.
     */
    abstract protected function getApplication($params);

    /**
     * 根据条件返回多个应用
     * @param string $params 检索的条件.
     * @return 返回全部指定条件的应用，如果没有这样的应用就返回null.
     */
    abstract protected function getApplications($params);
    /**
     * 根据应用获取该应用的全部菜单和操作
     * @param string $params 检索的条件.
     * @return 返回全部指定条件的菜单列表，如果没有这样的菜单列表就返回空数组.
     */
    abstract protected function getMenusByApplicationName($params);

    /**
     * 返回一个导航(操作）
     * @param array $params 检索的条件.
     * @return 返回一个指定条件的导航(操作)，如果没有这样的导航(操作)就返回null.
     */
    abstract protected function getMenu($params);

    /**
     * 根据条件返回全部导航(操作)
     * @param array $params 检索的条件.
     * @return 返回全部指定条件的导航(操作)，如果没有这样的导航(操作)就返回null.
     */
    abstract protected function getMenus($params);
    /**
     * 添加一个导航(操作)
     * @param $menu 要添加的菜单对象.
     * @return 返回全部指定条件的导航(操作)，如果没有这样的导航(操作)就返回null.
     */
    abstract protected function addMenu($menu);
    /**
     * 移除一个导航(操作)
     * @param $menu 要删除的菜单对象.
     * @return 返回全部指定条件的导航(操作)，如果没有这样的导航(操作)就返回null.
     */
    abstract protected function removeMenu($menu);
    /**
     * 更新一个导航(操作)
     * @param $name 要更新的导航(操作名).
     * @param $menu 填充的菜单对象.
     * @return 返回全部指定条件的导航(操作)，如果没有这样的导航(操作)就返回null.
     */
    abstract protected function updateMenu($name, $menu);

    /**
     * Returns the  applications.
     * @return Application[] the applications.
     */
    public function getApplicationAll(){
        return $this->getApplications([]);
    }

    /**
     * Returns the application of the specified userId.
     * @param int $userId the user id
     * @return Application[] the auth item of the specified user idd.
     */
    public function getApplicationsByUser($userId){
        return $this->getApplications(['user_id' => $userId]);
    }

    /**
     * Returns the named application.
     * @param string $name the application name
     * @return Application the auth items of the specified type.
     */
    public function getApplicationByName($name){
        $application = $this->getApplication(['name' => $name]);
        return $application instanceof Application ? $application : null;
    }

    /**
     * @inheritdoc
     */
    public function getRole($name)
    {
        $item = $this->getItem(['name' => $name]);
        return $item instanceof Item && $item->type == Item::TYPE_ROLE ? $item : null;
    }

    /**
     * @inheritdoc
     */
    public function getPermission($name)
    {
        $item = $this->getItem(['name' => $name]);
        return $item instanceof Item && $item->type == Item::TYPE_PERMISSION ? $item : null;
    }

    /**
     * @inheritdoc
     */
    public function getRoles($application)
    {
        return $this->getItems(['type' => Item::TYPE_ROLE,'app_name' => $application->appName]);
    }

    /**
     * @inheritdoc
     */
    public function getPermissions($application)
    {
        return $this->getItems(['type' => Item::TYPE_PERMISSION,'app_name' => $application->appName]);
    }

    /**
     * @inheritdoc
     */
    public function getNavigation($name)
    {
        $menu = $this->getMenu(['name' => $name]);
        return $menu instanceof Menu && $menu->type == Menu::TYPE_NAVIGATION ? $menu : null;
    }

    /**
     * @inheritdoc
     */
    public function getOperate($name)
    {
        $menu = $this->getMenu(['name' => $name]);
        return $menu instanceof Menu && $menu->type == Menu::TYPE_OPERATE ? $menu : null;
    }

    /**
     * @inheritdoc
     */
    public function getMenuList($application)
    {
        return $this->getMenusByApplicationName($application->name);
    }

    /**
     * @inheritdoc
     */
    public function createApplication($name,$userId)
    {
        $application = new Application();
        $application->name = $name;
        $application->userId=$userId;
        return $application;
    }

    /**
     * @inheritdoc
     */
    public function createNavigation($name,$appName)
    {
        $navigation = new Navigation();
        $navigation->name = $name;
        $navigation->appName=$appName;
        $navigation->url='';
        $navigation->pic='';
        $navigation->sort=1;
        return $navigation;
    }

    public function createOperate($name,$appName)
    {
        $operate = new Operate();
        $operate->name = $name;
        $operate->appName=$appName;
        $operate->url='';
        $operate->pic='';
        $operate->sort=1;
        return $operate;
    }

    /**
     * @inheritdoc
     */
    public function createRole($name,$appName)
    {
        $role = new Role();
        $role->name = $name;
        $role->appName=$appName;
        return $role;
    }

    /**
     * @inheritdoc
     */
    public function createPermission($name,$appName)
    {
        $permission = new Permission();
        $permission->name = $name;
        $permission->appName=$appName;
        return $permission;
    }

    /**
     * @inheritdoc
     */
    public function add($object)
    {
        if ($object instanceof Item) {
            if ($object->ruleName && $this->getRule($object->ruleName) === null) {
                $rule = \Yii::createObject($object->ruleName);
                $rule->name = $object->ruleName;
                $this->addRule($rule);
            }
            return $this->addItem($object);
        } elseif ($object instanceof Rule) {
            return $this->addRule($object);
        } elseif ($object instanceof Application) {
            return $this->addApplication($object);
        } elseif ($object instanceof Menu) {//添加菜单后，生成对应菜单权限
            if($this->addMenu($object)){
                $permission=$this->createPermission($object->name,$object->appName);
                $permission->description=$object->description;
                return $this->addItem($permission);
            }
            return true;
        } else {
            throw new InvalidParamException('Adding unsupported object type.');
        }
    }

    /**
     * @inheritdoc
     */
    public function remove($object)
    {
        if ($object instanceof Item) {
            return $this->removeItem($object);
        } elseif ($object instanceof Rule) {
            return $this->removeRule($object);
        } elseif ($object instanceof Application) {
            return $this->removeApplication($object);
        } elseif ($object instanceof Menu) {
            if($this->removeMenu($object)){//移除菜单后，把对应的权限也删除掉
                $permission=$this->getPermission($object->name);
                return $this->removeItem($permission);
            }
            return true;
        } else {
            throw new InvalidParamException('Removing unsupported object type.');
        }
    }

    /**
     * @inheritdoc
     */
    public function update($name, $object)
    {
        if ($object instanceof Item) {
            if ($object->ruleName && $this->getRule($object->ruleName) === null) {
                $rule = \Yii::createObject($object->ruleName);
                $rule->name = $object->ruleName;
                $this->addRule($rule);
            }
            return $this->updateItem($name, $object);
        } elseif ($object instanceof Rule) {
            return $this->updateRule($name, $object);
        } elseif ($object instanceof Application) {
            return $this->updateApplication($name, $object);
        }elseif ($object instanceof Menu) {//更新菜单后，把对应的权限名也更新下
            $menu=$this->getMenu(['name' => $name]);
            if($this->updateMenu($name, $object)){
                //获取到要被更新的权限
                $permission=$this->getPermission($name);
                $permission->name=$object->name;
                $permission->appName=$object->appName;
                return $this->updateItem($name,$permission);
            }
            return true;
        }else {
            throw new InvalidParamException('Updating unsupported object type.');
        }
    }

    /**
     * Executes the rule associated with the specified auth item.
     *
     * If the item does not specify a rule, this method will return true. Otherwise, it will
     * return the value of [[Rule::execute()]].
     *
     * @param string|int $user the user ID. This should be either an integer or a string representing
     * the unique identifier of a user. See [[\yii\web\User::id]].
     * @param Item $item the auth item that needs to execute its rule
     * @param array $params parameters passed to [[CheckAccessInterface::checkAccess()]] and will be passed to the rule
     * @return bool the return value of [[Rule::execute()]]. If the auth item does not specify a rule, true will be returned.
     * @throws InvalidConfigException if the auth item has an invalid rule.
     */
    protected function executeRule($user, $item, $params)
    {
        if ($item->ruleName === null) {
            return true;
        }
        $rule = $this->getRule($item->ruleName);
        if ($rule instanceof Rule) {
            return $rule->execute($user, $item, $params);
        } else {
            throw new InvalidConfigException("Rule not found: {$item->ruleName}");
        }
    }

    /**
     * Checks whether array of $assignments is empty and [[defaultRoles]] property is empty as well
     *
     * @param Assignment[] $assignments array of user's assignments
     * @return bool whether array of $assignments is empty and [[defaultRoles]] property is empty as well
     * @since 2.0.11
     */
    protected function hasNoAssignments(array $assignments)
    {
        return empty($assignments) && empty($this->defaultRoles);
    }
}
