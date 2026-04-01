<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => '2_SoyzChernobilKurgan_console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => [
        'log',
        'queue',
    ],
    'controllerNamespace' => 'app\commands',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => '@app/tests',
    ],
    'components' => [
        'queue' => [
            'class' => \yii\queue\db\Queue::class,
            'db' => $db,
            'tableName' => '{{%queue}}',
            'channel' => 'default',
            'mutex' => \yii\mutex\MysqlMutex::class,
            'attempts' => 5,
            'as log' => \yii\queue\LogBehavior::class,
                'handleAttempt' => function($event) {
                if ($event->attempt >= 5) {
                    // Отправить уведомление администратору
                }
            },
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
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
                ],
            ],
        ],
        'db' => $db,
		'urlManager' => [
			'baseUrl' => 'https://xn--80abggjugofbdwfe3b6c2dta2a.xn--p1ai',
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
    ],
    'params' => $params,
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\faker\FixtureController',
        ],
    ],
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];

    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
