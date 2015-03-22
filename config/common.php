<?php

use app\models\settings\AntragsgruenApp;

/**
 * @var AntragsgruenApp $params
 */


require_once(__DIR__ . DIRECTORY_SEPARATOR . "defines.php");

return [
    'bootstrap'    => ['log'],
    'basePath'     => dirname(__DIR__),
    'components'   => [
        'cache'      => [
            'class' => 'yii\caching\FileCache',
        ],
        'mailer'     => [
            'class' => 'yii\swiftmailer\Mailer',
        ],
        'log'        => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'  => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db'         => $params->dbConnection,
        'urlManager' => array(
            'showScriptName'  => false,
            'enablePrettyUrl' => true,
            'rules'           => require(__DIR__ . DIRECTORY_SEPARATOR . "urls.php")
        ),

    ],
    'defaultRoute' => 'manager/index',
    'params'       => $params,
];