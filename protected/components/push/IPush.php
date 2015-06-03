<?php

interface IPush
{
    public function send(IMessageEntity $message);
}
