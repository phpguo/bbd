<?php

/**
 * Class Message
 *
 * 信息辅助类
 *
 */
class Message {
    /**
     *  发送邮件
     *
     * @param string $to 收件人
     * @param string $subject 邮件主题
     * @param string $message 邮件内容
     *
     * @return boolean
     */
    public static function email($to, $subject, $message){
        try{
            $messageEntity = new EmailMessageEntity();
            $messageEntity->subject = $subject;
            $messageEntity->to = $to;
            $messageEntity->message = $message;
            $messageEntity->contentType = 'text/html';
            $emailService = new EmailPushService();
            $emailService->send($messageEntity);
            return true;
        }catch(Exception $e){
            Yii::log('邮件发送失败：'.$e->getMessage(), 'info');
            return false;
        }
    }

    /**
     * 发送短信
     *
     * @param string $to 接收信息的手机号码
     * @param string $message 短信内容
     *
     * @return bool
     */
    public static function phone($to, $message){
        try{
            /*$messageEntity = new PhoneMessageEntity();
            $messageEntity->to = $to;
            $messageEntity->message = $message;
            $phoneService = new PhonePushService();
            $phoneService->send($messageEntity);*/
            /*第二次
            $sendApi = Yii::app()->params->send_phone_msg;
            $data = array(
                'phone' => $to,
                'message' => $message,
            );
            $sendResult = CJSON::decode(Yii::app()->http->post($sendApi,$data));
            if($sendResult && $sendResult['code'] == '0'){
                return true;
            }else{
                throw new PushException($sendResult['message'], $sendResult['code']);
            }*/
            Yii::app()->sms->send($to, $message);
            return true;
        }catch(Exception $e){
            return false;
        }
    }
}
