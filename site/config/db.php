<?php

return [
    'db' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=db;dbname=cbt',
        'username' => 'root',
        // 'password' => '',
        'charset' => 'utf8',
        // 'enableSchemaCache' => true,
    ],
    'dbNge' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=db;dbname=nge',
        'username' => 'root',
        // 'password' => 'site-pass',
        'charset' => 'utf8',
        // 'enableSchemaCache' => true,
    ],
];
// https://www.youtube.com/watch?v=URlo4QjNNao&t=991s