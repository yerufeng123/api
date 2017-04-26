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
class Item extends Object
{
    const TYPE_ROLE = 1;
    const TYPE_PERMISSION = 2;
    const PRIVATE_ALLOW=1;
    const PRIVATE_DENIED=2;
    const STATUS_ENABLE=1;
    const STATUS_DISABLE=2;
    const DANGER_LOW=1;
    const DANGER_MIDDLE=2;
    const DANGER_HIGH=3;



    /**
     * @var int the type of the item. This should be either [[TYPE_ROLE]] or [[TYPE_PERMISSION]].
     */
    public $type;
    /**
     * @var string the name of the item. This must be globally unique.
     */
    public $appName;
    /**
     * @var string the name of the item. This must be globally unique.
     */
    public $name;
    /**
     * @var string the item description
     */
    public $description;
    /**
     * @var int 是否可公开申请1：公开可申请2：不能公开申请
     */
    public $private;
    /**
     * @var int 状态1：有效2：无效
     */
    public $status;
    /**
     * @var int 风险等级1：低2：中3：高
     */
    public $danger;
    /**
     * @var string name of the rule associated with this item
     */
    public $ruleName;
    /**
     * @var mixed the additional data associated with this item
     */
    public $data;
    /**
     * @var int UNIX timestamp representing the item creation time
     */
    public $createdAt;
    /**
     * @var int UNIX timestamp representing the item updating time
     */
    public $updatedAt;
}
