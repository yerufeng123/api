<?php
/**
 * @copyright 2017-03-15
 * @author gaoxiangdong<gxd_dnjlw@163.com>
 */

namespace app\themes\basic\login;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class BackgroundAsset extends AssetBundle
{
    public $basePath = '@app/themes/asssets';
    public $baseUrl = '@web/themes/asssets';
    public $css = [
        'css/style.default.css',
    ];
    public $js = [
        //"js/plugins/jquery-1.7.min.js",
        'js/plugins/jquery-ui-1.8.16.custom.min.js',
        'js/plugins/jquery.cookie.js',
        'js/plugins/jquery.uniform-2.2.2.min.js',
        'js/custom/general.js',
        'js/custom/index.js',
    ];
    public $depends = [
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
