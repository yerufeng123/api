<?php
namespace app\components;
use yii\base\Event;

class MessageEvent extends Event
{
    public $message;
}