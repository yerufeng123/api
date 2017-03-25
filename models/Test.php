<?php

namespace app\models;

use Yii;
use app\components\BaseModel;
use yii\base\Event;

/**
 * ContactForm is the model behind the contact form.
 */
class Test extends BaseModel
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
