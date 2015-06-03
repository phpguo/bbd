<?php

interface IHttpResponse
{
    /**
     * 输出响应信息
     */
    public function response();

    public function __construct(HttpResponseEntity $entity);
}
