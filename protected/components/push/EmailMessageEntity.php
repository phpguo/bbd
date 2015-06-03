<?php

/**
 * 邮件信息实体
 */
class EmailMessageEntity extends MessageEntity
{

    public $subject = '';
    public $charset = 'utf-8';
    public $contentType = 'text/plain';

    public function __construct() {
    }
}
