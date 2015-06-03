<?php

define('YII_ENABLE_EXCEPTION_HANDLER',0);
define('YII_ENABLE_ERROR_HANDLER',0);

// change the following paths if necessary
$yii=dirname(__FILE__).'/framework/yii.php';
//todo 根据环境变量切换配置文件
defined('ENVIRONMENT') || define('ENVIRONMENT', isset($_SERVER['ENVIRONMENT']) ? $_SERVER['ENVIRONMENT'] : 'development');
$configDir=dirname(__FILE__).'/protected/config/';
switch (ENVIRONMENT) {
    case 'development' :
        error_reporting(E_ALL ^ E_NOTICE);
        ini_set('display_errors', 1);

        $tryConfig = $configDir . 'develop' . '.php';
        file_exists($tryConfig) && $config = $tryConfig;
        unset($tryConfig);
        break;

    case 'testing' :
        $tryConfig = $configDir . ENVIRONMENT . '.php';
        file_exists($tryConfig) && $config = $tryConfig;
        unset($tryConfig);
        break;
    case 'production' :
        error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);
        ini_set('display_errors', 0);
        $tryConfig = $configDir . ENVIRONMENT . '.php';
        file_exists($tryConfig) && $config = $tryConfig;
        unset($tryConfig);
        break;

    default :
        exit();
}

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',false);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
Yii::createWebApplication($config)->run();
