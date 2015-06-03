<?php
class Response {

    public static function send($code = 1, $msg = '', $data = array()) {
        $responeEntity = new HttpResponseEntity();
        $responeEntity->code = $code;
        $responeEntity->msg = $msg;
        $responeEntity->data = $data;
        $response = new HttpResponse();
        $response->response($responeEntity, self::detectResponseType());
    }

    private static function detectResponseType() {
        $type = isset($_GET['reponse_type']) ? (int)$_GET['response_type'] : (isset($_POST['response_type']) ? (int)$_POST['response_type'] : '');
        $type = $type ? $type : (isset($_GET['callback']) ? HttpResponse::RESPONSE_TYPE_JSONP : HttpResponse::RESPONSE_TYPE_AJAX);
        return $type;
    }
}

