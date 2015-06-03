<?php

abstract class PushService extends CComponent implements IPush
{

    public $enable = true;
    public $message = null;

    public function __construct() {
    }

    public function init() {
    }

    public abstract function send(IMessageEntity $message);

    public function isEnable() {
        return $this->enable;
    }

}
