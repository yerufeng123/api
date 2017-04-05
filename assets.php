<?php
/**
 * Configuration file for the "yii asset" console command.
 */

// In the console environment, some path aliases may not exist. Please define these:
 Yii::setAlias('@webroot', 'web');
 Yii::setAlias('@web', '/');

return [
    // Adjust command/callback for JavaScript files compressing:
    'jsCompressor' => 'java -jar vendor/crisu83/closurecompiler-bin/build/compiler.jar --js {from} --js_output_file {to}',
    // Adjust command/callback for CSS files compressing:
    'cssCompressor' => 'java -jar vendor/nervo/yuicompressor/yuicompressor.jar --type css {from} -o {to}',
    // Whether to delete asset source after compression:
    'deleteSource' => false,
    // The list of asset bundles to compress:
    'bundles' => [
        'app\themes\basic\BackgroundAsset',
    ],
    // Asset bundle for compression output:
    'targets' => [
        'all' => [//共用部分
            'class' => 'yii\web\AssetBundle',
            'basePath' => '@webroot/public',
            'baseUrl' => '@web/public',
            'js' => 'js/all-{hash}.js',
            'css' => 'css/all-{hash}.css',
            'depends' => [
                
            ],
        ],
        'back' => [//后台部分
            'class' => 'yii\web\AssetBundle',
            'basePath' => '@webroot/public',
            'baseUrl' => '@web/public',
            'js' => 'js/back-{hash}.js',
            'css' => 'css/back-{hash}.css',
            'depends' => [
                'app\themes\basic\BackgroundAsset',
            ],
        ],
    ],
    // Asset manager configuration:
    'assetManager' => [
        'basePath' => '@webroot/public/basic',
        'baseUrl' => '',
    ],
];