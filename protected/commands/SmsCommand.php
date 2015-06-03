<?php

class SmsCommand extends CConsoleCommand
{

    const GET_REMAIN_SMS = '/getfee/';
    const SMS_TYPE_HONGLIAN = 1;
    const SMS_TYPE_YUNPIAN = 2;
    const RESPONSE_TYPE_EMAIL = 1;
    const RESPONSE_TYPE_CONSOLE = 2;
    const SMS_ALERT_NUM = 5000;

    public $recipients = array(
        'zhanglei@mofang.com',
        'chenmao@mofang.com',
        'guojia@mofang.com',
        'yuchun@mofang.com',
        'qulong@mofang.com',
    );

    public $phoneReciver = array(
        '13146610163',
        '18811395304',
        '17701337996',
    );
    public static $SMS_PROVIDER_GROUP = array(
        self::SMS_TYPE_HONGLIAN => array('name' => '九五鸿联', 'method' => 'honglian', 'enable' => true),
        self::SMS_TYPE_YUNPIAN  => array('name' => '云片', 'method' => 'yunpian', 'enable' => false),
    );

    public $help = <<<EOT
\n
短信服务Console\n
remain 查询短信余量\n
\t--type=type type：1 查询九五鸿联短信接口
\t                  2 查询云片短信接口
\t--response=type type：1 邮件发送查询结果
\t                      2 直接输出查询结果
\n
EOT;

    public $alarm = <<<EOT
%s 短信余量 %d, 已不足%d\n
EOT;

    /**
     * 帮助信息
     */
    public function actionHelp() {
        echo $this->help;
    }

    /**
     * 查询短信余量
     *
     * @param int    $type 短信提供商 1:九五鸿联 2:云片网络
     * @param int    $response 输出形式 1:Email 2:控制台(默认)
     * @param string $recipients 收件人,多个收件人以“,”分割
     */
    public function actionRemain($type = null, $response = self::RESPONSE_TYPE_CONSOLE, $recipients = '') {
        $msg = '';
        $msg .= "查询短信余量\n\n";
        $types = !array_key_exists($type, self::$SMS_PROVIDER_GROUP) ? array_keys(self::$SMS_PROVIDER_GROUP) : (array)$type;
        foreach ($types as $type) {
            //短信提供商是否启用
            if (!self::$SMS_PROVIDER_GROUP[$type]['enable']) {
                break;
            }
            //调用查询短信余额接口
            $method = self::$SMS_PROVIDER_GROUP[$type]['method'] . 'SmsRemain';
            $sms = $this->$method();
            $msg .= "\t\t";
            if (is_numeric($sms)) {
                $msg .= self::$SMS_PROVIDER_GROUP[$type]['name'] . "-短信余量为：{$sms} 条";
            } else {
                $msg .= self::$SMS_PROVIDER_GROUP[$type]['name'] . "-短信余量查询暂时不可用\n{$sms}";
            }
            $msg .= "\n";
        }
        $msg .= "\n";
        if (self::RESPONSE_TYPE_EMAIL == $response) {
            $recipients = preg_split('/[\s,]+/', trim($recipients), -1, PREG_SPLIT_NO_EMPTY);
            $recipients = $recipients ? array_merge($this->recipients, $recipients) : $this->recipients;
            $recipients = array_unique($recipients);
        }
        //输出查询结果
        switch ($response) {
            case self::RESPONSE_TYPE_EMAIL:
                if (Message::email($recipients, '短信余量查询', nl2br($msg))) {
                    echo "邮件发送成功\n";
                } else {
                    echo "邮件发送失败\n";
                }
                break;
            case self::RESPONSE_TYPE_CONSOLE:
            default:
                echo $msg;
        }
    }

    /**
     * 短信预警cron
     *
     * @param int $num 短信预警阀值
     */
    public function actionRemainCron($num = self::SMS_ALERT_NUM) {
        $msg = '';
        $types = array_keys(self::$SMS_PROVIDER_GROUP);
        foreach ($types as $type) {
            //短信提供商是否启用
            if (!self::$SMS_PROVIDER_GROUP[$type]['enable']) {
                break;
            }
            //调用查询短信余额接口
            $method = self::$SMS_PROVIDER_GROUP[$type]['method'] . 'SmsRemain';
            $sms = $this->$method();
            if (is_numeric($sms) && $sms < $num) {
                $msg .= sprintf($this->alarm, self::$SMS_PROVIDER_GROUP[$type]['name'], $sms, $num) . "\n";
            } elseif (!is_numeric($sms)) {
                $msg .= self::$SMS_PROVIDER_GROUP[$type]['name'] . "-短信余量查询暂时不可用\n{$sms}";
            } else {
                Yii::log("SMS Query :" . self::$SMS_PROVIDER_GROUP[$type]['name'] . " 短信余量{$sms}条", 'info');
            }
        }
        //若存在问题，发送邮件预警
        if ($msg) {
            $msg = "短信余量报警\n\n" . $msg;
            if (Message::email($this->recipients, '短信余量报警', nl2br($msg))) {
                Yii::log('短信预警发送成功', 'info');
            } else {
                Yii::log('短信预警发送失败', 'info');
            }
            if($this->phoneReciver){
                foreach($this->phoneReciver as $phone){
                    Message::phone($phone, $msg);
                }
            }
        }
    }

    /**
     * 查询云片短信提供商短信余量
     */
    private function yunpianSmsRemain() {
        return false;
    }

    /**
     * 查询九五鸿联短信提供商短信余量
     */
    private function honglianSmsRemain() {
        $honglian = Yii::app()->params->honglian;
        $url = $honglian['url'] . self::GET_REMAIN_SMS;
        $params = array(
            'username' => $honglian['username'],
            'epid'     => $honglian['epid'],
            'password' => md5($honglian['password']),
        );
        try {
            $response = Yii::app()->curl->get($url, $params);
        } catch (Exception $e) {
            $response = "Exception occor!\n";
            $response .= $e;
        }

        return $response;
    }
}

