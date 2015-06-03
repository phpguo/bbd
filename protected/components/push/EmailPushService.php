<?php
//加载Swift Email类库
Yii::import('application.vendor.swiftMailer.swift_required', true);

/**
 * 邮件服务
 *
 * 构造Message对象，调用send方法发送
 * 例如：
 *      $message = new EmailMessageEntity();
 *      $message->to = '对方邮件地址';
 *      $message->message = '邮件内容';
 *      $message->subject = '邮件内容摘要';
 *      $service = new EmailPushService();
 *      $service->send($message);
 */
class EmailPushService extends PushService
{

    /**
     * @var SMTP服务配置
     * 例如：array(
     *          'host'=>'smtp服务器路径',
     *          'username'=>'smtp用户名',
     *          'password'=>'smtp密码',
     *          'port'=>端口号
     *          )
     */
    public $smtp = array(
        'host'     => 'smtp.exmail.qq.com',
        'username' => 'noreply@mofang.com',
        'password' => 'ltfcLM2OsfPK',
        'port'     => 25,
    );
    /**
     * @var 发件人
     * 例如：array('发件人账号'=>'发件人别称') | 发件人账号
     */
    public $from = array('noreply@mofang.com' => '魔方网');
    /**
     * @var 超时时间（秒）
     */
    public $timeout = 3;
    /**
     * @var 发送邮件对象
     */
    private $mailer;

    public function __construct() {
        $this->init();
    }

    /**
     * 发送Email
     *
     * @param IMessageEntity $messageEntity 消息实体
     *
     * @return bool
     * @throws PushException
     */
    public function send(IMessageEntity $messageEntity) {
        try {
            if (!$this->isEnable()) {
                throw new PushException(ErrorCode::msg('email_not_available'), ErrorCode::code('email_not_available'));
            }
            $this->exchangeMessage($messageEntity);
            $this->mailer->send($this->message);

            return true;
        } catch (Swift_SwiftException $e) {
            throw new PushException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * 发送前装配Email格式
     *
     * @param IMessageEntity $messageEntity 消息实体
     */
    private function exchangeMessage(IMessageEntity $messageEntity) {
        $message = Swift_Message::newInstance();
        $message->setFrom($messageEntity->from ? $messageEntity->from : $this->from);
        $message->setSubject($messageEntity->subject);
        $message->setTo($messageEntity->to);
        $message->setBody($messageEntity->message, $messageEntity->contentType);
        $this->message = $message;
    }

    /**
     * 初始化发送邮件实体
     * 初始化SMTP服务对象及其他对象
     *
     * @todo 增加其他邮件服务对象
     */
    private function initMailer() {
        $transportGroup = array();
        if (!is_null($smtpTransport = $this->initSmtpMailer())) {
            $transportGroup = array($smtpTransport);
        }
        if (!count($transportGroup)) {
            throw new PushException(ErrorCode::msg('transport_not_available'), ErrorCode::code('transport_not_available'));
        }
        $transport = new Swift_Transport_LoadBalancedTransport();
        $transport->setTransports($transportGroup);
        $this->mailer = Swift_Mailer::newInstance($transport);
    }

    /**
     * 初始化SMTP协议服务对象
     */
    private function initSmtpMailer() {
        if ($this->smtp && isset($this->smtp['host'], $this->smtp['username'], $this->smtp['password'])) {
            $transport = Swift_SmtpTransport::newInstance($this->smtp['host'], isset($this->smtp['port']) ? $this->smtp['port'] : 25);
            $transport->setUsername($this->smtp['username']);
            $transport->setPassword($this->smtp['password']);
            $transport->setTimeout($this->timeout);

            return $transport;
        }

        return null;
    }

    public function init() {
        parent::init();
        $this->initMailer();
    }

}
