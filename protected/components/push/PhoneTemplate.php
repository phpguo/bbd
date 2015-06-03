<?php

class PhoneTemplate
{

    public static $resetPasswordText = "请点击下面链接重置密码 %s [请勿向任何人提供您收到的短信验证码]";

    public static $captchaRegisteText = <<<EOT
验证码 %s，您正在注册魔方网账号，需要进行验证[请勿向任何人提供您收到的短信验证码]
EOT;

    public static $captcha = <<<EOT
验证码 %s [请勿向任何人提供您收到的短信验证码]
EOT;
    public static $captchaBindText = <<<EOT
您的验证码为：%s （5分钟内有效）。此验证码仅限加加、魔方网账号管理使用。如非本人操作，请忽略。
EOT;


}
