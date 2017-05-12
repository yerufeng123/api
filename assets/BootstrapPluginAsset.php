<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for the Twitter bootstrap javascript files.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class BootstrapPluginAsset extends AssetBundle
{
    public $basePath = '@webroot/public/basic';
    public $baseUrl = '@web/public/basic';
    public $js = [
    	'js/plugins/jquery.js',
        'js/plugins/bootstrap.js',
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
    ];
}
