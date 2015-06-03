<?php
use application\service\exception\ServiceException;

class PhoneCaptchaValidator extends CValidator
{

    public $caseSensitive = false;

    public $delete = false;

    public $phoneField = 'phone';

    public $phoneCaptchaAction = 'phoneCaptcha';

    public $throwException = false;

    protected function validateAttribute($object, $attribute) {
        $value = $object->$attribute;
        if ($this->isEmpty($value)) {
            if ($this->throwException) {
                throw new ServiceException(ErrorCode::msg('phone_capthcha_empty'), ErrorCode::code('phone_capthcha_empty'));
            }
            $this->addError($object, $attribute, Yii::t('validation', 'Please input the Phone Captcha'));

            return;
        }
        $captcha = $this->getPhoneCaptchaAction();
        if (is_array($value) || !$captcha->validate($value, $this->phoneField, $this->caseSensitive, $this->delete)) {
            if ($this->throwException) {
                throw new ServiceException(ErrorCode::msg('phone_capthcha_error'), ErrorCode::code('phone_capthcha_error'));
            }
            $message = $this->message !== null ? $this->message : Yii::t('validation', 'The Captcha has expired or wrong');
            $this->addError($object, $attribute, $message);
        }
    }

    protected function getPhoneCaptchaAction() {
        if (($captcha = Yii::app()->getController()->createAction($this->phoneCaptchaAction)) === null) {
            if (strpos($this->phoneCaptchaAction, '/') !== false) // contains controller or module
            {
                if (($ca = Yii::app()->createController($this->phoneCaptchaAction)) !== null) {
                    list($controller, $actionID) = $ca;
                    $captcha = $controller->createAction($actionID);
                }
            }
            if ($captcha === null)
                throw new CException(Yii::t('yii', 'RequestIdValidator.action "{id}" is invalid. Unable to find such an action in the current controller.',
                    array('{id}' => $this->phoneCaptchaAction)));
        }

        return $captcha;
    }
}
