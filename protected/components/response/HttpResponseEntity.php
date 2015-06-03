<?php

class HttpResponseEntity
{
    const HEADER_CODE = 'code';
    const HEADER_MESSAGE = 'message';
    const HEADER_DATA = 'data';

    public $code = -1;
    public $msg = '';
    public $data = array();

    public function __construct($code = -1, $msg = '', $data = array()) {
        $this->setCode(-1 == $code ? ErrorCode::code('system_error') : $code);
        $this->setMsg($msg);
        $this->setData($data);
    }

    public function setCode($code) {
        $this->code = (integer)$code;
    }

    public function setMsg($msg) {
        $this->msg = (string)$msg;
    }

    public function setData($data) {
        $this->data = (array)$data;
    }

    public function build() {
        return array(
            self::HEADER_CODE    => $this->code,
            self::HEADER_MESSAGE => $this->msg,
            self::HEADER_DATA    => $this->data,
        );
    }

    public function __toString() {
        return 'Response Header Code : ' . $this->code . ' MSG : ' . $this->msg . ' Data : ' . serialize($this->data);
    }
}
