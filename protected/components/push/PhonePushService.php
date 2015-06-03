<?php

/**
 * 手机短信服务
 *
 * 构造Message对象，调用send方法发送
 * 例如：
 *     <code>
 *          $message = new PhoneMessageEntity();
 *          $message->to = '对方手机号码';
 *          $message->message = '内容';
 *          $service = new PhonePushService();
 *          $service->send($message);
 *     </code>
 */
class PhonePushService extends PushService
{

    /**
     * 发送短信
     *
     * @param IMessageEntity $message 消息实体
     *
     * @throws SystemException
     *
     * @todo 多终端 你妹的，做好了，结果全都还原了
     */
    public function send(IMessageEntity $message) {
        try {
            $provider = new HongLianProvider();
            $provider->sendSms($message->message, $message->to);
        } catch (PushException $e) {
            throw new SystemException($e->getMessage(), $e->getCode());
        }
    }
}

