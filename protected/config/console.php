<?php

Yii::setPathOfAlias('lib', realpath(__DIR__ . '/../../lib'));
Yii::setPathOfAlias('vendor', realpath(__DIR__ . '/../../vendor'));

$params = require('params.php');

$components = array();
$logRoutes = array(
    array(
        'class' => 'CFileLogRoute',
        'logFile' => 'console.log',
        'levels' => 'error,warning',
    ),
    array(
        'class' => 'CFileLogRoute',
        'levels' => 'info',
        'logFile' => 'info.log',
    )
);
if ($params['useSentry']) {
    $logRoutes[] = array(
        'class'=>'vendor.m8rge.yii-sentry-log.RSentryLog',
        'levels'=>'error, warning',
        'except' => 'exception.*',
        'dsn' => $params['sentryDSN'],
    );
    $components['RSentryException'] = array(
        'dsn' => $params['sentryDSN'],
        'class' => 'vendor.m8rge.yii-sentry-log.RSentryComponent',
    );
}

return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => $params['appName'],
    'language' => 'ru',
    'timeZone' => 'Asia/Yekaterinburg',
    'preload' => array('log', 'RSentryException'),
    'import' => array(
        'application.models.*',
        'application.models.forms.*',
        'application.components.*',
        'application.helpers.*',
    ),
    'modules' => require(__DIR__.'/modules.php'),
    'components' => array_merge(
        array(
            'db' => array(
                'connectionString' => 'mysql:host=' . $params['dbHost'] . ';dbname=' . $params['dbName'],
                'emulatePrepare' => true,
                'username' => $params['dbLogin'],
                'password' => $params['dbPassword'],
                'charset' => 'utf8',
            ),
            'errorHandler' => array(
                'class' => 'ConsoleErrorHandler',
            ),
            'authManager' => array(
                'class' => 'CDbAuthManager',
                'connectionID' => 'db',
            ),
            'fs' => array(
                'class' => 'FileSystem',
                'nestedFolders' => 1,
            ),
            'cache' => array(
                'class' => 'CMemCache',
                'useMemcached' => true,
            ),
            'log' => array(
                'class' => 'CLogRouter',
                'routes' => $logRoutes,
            ),
            'unistorage' => array(
                'class' => 'vendor.66ru.unistorage-yii-client.YiiUnistorage.YiiUnistorage',
                'host' => $params['unistorageHost'],
                'token' => $params['unistorageToken'],
            ),
        ),
        $components
    ),
    'params' => array_merge($params, array(
            'md5Salt' => 'ThisIsMymd5Salt(*&^%$#',
        )),
    'commandMap' => array(
        'migrate' => array(
            'class' => 'vendor.yiisoft.yii.framework.cli.commands.MigrateCommand',
            'migrationTable' => 'migrations',
        ),
    ),
);