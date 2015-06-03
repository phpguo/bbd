<?php

class EmailTemplate
{
//----------------------------------------------------------
    public static $registerText = <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="zh-CN" xml:lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>邮箱验证</title>
<style>
*{margin:0 auto;}
body{text-align: center; font-family: "Microsoft YaHei", "微软雅黑", sans-serif; }
html,body,p,a,span{margin: 0px; padding: 0px;}
img{border: none;}
a{text-decoration: none;}
.wrap{width: 600px; text-align: left; background: #fff;}
.head{ line-height: 60px;  padding: 0 50px; font-size: 16px; color: #999; background: #f5f5f5;}
.head_word{float: right; }
.con{height: 513px; padding:0 50px; font-size: 14px; }
.con p{width: 450px;}
.con_user{padding: 50px 0 25px 0;}
.con_user span{color: #34b3f6; }
.con_but{display: block; width: 160px; height: 40px; line-height: 40px; text-align: center; background: #34b3f6; color: #fff;}
.con_link{padding: 30px 0 20px 0; }
.con_link a{display: block; color: #34b3f6; line-height: 25px; text-decoration: underline;}
.con_com{color: #999;}
</style>
</head>
<body>
	<div class="wrap">
		<div class="head">
			<span class="head_word">中国手游媒体第一门户网站</span>
			<a href="http://www.mofang.com" target="_blank"><img src="http://pic0.mofang.com/307/571/b62f52a37d8feebe4526750b798206b3f2b4198f"></a>
		</div>
		<div class="con">
			<p class="con_user"><span>{email}</span>，欢迎注册魔方网</p>
			<a href="{url}"  target="_blank" class="con_but">验证邮箱</a>
			<p class="con_link">如果上面按钮无法验证，请尝试直接点击链接或者将链接复制到浏览器中完成验证：<a href="{url}" target="_blank">{url}</a></p>
			<p class="con_com">魔方网</p>
		</div>
	</div>
</body>
</html>
EOT;
//----------------------------------------------------------
    public static $activationText = <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="zh-CN" xml:lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>邮箱验证</title>
<style>
*{margin:0 auto;}
body{text-align: center; font-family: "Microsoft YaHei", "微软雅黑", sans-serif; }
html,body,p,a,span{margin: 0px; padding: 0px;}
img{border: none;}
a{text-decoration: none;}
.wrap{width: 600px; text-align: left; background: #fff;}
.head{ line-height: 60px;  padding: 0 50px; font-size: 16px; color: #999; background: #f5f5f5;}
.head_word{float: right; }
.con{height: 513px; padding:0 50px; font-size: 14px; }
.con p{width: 450px;}
.con_user{padding: 50px 0 25px 0;}
.con_user span{color: #34b3f6; }
.con_but{display: block; width: 160px; height: 40px; line-height: 40px; text-align: center; background: #34b3f6; color: #fff;}
.con_link{padding: 30px 0 20px 0; }
.con_link a{display: block; color: #34b3f6; line-height: 25px; text-decoration: underline;}
.con_com{color: #999;}
</style>
</head>
<body>
	<div class="wrap">
		<div class="head">
			<span class="head_word">中国手游媒体第一门户网站</span>
			<a href="http://www.mofang.com" target="_blank"><img src="http://pic0.mofang.com/307/571/b62f52a37d8feebe4526750b798206b3f2b4198f"></a>
		</div>
		<div class="con">
			<p class="con_user"><span>{nickname}</span>，欢迎激活邮箱</p>
			<a href="{url}"  target="_blank" class="con_but">验证邮箱</a>
			<p class="con_link">如果上面按钮无法验证，请尝试直接点击链接或者将链接复制到浏览器中完成验证：<a href="{url}" target="_blank">{url}</a></p>
			<p class="con_com">魔方网</p>
		</div>
	</div>
</body>
</html>
EOT;
    public static $registerSubject = '魔方网';
//----------------------------------------------------------
    public static $resetPasswordText = <<<EOT
尊敬的玩家：<br>

        您好！<br>
        请点击下面的链接即可重置密码：<br>

        {url}<br>
 (如果链接无法点击，请将它拷贝到浏览器的地址栏中。)<br>

        您的帐号是：{email}<br>

        魔方网<br>
        {date}<br>
EOT;
//----------------------------------------------------------
    public static $captcha = <<<EOT
尊敬的玩家：<br>

        您好！<br>
        请复制以下验证码至页面：<br>

        验证码：{vcode}<br>

        （为了您的帐号安全，请勿将验证码透露给其他人）<br>

        魔方网<br>
        {date}<br>
EOT;
    public static $captchaSubject = '魔方网';
//----------------------------------------------------------
}
