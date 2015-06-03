<?php

/**
 * Class File
 * 文件辅助类
 */
class File
{
    /**
     * 远程URL文件保存本地
     *
     * @param string $url 远程URL
     * @param int    $connectTimeout
     * @param int    $executeTimeout
     *
     * @return string
     * @throws Exception
     */
    public static function saveRemoteFile($url = '', $connectTimeout = 3, $executeTimeout = 3)
    {
        if (self::isRemoteFile($url) && self::isRemoteFileExist($url)) {
            $filename = tempnam(Yii::app()->getRuntimePath(), 'avatar');
            $ch = curl_init(); 
            curl_setopt($ch, CURLOPT_URL, $url);  
            curl_setopt($ch,CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, $connectTimeout); 
            curl_setopt($ch,CURLOPT_TIMEOUT, $executeTimeout); 
            $str = curl_exec($ch);
            curl_close($ch);
            if($str) {
                @file_put_contents($filename, $str);
            }else{
                @unlink($filename);
                throw new Exception(ErrorCode::msg('remote_file_timeout'), ErrorCode::code('remote_file_timeout'));
            }
            return $filename;
        }else{
            throw new Exception(ErrorCode::msg('remote_file_path_error'), ErrorCode::code('remote_file_path_error'));
        }
    }

    /**
     * 远程文件是否存在
     *
     * @param $filePath 远程URL
     *
     * @return bool
     */
    public static function isRemoteFileExist($filePath)
    {
        return $filePath && @fopen($filePath, 'r');
    }

    /**
     * 是否为远程文件（HTTP[S]）
     *
     * @param $filePath
     *
     * @return bool
     */
    public static function isRemoteFile($filePath){
        return (boolean)preg_match('/http|https/i', $filePath); 
    }

    /**
     * 获取文件后缀
     *
     * @param $filename 文件路径
     *
     * @return string
     */
    public static function fileSuffix($filename){
        $pathInfo = pathinfo($filename);
        return isset($pathInfo[PATHINFO_EXTENSION]) ? $pathInfo[PATHINFO_EXTENSION] : '';
    }

}
