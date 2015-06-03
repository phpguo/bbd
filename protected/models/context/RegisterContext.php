<?php
use application\service\exception\ServiceException;
class RegisterContext extends Context
{
    /**
     * @var string $phone 手机号
     */
    public $phone;
    /**
     * @var string $md5Password 密码
     */
    public $md5Password;
    /**
     * @var string $rawPassword 明文密码
     */
    public $rawPassword;
    /**
     * @var string $vcode 手机验证码
     */
    public $vcode;


    public function attributeLabels() {
        return array(
            'phone' => '用户名',
            'rawPassword' => '密码',
            'vcode' => '验证码',
        );
    }

    /**
     * @return 以下几个是验证验证码参数
     */

    public function rules() {
        return array(
            array('phone,rawPassword,vcode', 'filter', 'filter' => array($this, 'trim')),
            array('phone,rawPassword,vcode', 'filter', 'filter' => array($this, 'purify')),
            array('phone', 'filter','filter' => array($this,'validatePhone')),
            array('rawPassword', 'required', 'skipOnError' => true),
            array('rawPassword','filter', 'filter' => array($this, 'validatePassword')),
            array('vcode', 'application.models.validators.PhoneCaptchaValidator', 'skipOnError' => true, 'delete' => true, 'phoneCaptchaAction' => 'umbrella/phone_vcode_request', 'phoneField' => 'phone',  'throwException' => false),
        );
    }

    /**
     * 验证手机号
     * @param $attribute
     */
    public function validatePhone($attribute){
        if($attribute == ''){
            $this->addError('phone', ErrorCode::msg('empty_phone'));
        }else{
            if(Regex::isLegalMobilePhone($attribute)){
                $this->phone = $attribute;
                //手机号是否存在
                $userService = new UserService();
                try{
                    $userService->checkPhoneExist($attribute);
                }catch (ServiceException $e){
                    $this->addError('phone', $e->getMessage());
                }
            }else{
                $this->addError('phone', ErrorCode::msg('the_phone_is_illegal'));
            }
        }
        return $attribute;
    }

    /**
     * 验证密码
     * @param $attribute
     */
    public function validatePassword($attribute){
        if($attribute == ''){
            $this->addError('rawPassword', ErrorCode::msg('empty_password'));
        }else{
            if(!Regex::isLegalPassword($attribute)){
                $this->addError('rawPassword', ErrorCode::msg('the_password_is_illegal'));
            }
        }
        $this->md5Password = md5($attribute);
        return $attribute;
    }

    /**
     * web端注册，nickname根据邮箱或者手机号生成
     */
    public function webNickname(){
        if($this->email){
            return MFString::passport_email($this->email);
        }elseif($this->phone){
            return MFString::passprot_phone($this->phone);
        }
        return '';
    }

    /**
     * 助手解谜加密字段
     *
     * @param string $attributes 加密字段
     *
     * @return string
     */
    public function crypt($attributes) {
        //RSA解密
        $plainText = Yii::app()->encryption->rsaDecode(hex2bin($attributes), Yii::app()->params->zhushou['privateKey']);
        if ($plainText) {
            $tempArray = explode('+', $plainText);
            @list($md5Password, $request_id) = $tempArray;
            $this->md5Password = $md5Password;
            $this->request_id = $request_id;
            $this->rawPassword = implode('+', array_slice($tempArray, 2));
            unset($tempArray);
        }

        return $attributes;
    }

    /**
     * 生成用户名
     *
     * @param string $attributes 待过滤字符串
     *
     * @return string 用户名
     */
    public function username($attributes) {
        //用户名不起作用
        /*if ($attributes && 'zhushou' == $this->getScenario()) {
            $this->validateUsername = true;

            return $attributes;
        }*/

        return strtoupper(uniqid());
    }

    /**
     * 过滤用户UID
     * 若UID未被使用，则使用该UID Update；否则Insert
     *
     * @param string $attributes uid
     *
     * @return mixed
     */
    public function uid($attributes) {
        if (!$attributes) {
            return $attributes;
        }
        $uid = UserIdAutoIncrement::model()->findByPk($attributes);
        if($uid){
            return (int)$attributes;
        }else{
            return null;
        }
    }

    /**
     * 是否限制注册
     * 移动端不需要
     *
     * @todo 公司IP有问题需修改MFHttpRequest
     */
    public function isRegisterLimit($attribute, $params) {
        //公司IP处理有问题，临时处理
        return false;
        if (Yii::app()->registerUmbrella->isDanger()) {
            $this->addError('ip', Yii::t('validation', 'register your ip is limit'));

            return true;
        }

        return false;
    }

    /**
     *  是否需要验证码验证
     *
     * @return bool
     */
    public function isCaptchaRequired() {
        return Yii::app()->registerUmbrella->isShowCaptcha();
    }

    /**
     * 注册地域处理
     *
     * @param string $attribute
     *
     * @return string
     */
    public function zone($attribute) {
        $zone = UserAccount::ZONE_NONE;
        if ($version = Yii::app()->request->getQuery('cv')) {
            $version = explode('_', $version);
            $zone = is_array($version) ? array_pop($version) : '';
            switch ($zone) {
                case 'CN':
                    $zone = UserAccount::ZONE_CHINA_CN;
                    break;
                case 'TW':
                    $zone = UserAccount::ZONE_CHINA_TW;
                    break;
            }
        }

        return $zone;
    }
}

