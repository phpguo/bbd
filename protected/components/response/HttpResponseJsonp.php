<?php

class HttpResponseJsonp implements IHttpResponse
{
    public function __construct(HttpResponseEntity $entity) {
        $this->entity = $entity;
    }

    public function response() {
        Yii::log($this->entity, 'trace');
        echo $_GET['callback'] . '(' . json_encode($this->entity) . ')';
    }
}
