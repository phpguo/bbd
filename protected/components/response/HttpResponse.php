<?php

class HttpResponse
{
    const RESPONSE_TYPE_AJAX = 0;
    const RESPONSE_TYPE_XML = 1;
    const RESPONSE_TYPE_JSONP = 2;

    protected static $ResponseTypeGroup;

    private function init() {
        self::$ResponseTypeGroup = array(
            self::RESPONSE_TYPE_AJAX  => 'ajax',
            self::RESPONSE_TYPE_XML   => 'xml',
            self::RESPONSE_TYPE_JSONP => 'jsonp'
        );
    }

    /**
     * @method 按指定格式输出响应信息
     * @param HttpResponseEntity $entity 响应消息实体
     * @param integer            $type 响应消息格式
     *
     * @throws CHttpException
     */
    public function response(HttpResponseEntity $entity, $type = self::RESPONSE_TYPE_AJAX) {
        try {
            $this->isSupportResponseType($type);
            $handlerClassname = __CLASS__ . ucfirst(self::$ResponseTypeGroup[$type]);
            $responseHandler = new $handlerClassname($entity);
            $responseHandler->response();
        } catch (MFException $e) {
            throw new CHttpException(403, $e->getMessage());
        }
    }

    /**
     * 是否支持{@$type}类型
     *
     * @param int $type 响应类型
     *
     * @throws MFException
     */
    public function isSupportResponseType($type) {
        $this->init();
        if (!array_key_exists($type, self::$ResponseTypeGroup)) {
            throw new MFException(
                ErrorCode::msg('response_type_error'),
                ErrorCode::code('response_type_error')
            );
        }
    }
}
