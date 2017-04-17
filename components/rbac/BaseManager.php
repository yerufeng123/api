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
    abstract protected function getItem($name);

    /**
     * Returns the items of the specified type.
     * @param int $type the auth item type (either [[Item::TYPE_ROLE]] or [[Item::TYPE_PERMISSION]]
     * @param string $application 应用
     * @return Item[] the auth items of the specified type.
     */
    abstract protected function getItems($type,$application);

    /**
     * Adds an auth item to the RBAC system.
     * @param Item $item the item to add
     * @return bool whether the auth item is successfully added to the system
     * @throws \Exception if data validation or saving fails (such as the name of the role or permission is not unique)
     */
    abstract protected function addItem($item);

    /**
     * Adds a rule to the RBAC system.
     * @param Rule $rule the rule to add
     * @return bool whether the rule is successfully added to the system
     * @throws \Exception if data validation or saving fails (such as the name of the rule is not unique)
     */
    abstract protected function addRule($rule);

    /**
     * Removes an auth item from the RBAC system.
     * @param Item $item the item to remove
     * @return bool whether the role or permission is successfully removed
     * @throws \Exception if data validation or saving fails (such as the name of the role or permission is not unique)
     */
    abstract protected function removeItem($item);

    /**
     * Removes a rule from the RBAC system.
     * @param Rule $rule the rule to remove
     * @return bool whether the rule is successfully removed
     * @throws \Exception if data validation or saving fails (such as the name of the rule is not unique)
     */
    abstract protected function removeRule($rule);

    /**
     * Updates an auth item in the RBAC system.
     * @param string $name the name of the item being updated
     * @param Item $item the updated item
     * @return bool whether the auth item is successfully updated
     * @throws \Exception if data validation or saving fails (such as the name of the role or permission is not unique)
     */
    abstract protected function updateItem($application,$name, $item);

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
    abstract protected function getApplicationOne($params);

    /**
     * 根据条件返回多个应用
     * @param string $params 检索的条件.
     * @return 返回全部指定条件的应用，如果没有这样的应用就返回null.
     */
    abstract protected function getApplicationMore($params);

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
    public function createMenu($name,$appName)
    {
        $menu = new Menu();
        $menu->name = $name;
        $menu->appName=$appName;
        return $menu;
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
        } elseif ($object instanceof Menu) {
            return $this->addMenu($object);
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
            return $this->removeMenu($object);
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
        }elseif ($object instanceof Menu) {
            return $this->updateMenu($name, $object);
        }else {
            throw new InvalidParamException('Updating unsupported object type.');
        }
    }

    /**
     * @inheritdoc
     */
    public function getRole($name)
    {
        $item = $this->getItem($name);
        return $item instanceof Item && $item->type == Item::TYPE_ROLE ? $item : null;
    }

    /**
     * @inheritdoc
     */
    public function getPermission($name)
    {
        $item = $this->getItem($name);
        return $item instanceof Item && $item->type == Item::TYPE_PERMISSION ? $item : null;
    }

    /**
     * @inheritdoc
     */
    public function getRoles()
    {
        return $this->getItems(Item::TYPE_ROLE);
    }

    /**
     * @inheritdoc
     */
    public function getPermissions()
    {
        return $this->getItems(Item::TYPE_PERMISSION);
    }

    /**
     * Returns the named application.
     * @param string $name the application name
     * @return Application[] the auth items of the specified type.
     */
    public function getApplication($name){
        $application = $this->getApplicationOne(['name' => $name]);
        return $application instanceof Application ? $application : null;
    }

    /**
     * Returns the  applications.
     * @return Application[] the applications.
     */
    public function getApplications(){
        return $this->getApplicationMore([]);
    }

    /**
     * Returns the application of the specified userId.
     * @param int $userId the user id
     * @return Application[] the auth item of the specified user idd.
     */
    public function getApplicationsByUser($userId){
        return $this->getApplicationMore(['user_id' => $userId]);
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
