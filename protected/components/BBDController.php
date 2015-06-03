<?php

use \application\service\Profile;
use \application\service\Account;
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class BBDController extends CController
{
    /**
     * 获取返回URL
     *
     * @param string $default 默认返回地址
     * @param bool   $excludeReferrer 是否排除Referrer
     *
     * @return mixed|string
     */
    public function getBackUrl($default = '', $excludeReferrer = false) {
        $request = Yii::app()->request;
        $referer = $excludeReferrer ? '' : $request->urlReferrer;
        $backurl = $request->getParam('backurl');
        $default = MFString::isUrl($default) ? $default : Yii::app()->createAbsoluteUrl($default);

        return $backurl ? $backurl : ($referer && $referer != Yii::app()->createAbsoluteUrl($request->requestUri) ? $referer : $default);
    }

    public function init() {
        parent::init();
    }
}

