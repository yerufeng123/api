<?php
namespace app\components;

use yii\base\Behavior;
use app\components\MessageEvent;
use app\models\Test;

class MyBehavior extends Behavior
{
    public $prop1;

    private $_prop2;

    public function getProp2()
    {
        return $this->_prop2;
    }

    public function setProp2($value)
    {
        $this->_prop2 = $value;
    }

    public function foo()
    {
        echo '这是行为测试';
    }

    public function events(){
    	return [
    	    Test::EVENT_HELLO=>['app\models\Test','lala'],
    	];
    }

    public function behaviorEvent(){
    	echo '行为中的事件测试';
    }
}