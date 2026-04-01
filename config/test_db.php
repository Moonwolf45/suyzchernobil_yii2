<?php
$db = require __DIR__ . '/params.php';

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=soyzchernobilkurgan_local',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'tablePrefix' => '',
    'enableSchemaCache' => true,
];
