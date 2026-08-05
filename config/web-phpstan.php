<?php

return [
    'id' => 'app-phpstan',
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
        'httpClient' => [
            'class' => 'yii\httpclient\Client',
            'transport' => 'yii\httpclient\CurlTransport',
        ],
        'queue' => [
            'class' => 'yii\queue\db\Queue',
            'tableName' => '{{%queue}}',
            'channel' => 'default',
            'mutex' => 'yii\mutex\MysqlMutex',
            'attempts' => 5,
            'as log' => 'yii\queue\LogBehavior'
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
            'class' => 'yii\web\Request',
        ],
        'response' => [
            'class' => 'yii\web\Response',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
        ],
        'mailer' => [
            'class' => 'yii\symfonymailer\Mailer',
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
        ],
        'db' => [
            'class' => 'yii\db\Connection',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                ]
            ]
        ]
    ],
    'modules' => [
        'admin' => [
            'class' => 'app\modules\admin\Module',
        ],
    ]
];