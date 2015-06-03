<?php
class Serialize
{
    public static function exception(Exception $e){
        return Yii::t(
            'mfsystem', 
            'Exception occous: Code-{code} Description-{msg} File-{file} Line-{line}',
            array(
                '{code}'=>$e->getCode(),
                '{msg}'=>$e->getMessage(),
                '{file}'=>$e->getFile(),
                '{line}'=>$e->getLine(),
            )
        );
    }
}
