<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => '2_SoyzChernobilKurgan',
    'name' => $params['title'],
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        'queue',
    ],
    'language' => 'ru-RU',
    'timeZone' => 'Asia/Yekaterinburg',
    'defaultRoute' => 'news/index',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'on beforeRequest' => function ($event) {
        \yii\helpers\Inflector::$transliterator = 'Russian-Latin/BGN; Any-Latin; Latin-ASCII; NFD; [:Nonspacing Mark:] Remove; NFC; [:Punctuation:] Remove; Lower();';
    },
    'components' => [
        'reCaptcha' => [
            'class' => 'himiklab\yii2\recaptcha\ReCaptchaConfig',
            'siteKeyV2' => $params['siteKeyV2'],
            'secretV2' => $params['secretV2'],
            'siteKeyV3' => $params['siteKeyV3'],
            'secretV3' => $params['secretV3'],
        ],
        'queue' => [
            'class' => \yii\queue\db\Queue::class,
            'db' => $db,
            'tableName' => '{{%queue}}',
            'channel' => 'default',
            'mutex' => \yii\mutex\MysqlMutex::class,
            'attempts' => 5,
            'as log' => \yii\queue\LogBehavior::class
        ],
        'formatter' => [
            'class' => 'yii\i18n\Formatter',
            'locale' => 'ru_RU',
            'timeZone' => 'Asia/Yekaterinburg',
            'dateFormat' => 'dd.MM.yyyy',
            'timeFormat' => 'H:mm:ss',
            'datetimeFormat' => 'dd.MM.yyyy H:mm:ss',
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',
            'currencyCode' => 'RUB',
        ],
        'request' => [
            'cookieValidationKey' => 'rm1Xz5XXQkjcpJT6rZsCesB3rwPntGX2',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['news/login'],
        ],
        'errorHandler' => [
            'errorAction' => 'news/error',
        ],
        'mailer' => [
            'class' => yii\symfonymailer\Mailer::class,
            'transport' => [
                'scheme' => 'smtps',
                'host' => '',
                'username' => '',
                'password' => '',
                'port' => 465,
                'dsn' => 'native://default',
            ],
            'useFileTransport' => false,
            'viewPath' => '@app/mail',
            'htmlLayout' => 'layouts/html',
            'textLayout' => 'layouts/text',
            'messageConfig' => [
                'charset' => 'UTF-8',
                'from' => ['info@xn--80abggjugofbdwfe3b6c2dta2a.xn--p1ai' => $params['title']],
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                ], [
                    'class' => 'yii\log\FileTarget',
                    'logFile' => '@runtime/logs/' . date('Y') . '/' . date('m') . '/' . date('d')
                        . '-http-request.log',
                    'levels' => ['error', 'warning', 'info'],
                    'categories' => ['yii\httpclient\*'],
                ], [
                    'class' => 'yii\log\FileTarget',
                    'logFile' => '@runtime/logs/' . date('Y') . '/' . date('m') . '/' . date('d')
                        . '-jobs-vk.log',
                    'levels' => ['error', 'warning', 'info'],
                    'categories' => ['jobs-vk'],
                ], [
                    'class' => 'yii\log\FileTarget',
                    'logFile' => '@runtime/logs/' . date('Y') . '/' . date('m') . '/' . date('d')
                        . '-jobs-ok.log',
                    'levels' => ['error', 'warning', 'info'],
                    'categories' => ['jobs-ok'],
                ], [
                    'class' => 'yii\log\FileTarget',
                    'logFile' => '@runtime/logs/' . date('Y') . '/' . date('m') . '/' . date('d')
                        . '-jobs-social.log',
                    'levels' => ['error', 'warning', 'info'],
                    'categories' => ['jobs-social'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                /* Sitemap */
                ['pattern' => 'sitemap', 'route' => 'sitemap/index', 'suffix' => '.xml'],

                '/' => 'news/index',
                'archive/page/<page:\d+>' => 'news/archive',
                'archive' => 'news/archive',
                'contact' => 'news/contact',
                'documents' => 'news/documents',
                'our-achievements' => 'news/our-achievements',
                'subscribe' => 'news/subscribe',

                'news/search' => 'news/search',

                'news-video/page/<page:\d+>' => 'news/videos',
                'news-video' => 'news/videos',

                'news/<category_alias:[A-Za-z0-9_-]+>/<alias:[A-Za-z0-9_-]+>' => 'news/view',
                'category/<alias:[A-Za-z0-9_-]+>/page/<page:\d+>' => 'category/view',
                'category/<alias:[A-Za-z0-9_-]+>' => 'category/view',
                'tags/<alias:[A-Za-z0-9_-]+>/page/<page:\d+>' => 'tags/view',
                'tags/<alias:[A-Za-z0-9_-]+>' => 'tags/view',

                '<module:\w+>/<controller:\w+>/<action:\w+>/<id:\d+>' => '<module>/<controller>/<action>',
                '<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
            ]
        ],
        'assetManager' => [
            'bundles' => [
                'yii\bootstrap5\BootstrapAsset' => [
                    'sourcePath' => '@bower/bootstrap/dist',
                    'css' => [
                        YII_ENV_DEV ? 'css/bootstrap.css' : 'css/bootstrap.min.css',
                    ]
                ],
                'yii\bootstrap5\BootstrapPluginAsset' => [
                    'sourcePath' => '@bower/bootstrap/dist',
                    'js' => [
                        YII_ENV_DEV ? 'js/bootstrap.js' : 'js/bootstrap.min.js',
                    ]
                ],
                'app\assets\FancyboxAsset' => [
                    'css' => [
                        YII_ENV_DEV ? 'jquery.fancybox.css' : 'jquery.fancybox.min.css',
                    ],
                    'js' => [
                        YII_ENV_DEV ? 'jquery.fancybox.js' : 'jquery.fancybox.min.js',
                    ]
                ],
				'yii\web\JqueryAsset' => [
                    'js' => [
                        YII_ENV_DEV ? 'jquery.js' : 'jquery.min.js',
                    ]
                ],
                'app\assets\AdminLteAsset' => [
                    'css' => [
                        YII_ENV_DEV ? 'css/adminlte.css' : 'css/adminlte.min.css',
                    ],
                    'js' => [
                        YII_ENV_DEV ? 'js/adminlte.js' : 'js/adminlte.min.js',
                    ]
                ],
            ]
        ]
    ],
    'modules' => [
        'admin' => [
            'class' => 'app\modules\admin\Module',
            'layout' => 'main_admin',
            'defaultRoute' => 'news/index',
        ],
    ],
    'controllerMap' => [
        'elfinder' => [
            'class' => 'mihaildev\elfinder\PathController',
            'access' => ['@'],
            'root' => [
                'path' => 'uploads',
                'name' => 'Files'
            ]
        ]
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
        'panels' => [
            'queue' => \yii\queue\debug\Panel::class,
            'httpclient' => [
                'class' => \yii\httpclient\debug\HttpClientPanel::class,
            ],
        ],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
        'generators' => [
            'job' => [
                'class' => \yii\queue\gii\Generator::class,
            ],
        ],
    ];
}

return $config;
