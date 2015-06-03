<?php
//当前版本
$base = include_once 'base.php';
$params = include_once 'params_production.php';
$config = array(
    'components'=>array(
        'db'=>array(
            'connectionString' => 'mysql:host=10.6.16.194;dbname=usercenter',
//            'schemaCachingDuration'=>86400,
            'emulatePrepare' => true,
            'username' => 'usercenter',
            'password' => 'UJJ2xYZe',
            'charset' => 'utf8',
            'autoConnect' => true,
            'tablePrefix' => 'user_',
        ),
        'cache'=>array(
            'class'=>'system.caching.CMemCache',
            'useMemcached'=>true,
            'servers'=>array(
                array(
                    'host'=>'backend1',
                    'port'=>'11213'
                    ),
                array(
                    'host'=>'backend2',
                    'port'=>'11213'
                    )
                )
        ),
    ),

);
return array_merge_recursive($base, $params, $config);
