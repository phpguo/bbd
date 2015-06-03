<?php
use application\service\exception\ServiceException;
class UserService extends Service{

    /**
     * 手机号是否存在
     */
    public function checkPhoneExist($phone = ''){
        if(!$phone){
            throw new ServiceException(ErrorCode::msg('empty_phone'), ErrorCode::code('empty_phone'));
        }else{
            if(UserAccount::model()->exists('phone=:phone', array(':phone'=>$phone) )){
                throw new ServiceException(ErrorCode::msg('phone_has_exists'), ErrorCode::code('phone_has_exists'));
            }
        }
    }
}