<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => require(__DIR__ . '/components.php'),
    'layout' => 'basic_header',
    'defaultRoute'=>'rbac',
    'modules' => [//定义模块
        'v1' => [
             'class' => 'app\modules\v1\Module',
        ],
        'v2' => [
              'class' => 'app\modules\v2\Module',
          ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1','192.168.222.1'] // 按需调整这里
    ];
}

return $config;
