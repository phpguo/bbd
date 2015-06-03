<?php
class ErrorCode
{
    private static $errorCodes = array(
        //处理成功
        'success'=>0,
        //系统级错误
        'system_error'=>500,
        //参数验证错误
        'authentication_failed' => 300,
        //参数级错误
        'param_error'=>400,
        //无效的操作
        'illegal_operation' => 401,
        //系统错误9XXXX
        'response_type_error'=>100,


        //账户信息错误10 user
        'empty_phone' => 100001,
        'phone_has_exists' => 100002,
        'the_phone_is_illegal' => 100003,
        'empty_password' => 100004,
        'the_password_is_illegal' => 100005,

    );

    private static $errorMessages = array(
        //处理成功
        'success'=>'Success',
        //系统内部错误
        'system_error'=>'System Error!',
        'authentication_failed' => 'Authentication failed!',
        //系统错误9XXXX
        'response_type_error'=>'Response Type Error',
        'illegal_operation' => 'illegal operation',
        //账户信息错误1XXXX
        'param_error'=>'The parameters of request is not enough or error',

        'empty_phone' => 'Empty phone',
        'phone_has_exists' => 'Phone has exists',
        'the_phone_is_illegal' => 'The format of the phone is illegal',
        'empty_password' => 'The password is empty',
        'the_password_is_illegal' => 'The format of the password is illegal',
    );

    public static function code($description){
        return array_key_exists($description, self::$errorCodes) ? self::$errorCodes[$description] : '';
    }

    public static function msg($description, $context = array()){
        return  array_key_exists($description, self::$errorMessages) ?
            Yii::t('error', self::$errorMessages[$description], $context) : $description;
    }
}

