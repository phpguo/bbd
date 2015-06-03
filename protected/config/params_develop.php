<?php
return array(
	'params'=>array(
        'thirdparty'=>array(
            'redirect_uri'=>'account/thirdparty',
            'bind_redirect_uri'=>'web/setting/bind_thirdparty',
            'providers'=>array(
                'Qq'=>array(
                    'keys' => array('id' => '100426657', 'secret' => 'b4445902c632ecd3212b6b64f6cba890' ),
                    'scope' => '',
                ),
                'Sina'=>array(
                    'keys' => array('id' => '910834796', 'secret' => 'ce0aad573886faa1d280e806d8040284' ),
                    'scope' => '',
                ),
                'Twitter'=>array(
                    'keys' => array('id' => 'zmSzonhcLWwH3B7KCRDjlGKd1', 'secret' => '9X7ssCow1sS2IQZq7r5gd3HrX9EZfobTzFx7B7TSCgwVcOMbaC' ),
                    'scope' => '',
                    'isOAuth1'=>true
                ),
                'Google'=>array(
                    'keys' => array('id' => '1022205159719-n16vok3q6krnhh00gpc26q0gcgng5lg5.apps.googleusercontent.com', 'secret' => '802G7GPj71Bp6cfgvoMd3eEM' ),
                    'scope' => 'https://www.googleapis.com/auth/plus.login',
                ),
                'Facebook'=>array(
                    'keys' => array('id' => '451419631629376', 'secret' => '86373423a3e34efae4d46e4d063b90df' ),
                    'scope' => '',
                    'certificate_file' => dirname(__FILE__).'/fb_ca_chain_bundle.crt',
                ),
            ),
        ),
    ),
);

