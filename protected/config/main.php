<?php

Yii::setPathOfAlias('lib', realpath(dirname(__FILE__).'/../../lib'));
Yii::setPathOfAlias('vendor', realpath(__DIR__ . '/../../vendor'));

$params = require('params.php');
$components = array();
$logRoutes = array(
    array(
        'class' => 'CFileLogRoute',
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
        'class' => 'ESentryComponent',
    );
}

return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Myarusel',
	'language' => 'ru',

	'preload'=>array('log', 'RSentryException'),

	'import'=>array(
		'application.models.*',
		'application.models.forms.*',
		'application.components.*',
		'application.helpers.*',
	),

//	'modules'=>array(
//	),

	'components'=>array_merge(
        array(
            'user'=>array(
                // enable cookie-based authentication
                'allowAutoLogin'=>true,
                'loginUrl'=>array('site/login'),
            ),
            'urlManager'=>array(
                'urlFormat'=>'path',
                'urlSuffix' => '/',
                'showScriptName' => false,
                'rules'=>array(
                    '/' => '/admin/adminCarousel',
    //				'admin/' => 'admin/admin',
                    'admin/<controller:\w+>/' => 'admin/admin<controller>',
                    'admin/<controller:\w+>/<action:\w+>/' => 'admin/admin<controller>/<action>',
                    'carousel/<id:\d+>' => 'carousel/show',
    //				'<action:\w+>/<id:\d+>' => 'site/<action>',
                    '<action:\w+>' => 'site/<action>',
    //				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
    //				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
    //				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
                ),
            ),
            'db'=>array(
                'connectionString' => 'mysql:host='.$params['dbHost'].';dbname='.$params['dbName'],
                'emulatePrepare' => true,
                'username' => $params['dbLogin'],
                'password' => $params['dbPassword'],
                'charset' => 'utf8',
            ),
            'authManager'=>array(
                'class'=>'CDbAuthManager',
                'connectionID'=>'db',
            ),
            'fs' => array(
                'class' => 'FileSystem',
                'nestedFolders' => 1,
            ),
            'viewRenderer'=>array(
                'class'=>'ext.ETwigViewRenderer',
                'twigPathAlias' => 'lib.twig.lib.Twig',
                'options' => array(
                    'autoescape' => true,
                ),
                'functions' => array(
                    'widget' => array(
                        0 => 'TwigFunctions::widget',
                        1 => array('is_safe' => array('html')),
                    ),
                    'createMyarouselLink' => 'TwigFunctions::createMyarouselLink',
                ),
            ),
            'bootstrap'=>array(
                'class'=>'lib.bootstrap.components.Bootstrap', // assuming you extracted bootstrap under extensions
                'responsiveCss' => true,
            ),
            'errorHandler'=>array(
                'errorAction'=>'site/error',
            ),
            'image' => array(
                'class' => 'ext.image.CImageComponent',
                'driver' => $params['imageDriver'],
            ),
            'cache' => array(
                'class' => 'CFileCache',
            ),
            'format' => array(
                'booleanFormat' => array('Нет', 'Да'),
            ),
            'log'=>array(
                'class'=>'CLogRouter',
                'routes'=> $logRoutes,
            ),
        ),
        $components
	),

	'params'=> array_merge($params, array(
		'md5Salt' => 'ThisIsMymd5Salt(*&^%$#',
	)),
);