<?php

/**
 * Class YunPianProvider
 * 云片网络短信服务商
 */
class YunPianProvider
{

    /**
     * @var int PUSH_SUCCESS_CODE 短信发送成功状态码
     */
    const PUSH_SUCCESS_CODE = 0;

    public $apiKey = '';

    public function __construct() {
        $this->init();
    }

    public function init() {
        $this->apiKey = Yii::app()->params->yunpian['apiKey'];
    }

    /**
     * 请求短信网管
     *
     * @param string $url 服务URL
     * @param mixed $params 附加参数
     *
     * @throws PushException
     */
    private function post($url, $params) {
        try {
            $params['http_header'] = array(
                'Accept: text/plain;charset=utf-8',
                'Content-Type: application/x-www-form-urlencoded;charset=utf-8',
            );
            $response = Yii::app()->http->post($url, $params);
            if ($response && ($response = json_decode($response, true)) && isset($response['code']) && self::PUSH_SUCCESS_CODE === intval($response['code'])) {
                Yii::log('SMS Push Success:' . $params['mobile'] . ' ' . serialize($response['result']), 'info');
            } else {
                throw new PushException(ErrorCode::msg('sms_push_fail'), ErrorCode::code('sms_push_fail'));
            }
        } catch (HttpException $e) {
            throw new PushException(ErrorCode::msg('sms_http_fail'), ErrorCode::code('sms_http_fail'));
        }
    }

    /**
     * 模板接口发短信
     *
     * @param int $tpl_id 模板id
     * @param string $tpl_value 模板参数
     * @param string $mobile 接受短信的手机号
     */
    public function sendSmsByTpl($tpl_id, $tpl_value, $mobile) {
        $url = "http://yunpian.com/v1/sms/tpl_send.json";
        $encoded_tpl_value = urlencode("$tpl_value");
        $post = array(
            'apikey'    => $this->apiKey,
            'tpl_id'    => $tpl_id,
            'tpl_value' => $tpl_value,
            'mobile'    => $mobile,
        );
        $this->post($url, $post);
    }

    /**
     * 普通接口发短信
     *
     * @param string $text 短信内容
     * @param string $mobile 接受短信的手机号
     */
    public function sendSms($text, $mobile) {
        $url = "http://yunpian.com/v1/sms/send.json";
        $post = array(
            'mobile' => $mobile,
            'apikey' => $this->apiKey,
            'text'   => $text,
        );
        $this->post($url, $post);
    }

}
