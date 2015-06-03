<?php

class Regex
{
    public static $mobilePhoneRegex = '/^1[34578]\d{9}$/';
    public static $passwordRegex = '/^[\@A-Za-z0-9\!\#\$\%\^\&\*\.\~]{6,16}$/';

    public static function isLegalQQ($qq) {
        return preg_match(self::$qqRegex, $qq);
    }

    public static function isLegalMobilePhone($mobilePhone) {
        return preg_match(self::$mobilePhoneRegex, $mobilePhone);
    }

    public static function isLegalPassword($password) {
        return preg_match(self::$passwordRegex, $password);
    }

}
