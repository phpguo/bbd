<?php
class MFString{

    /**
     * 中文字符按2个位宽计算
     * @method 计算字符位宽
     * @param string $str
     * @return string
     */
    public static function mfstrlen($str){
        $string = preg_replace(Regex::$chineseCharRegex, '**', $str);
        return strlen($string);
    }

    /**
     * @method 去除BOM头
     * @param string $str 待处理字符串
     * @return string
     */
    public static function removeBom($str){
        $res = substr(($str),0,3);
        if (ord($res[0]) == 239 && ord($res[1]) == 187 && ord($res[2]) == 191){
            $str = substr($str,3);
        }
        return $str;
    }

    /**
     * @method 是否为URL
     * @param string $string 待检查字符串
     * @return boolean
     */
    public static function isUrl($string){
        return (bool)filter_var($string, FILTER_VALIDATE_URL);
    }

    /**
     * @method 生成session_id
     * @return string
     */
    public static function session_id() {
        return substr(md5(microtime(true)),-16);
    }

    public static function username() {
        return strtoupper(uniqid());
    }

    /**
     * 返回加密邮箱
     * @param $email
     * @return string
     */
    public static function passport_email($email){
        return substr($email,0,2).'...'.substr($email,stripos($email, '@')-1,-1).substr($email,-1,stripos($email, '@'));
    }

    /**
     * 返回加密邮箱
     * @param $phone
     * @return string
     */
    public static function passprot_phone($phone){
        return substr($phone,0,3).'******'.substr($phone,9,2);
    }
}

