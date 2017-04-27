<?php
/**
 * @copyright 2017-03-15
 * @author gaoxiangdong<gxd_dnjlw@163.com>
 */

namespace app\themes\basic;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class BackgroundAsset extends AssetBundle
{
    //public $sourcePath = '@app/themes/basic/asssets'; 
    public $basePath = '@webroot/public/basic';
    public $baseUrl = '@web/public/basic';
    public $css = [
        'fonts/roboto.css',
        'css/plugins/jquery.alerts.css',
        'css/plugins/uniform.tp.css',
        'css/plugins/jquery.ui.css',
        'css/plugins/jquery.ui.autocomplete.css',
        'css/plugins/fullcalendar.css',
        'css/plugins/colorbox.css',
        'css/plugins/colorpicker.css',
        'css/plugins/jquery.jgrowl.css',
        'css/plugins/jquery.tagsinput.css',
        'css/plugins/ui.spinner.css',
        'css/plugins/jquery.chosen.css',
        'css/style.default.css',
    ];
    public $js = [
        "js/plugins/jquery-1.7.min.js",
        'js/plugins/jquery-ui-1.8.16.custom.min.js',
        'js/plugins/jquery.cookie.js',
        'js/plugins/jquery.dataTables.min.js',
        'js/plugins/jquery.uniform.min.js',
        'js/plugins/jquery.flot.min.js',
        'js/plugins/jquery.flot.resize.min.js',
        'js/plugins/jquery.slimscroll.js',
        
        'js/custom/general.js',
    ];
    public $depends = [
    ];
    public $jsOptions = ['position' => \yii\web\View::POS_HEAD];
}
