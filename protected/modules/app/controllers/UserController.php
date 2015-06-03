<?php
class UserController extends AppBaseController{

    public function actionRegister(){
        $request = $this->request;
        $phone = $request->getPost('phone','');
        $password = $request->getPost('password','');
        $vcode = $request->getPost('vcode','');
        $registerContext = new RegisterContext();
        $registerContext->phone = $phone;
        $registerContext->rawPassword = $password;
        $registerContext->vcode = $vcode;
        if(!$registerContext->validate()){
            Response::send(ErrorCode::code('param_error'), $registerContext->getError());
            die;
        }

    }
}