<?php

/**
 * Class HongLianProvider
 * 鸿联九五短信服务提供商
 */
class HongLianProvider {

    const PUSH_SUCCESS_CODE = '00';
    const PUSH_PARAM_LACK = '1';
    const PUSH_AUTHENTICATE_FAIL = 2;
    const PUSH_MONEY_OVERFLOW = 5;
    const PUSH_PHONE_NUM_OVERFLOW = 3;
    const PUSH_ERROR = 4;
    const PUSH_MESSAGE_SENSE = 6;
    const PUSH_MESSAGE_SENSE_KILL = 72;

    const MEG_PRE = '【魔方网】';

    public $username = '';

    public $password = '';

    public $url = '';

    public $epid = '';

    public function __construct(){
        $this->init();
    }

    /**
     *
     * 根据错误状态码，转换为可读提示
     *
     * @param int $code 错误状态码
     *
     * @return string
     */
    private function getMessage($code) {
        switch($code){
        case self::PUSH_PARAM_LACK:
            $message = '参数不完整';
            break;
        case self::PUSH_AUTHENTICATE_FAIL:
            $message = '授权失败：状态，密码，用户等问题';
            break;
        case self::PUSH_MONEY_OVERFLOW:
            $message = '余额不足';
            break;
        case self::PUSH_PHONE_NUM_OVERFLOW:
            $message = '手机号码过多，最多50个';
            break;
        case self::PUSH_ERROR:
            $message = '服务商发送短信失败';
            break;
        case self::PUSH_MESSAGE_SENSE:
        case self::PUSH_MESSAGE_SENSE_KILL:
                $message = '内容含有敏感词汇';
                break;
        default:
            $message = '发送失败';
        }
        return $message;
    }

    public function init() {
        $honglian = Yii::app()->params->honglian;
        $this->username = $honglian['username'];
        $this->password = md5($honglian['password']);
        $this->epid = $honglian['epid'];
        $this->url = $honglian['url'];
        if(empty($this->epid) || empty($this->username) || empty($this->password) || empty($this->url)){
            throw new PushException();
        }
    }
    /**
     *
     * Http请求短信网关
     *
     * @param string $url 服务的url地址
     * @param array $params 请求参数
     *
     * @throws PushException
     */
    private function post($url, $params){
        try{
            $params = array_merge($params, array(
                'epid' => $this->epid,
                'username' => $this->username,
                'password' => $this->password,
                'linkid' => uniqid(),
                'subcode' => '',
            ));
            $response = Yii::app()->http->get($url, $params);
            if($response && self::PUSH_SUCCESS_CODE == $response){ 
                Yii::log('SMS Push Success:'.$params['phone'], 'info');
            }else{
                Yii::log('SMS Push Fail:'.$this->getMessage($response), 'info');
                throw new PushException(ErrorCode::msg('sms_push_fail'), ErrorCode::code('sms_push_fail'));
            }
        }catch(HttpException $e){
            throw new PushException(ErrorCode::msg('sms_http_fail'), ErrorCode::code('sms_http_fail'));
        }
    }
    /**
     * 普通接口发短信
     *
     * @param string $text 短信内容
     * @param string $mobile 接收短信的手机号码
     */
    public function sendSms($text, $mobile){
        $text = self::MEG_PRE.$text;
        $post = array(
            'phone' => $mobile,
            'message' => urlencode(iconv('UTF-8', 'GB2312', $text)),
        );
        $this->post($this->url, $post);
    }
}
