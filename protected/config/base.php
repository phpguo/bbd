<?php
return array(
    'language'=>'zh_cn',
    'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
    'name'=>'邦邦达',
    'modules'=>array(
        'app',
    ),
    'import'=>array(
        'application.controllers.actions.*',
        'application.models.*',
        'application.models.context.*',
        'application.models.validators.*',
        'application.helpers.*',
        'application.components.*',
        'application.components.push.*',
        'application.components.response.*',
        'application.service.*',
        'application.service.exception.*',
    ),
	'components'=>array(
		'urlManager'=>array(
			'urlFormat'=>'path',
            'showScriptName'=>false,
			'rules'=>array(
			),
		),
        'request'=>array(
            'class'=>'application.components.BBDRequest',
        ),
        'http'=>array(
            'class'=>'application.components.Http',
        ),
        // API签名组件
        'apiSign' => array(
            'class' => 'application.components.BBDApiSign'
        ),
		/*'user'=>array(
            'class'=>'application.components.MFWebUser',
		),

        'UserInfoCache'=>array(
            'class'=>'application.components.UserInfoCache',
        ),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning, info',
                    'maxLogFiles'=>64,
                    'maxFileSize'=>65536,
                    'logPath'=>'/data/log/www/u.mofang.com',
				),

			),
		),
		'errorHandler'=>array(
			'errorAction'=>'site/error',
		),
        'securityManager'=>array(
           'cryptAlgorithm'=>'blowfish',
        ),
        'encryption'=>array(
           'class'=>'application.components.Encryption',
        ),
        'emailPush'=>array(
            'class'=>'application.components.push.EmailPushService',
            'enable'=>false,
        ),
        'qiniu'=>array(
            'class'=>'application.components.Qiniu',
        ),
        'smarty'=>array(
            'class'=>'application.components.CSmarty',
        ),
        'dataCache'=>array(
            'class'=>'application.components.DataCache',
        ),
        'loginUmbrella'=>array(
            'class'=>'application.components.umbrella.LoginRequestTimeUmbrella',
            //'showCaptcha'=>1
        ),
        'registerUmbrella'=>array(
            'class'=>'application.components.umbrella.RegisterIpUmbrella',
            'showCaptcha'=>1
        ),
        'fahao'=>array(
            'class'=>'application.components.Fahao',
        ),
        //短信
        'sms'=>array(
            'class'=>'application.components.Sms',
        ),
        // API签名组件
        'apiSign' => array(
            'class' => 'pub.MApiSign'
        ),
        // 公共发号组件
        'mfahao' => array(
            'class' => 'pub.MFahao'
        ),
        //curl
        'curl' => array(
            'class' => 'pub.MCurl'
        ),*/
    ),
);

