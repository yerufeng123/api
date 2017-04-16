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
class Application extends Object
{

    /**
     * @var string 应用名
     */
    public $name;
    /**
     * @var string 用户账号
     */
    public $userId;
    /**
     * @var int UNIX timestamp 创建时间
     */
    public $createdAt;
    /**
     * @var int UNIX timestamp 更新时间
     */
    public $updatedAt;
}
