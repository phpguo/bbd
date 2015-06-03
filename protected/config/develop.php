<?php
//当前版本
$base = include_once 'base.php';
$params = include_once 'params_develop.php';
$config = array(
    'components'=>array(
        'db'=>array(
            'connectionString' => 'mysql:host=127.0.0.1;dbname=bbd',
//            'schemaCachingDuration'=>86400,
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'autoConnect' => true,
            'tablePrefix' => 'bbd_',
        ),
        'cache'=>array(
            'class'=>'system.caching.CMemCache',
            'useMemcached'=>false,
            'servers'=>array(
                array(
                    'host'=>'127.0.0.1',
                    'port'=>'11211'
                ),
            )
        ),
    ),

);
return array_merge_recursive($base, $config, $params);

