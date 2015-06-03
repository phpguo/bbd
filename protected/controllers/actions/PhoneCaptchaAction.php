<?php

class PhoneCaptchaAction extends CAction
{

    /*
     * @var string $cacheID 缓存ID
     */
    public $cacheID = 'cache';
    /*
     * @var string $cachePre Cache前缀
     */
    public $cachePre = 'phone_captcha_';
    /**
     * 发送间隔缓存
     * @var string $limitCachePre Cache前缀
     */
    public $limitCachePre = 'phone_limit_captcha_';
    /**
     * 发送间隔缓存
     * @var string $limitCachePre Cache前缀
     */
    public $limitIpCachePre = 'ip_limit_captcha_';
    /*
     * @var int $expire发送间隔过期时间
     */
    public $limitExpire = 55;
    /*
     * @var string $fieldName 手机号字段名
     */
    public $fieldName = 'phone';
    /*
     * @var int $expire 验证码过期时间
     */
    public $expire = 300;
    /*
     * @var string $codeLength 验证码长度
     */
    public $codeLength = 4;

    /*
     * @var string $_phone 手机号码
     */
    private $_phone;
    /*
     * @var ICache $_cache 缓存实例
     */
    private $_cache;
    /*
     * @var string $_captcha 验证码
     */
    private $_captcha;

    /**
     * 验证码验证
     */
    public function validateVcode($code){
        $captcha = Yii::app()->createController('captcha');
        list($controller, $actionID) = $captcha;
        $valiResult = $controller->createAction('captcha')->validate($code,false);
        return $code && $valiResult;
    }

    public function run($template = 'captchaRegisteText',$hasVcode = 0) {
        try {
            $this->initialize();
            if (Regex::isLegalMobilePhone($this->_phone)) {
                //验证图形验证码
                if($hasVcode){
                    if(!$this->validateVcode($_GET['vcode'])){
                        Response::send(500, Yii::t('validation', 'The Captcha has expired or wrong'));die;
                    }
                }
                //手机号间隔判断
                $limitCache = $this->_cache->get($this->limitCachePre . $this->_phone);
                if($limitCache){
                    Response::send(ErrorCode::code('phone_verification_code_sent_frequent'), ErrorCode::msg('phone_verification_code_sent_frequent'));die;
                }
                //ip间隔判断
                $ip = Yii::app()->request->clientIp;
                $limitCache = $this->_cache->get($this->limitIpCachePre . $ip);
                if($limitCache){
                    Response::send(ErrorCode::code('phone_verification_code_sent_frequent'), ErrorCode::msg('phone_verification_code_sent_frequent'));die;
                }
                $this->generateCaptcha();
                $templateText = property_exists('PhoneTemplate', $template) ? PhoneTemplate::$$template : PhoneTemplate::$captcha;
                $message = sprintf($templateText, $this->getCaptcha());
                if (Message::phone($this->_phone, $message)) {
                    $this->_cache->set($this->limitCachePre . $this->_phone, 1, $this->limitExpire);
                    $this->_cache->set($this->limitIpCachePre . $ip, 1, $this->limitExpire);
                    Response::send(ErrorCode::code('success'), ErrorCode::msg('success'));
                } else {
                    Response::send(ErrorCode::code('system_error'), ErrorCode::msg('system_error'));
                }
            } else {
                Response::send(ErrorCode::code('phone_format_illegal'), ErrorCode::msg('phone_format_illegal'));
            }
        } catch (MFException $e) {
            Yii::log($e, CLogger::LEVEL_INFO);
            Response::send(ErrorCode::code('system_error'), ErrorCode::msg('system_error'));
        }
    }

    private function initialize() {
        $this->_cache = Yii::app()->getComponent($this->cacheID);
        if (!($this->_cache instanceof ICache)) {
            throw new SystemException(ErrorCode::msg('cache_type_error'), ErrorCode::code('cache_type_error'));
        }
        $this->_phone = Yii::app()->request->getParam($this->fieldName);
    }

    /**
     * 生成Captcha并入Cache
     *
     * @throws MFException
     */
    private function getCaptcha() {
        if (!$this->_captcha) {
            $this->generateCaptcha();
        }

        return $this->_captcha;
    }

    /**
     * 生成验证码
     *
     * @throws SystemException
     */
    private function generateCaptcha() {
        $this->_captcha = $this->_cache->get($this->cachePre . $this->_phone);
        if(!$this->_captcha){
            $captcha = new MFPhonecaptcha();
            $captcha->charSlot = '0123456789';
            $captcha->codeLength = $this->codeLength;
            $this->_captcha = $captcha->getCode();
        }
        if (!$this->_cache->set($this->cachePre . $this->_phone, $this->_captcha, $this->expire)) {
            throw new SystemException(ErrorCode::msg('cache_set_error'), ErrorCode::code('cache_set_error'));
        }
    }

    /**
     *
     *  校验短信验证码
     *
     * @param string  $code 待校验验证码
     * @param string  $phoneField 手机号码表单字段名称
     * @param boolean $caseSensitive 大小写是否敏感
     * @param boolean $delete 是否验证后删除
     *
     * @return boolean 验证通过
     */
    public function validate($code, $phoneField, $caseSensitive = false, $delete = false) {
        $this->fieldName = $phoneField;
        $this->initialize();
        if ($cacheCode = $this->_cache->get($this->cachePre . $this->_phone)) {
            if ($code == $cacheCode || (!$caseSensitive && strtolower($code) == strtolower($cacheCode))) {
                Yii::log('SMS verify success:' . $this->_phone, CLogger::LEVEL_INFO);
                if ($delete) {
                    $this->_cache->delete($this->cachePre . $this->_phone);
                }
                return true;
            } else {
                return false;
            }
        }

        return false;
    }
}
