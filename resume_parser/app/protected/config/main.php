<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
require_once( dirname(__FILE__) . '/../components/UtilityHelper.php');
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'Resume Parser',

	// preloading 'log' component
	'preload'=>array('log','input'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*'
	),
	'modules'=>array(
		// uncomment the following to enable the Gii tool
		
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'piglet',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1','217.23.70.98','188.168.233.1'),
		),
	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		'input'=>array(   
            'class'         => 'CmsInput',  
            'cleanPost'     => true,  
            'cleanGet'      => true,  
        ),
		// uncomment the following to enable URLs in path-format
		/*
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		*/
		'db'=>array(
				'connectionString'=>'mysql:host=localhost;dbname=itinsigh_resumeparser',
				'username'=>'root',
				'password'=>'My31415926q',  
				'charset' => 'utf8'
		),
		// uncomment the following to use a MySQL database
		/*
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=testdrive',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
		),
		*/
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log' => array(
			'class' => 'CLogRouter',
			'routes' => array(
				array(
					'class' => 'CFileLogRoute',
					'levels' => 'info, error, warning, vardump',
				),
				// uncomment the following to show log messages on web pages
				// array(
					// 'class' => 'CWebLogRoute',
					// 'enabled' => YII_DEBUG,
					// 'levels' => 'error, warning, trace, notice',
					// 'categories' => 'application',
					// 'showInFireBug' => false,
				// ),
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
	),
);