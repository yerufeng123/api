<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\components\rbac;

use yii\base\Object;

/**
 * For more details and usage information on Item, see the [guide article on security authorization](guide:security-authorization).
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Menu extends Object
{
    const TYPE_NAVIGATION = 1;//菜单
    const TYPE_OPERATE = 2;//操作
    const TYPE_MENU_DOWN=1;//父级下拉菜单
    const TYPE_MENU_JUMP=2;//子级跳转菜单

    /**
     * @var string 菜单名
     */
    public $name;
    /**
     * @var string 应用名
     */
    public $appName;
    /**
     * @var int 菜单类型
     */
    public $type;
    /**
     * @var int 菜单样式
     */
    public $style;
    /**
     * @var int 跳转链接
     */
    public $url;
    /**
     * @var string 菜单描述
     */
    public $description;
    /**
     * @var int UNIX timestamp 创建时间
     */
    public $createdAt;
    /**
     * @var int UNIX timestamp 更新时间
     */
    public $updatedAt;
}
