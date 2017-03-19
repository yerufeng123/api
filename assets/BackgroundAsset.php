<?php
/**
 * @copyright 2017-03-15
 * @author gaoxiangdong<gxd_dnjlw@163.com>
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class BackgroundAsset extends AssetBundle
{
    public $basePath = '@cmsroot';
    public $baseUrl = '@cms';
    public $css = [
        'css/site.css',
    ];
    public $js = [
    ];
    public $depends = [
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
