<?php

class Context extends CFormModel
{
    /**
     * trim过滤
     * @param mixed $str 待过滤串
     * @return string
     */
    public function trim($str) {
        return is_null($str) ? $str : trim($str);
    }

    /**
     * 安全过滤
     * @param string $str 待过滤字符
     * @return string
     */
    public function purify($str) {
        if (is_null($str)) {
            return $str;
        } else {
            $purify = new CHtmlPurifier();

            return $purify->purify($str);
        }
    }

    public function validate($attributes = null, $clearErrors = true) {
        if ($clearErrors)
            $this->clearErrors();
        if ($this->beforeValidate()) {
            foreach ($this->getValidators() as $validator) {
                $validator->validate($this, $attributes);
                if ($this->hasErrors()) {
                    break;
                }
            }
            $this->afterValidate();

            return !$this->hasErrors();
        } else
            return false;
    }

    public function getError($attribute = null) {
        $errors = $this->getErrors();
        if ($attribute === null) {
            $error = array_shift($errors);
            return $error[0];
        } else {
            return isset($errors[$attribute]) ? array_shift($errors[$attribute]) : array();
        }
    }
}

