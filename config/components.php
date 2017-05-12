<?php

return [
    'request' => [
        // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
        'cookieValidationKey' => 'gaoxiangdong',
        "enableCsrfValidation"=>true,//开启csrf令牌验证可防止csrf攻击,如果开启该功能，要求所有post请求传入csrf令牌
    ],
    'cache' => [
        'class' => 'yii\caching\FileCache',
    ],
    'user' => [
        'identityClass' => 'app\models\User',
        'enableAutoLogin' => true,
        'loginUrl' => ['login/index'],
    ],
    'errorHandler' => [
        'errorAction' => 'site/error',
        'maxSourceLines' => 20,
    ],
    'mailer' => [
        'class' => 'yii\swiftmailer\Mailer',
        // send all mails to a file by default. You have to set
        // 'useFileTransport' to false and configure a transport
        // for the mailer to send real emails.
        'useFileTransport' => true,
    ],
    'log' => [
        'traceLevel' => YII_DEBUG ? 3 : 0,
        'flushInterval' => 10, 
        'targets' => [
            [
                'class' => 'yii\log\FileTarget',
                //'levels' => ['error', 'warning','info'],
                'categories' => ['app\controllers\*'],
                'prefix' => function ($message) {
                    $user = Yii::$app->has('user', true) ? Yii::$app->get('user') : null;
                    $userID = $user ? $user->getId(false) : '-';
                    return "[$userID]";
                },
                'logVars' => [''],
                'exportInterval' => 10,  // default is 1000
            ],
        ],
    ],
    'db' => require(__DIR__ . '/db.php'),
    'urlManager' => [
        'enablePrettyUrl' => true,
        //'enableStrictParsing' => true,
        'showScriptName' => false,
        'rules' => [
          //  ['class' => 'yii\rest\UrlRule', 'controller' => 'user'],
        ],
    ],

    // 'session' => [
    //     'class' => 'yii\web\DbSession',
    //     // 'db' => 'mydb',  // 数据库连接的应用组件ID，默认为'db'.
    //     'sessionTable' => 'my_session', // session 数据表名，默认为'session'.
    // ],
    'authManager' => [
        'class' => 'app\components\rbac\DbManager',  
        'itemTable' => 'web_auth_item',  
        'assignmentTable' => 'web_auth_assignment',  
        'itemChildTable' => 'web_auth_item_child',  
        'ruleTable'=>'web_auth_rule' 
    ],
    'assetManager' => [//资源管理器
        'bundles' => require(__DIR__ . '/' . (YII_ENV_DEV ? 'assets-dev.php' : 'assets-prod.php')),  
        // 'bundles' => array_merge(
        //     [
        //         'yii\web\JqueryAsset' => [
        //             'js' => [
        //                 YII_ENV_DEV ? 'jquery.js' : 'jquery.min.js',
        //             ]
        //         ],
        //     ],
        //     require(__DIR__ . '/' . (YII_ENV_DEV ? 'assets-dev.php' : 'assets-prod.php'))
        // ),  
        'appendTimestamp' => false,//开启可防止前端缓存(静态文件后添加了时间戳)
        //'linkAssets' => true,//创建一个符号链接到要发布的资源包源路径， 这比拷贝文件方式快
        //'basePath'=>'',//修改默认发布位置
        //'baseUrl'=>'',//修改默认发布位置
    ],
    'view' => [//视图组件
        'theme' => [
            'basePath' => '@app/themes/basic',
            'baseUrl' => '@web/themes/basic',
            'pathMap' => [
                '@app/views' => '@app/themes/basic',
                //'@app/modules' => '@app/themes/basic/modules',//主题话模块
            ],
        ],
    ],

];
