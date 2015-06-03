<?php
class UmbrellaController extends BBDController {

    public function actions() {
        return array(
            'phone_vcode_request'=>array(
                'class'=>'application.controllers.actions.PhoneCaptchaAction',
            ),
        );
    }
}
