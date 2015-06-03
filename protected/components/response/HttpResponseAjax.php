<?php

class HttpResponseAjax implements IHttpResponse
{
    private $entity;

    public function __construct(HttpResponseEntity $entity) {
        $this->entity = $entity;
    }

    /**
     * @see
     */
    public function response() {
        Yii::log($this->entity, 'trace');
        echo json_encode($this->entity->build());
    }
}
