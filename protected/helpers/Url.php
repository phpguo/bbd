<?php
class Url {

    public static function isUrl($string){
        return (bool)filter_var($string, FILTER_VALIDATE_URL);
    }
}
