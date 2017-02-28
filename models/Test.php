<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\base\Event;

/**
 * ContactForm is the model behind the contact form.
 */
class Test extends Model
{
    const EVENT_HELLO="helloword";


    public function init(){
        $this->on(self::EVENT_HELLO,[$this,'say'],'ni hao');
        $this->on(self::EVENT_HELLO,[$this,'ma'],'sao ni ma');
    }

    public function say($event){
        echo json_encode($event->data);
    }

    public function ma($event){
        echo json_encode($event->data);
    }

}
