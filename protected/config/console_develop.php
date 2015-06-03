<?php

// This is the configuration for yiic console application.
// Any writable CConsoleApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Console Application',

	// preloading 'log' component
	'preload'=>array('log'),
    'aliases'=>array(
        'pub'=>'/opt/phplib/components',
    ),
    'import'=>array(
        'application.controllers.actions.*',
        'application.controllers.filters.*',
        'application.models.*',
        'application.models.context.*',
        'application.models.validators.*',
        'application.helpers.*',
        'application.components.*',
        'application.components.push.*',
        'application.components.response.*',
        'application.components.umbrella.*',
    ),

	// application components
	'components'=>array(
        'solrUsercenter'=>array(
            'class' => 'pub.MSolrClient',
            'host' => '192.168.1.127',
            'port' => 8080,
            'path' => '/solr/usercenter',
        ),
		'olddb'=>array(
            'class'=>'CDbConnection',
			'connectionString' => 'mysql:host=localhost;dbname=usercenter',
            //'enableProfiling'=>true,
            'schemaCachingDuration'=>86400,
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => 'uw8nk3atzES6',
			'charset' => 'utf8',
            'autoConnect' => true,
            'tablePrefix' => 'user_',
		),
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=usercenter',
            //'enableProfiling'=>true,
            'schemaCachingDuration'=>86400,
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => 'uw8nk3atzES6',
			'charset' => 'utf8',
            'autoConnect' => true,
            'tablePrefix' => 'user_',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning,info',
                    'maxLogFiles'=>31,
                    'maxFileSize'=>30720,
				),
			),
		),
        'encryption'=>array(
           'class'=>'application.components.Encryption',
        ),
        'request'=>array(
            'hostInfo'=>'http://u.test.mofang.com',
            'baseUrl'=>'',
        ),
        'http'=>array(
            'class'=>'application.components.Http',
        ),
	),
    'params'=>array(
        'yunpian'=>array(
            'apiKey'=>'12a2ef139fdc291e049ce30fccf87328'
        ),
        'honglian'=>array(
            'username' => 'mfwl',
            'password' => 'mfwl321',
            'epid' => '109681',
            'url' => 'http://114.255.71.158:8061',
        ),
        'ucenter'=>array('url'=>'http://gc.test.mofang.com/discuz/uc_server', 'key'=>'mofangUCENTER2013'),
    ),
);
