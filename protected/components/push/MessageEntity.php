<?php

class MessageEntity extends CComponent implements IMessageEntity
{

    public $from = null;
    public $to = null;
    public $message = '';

    public function __construct() {
    }
}
