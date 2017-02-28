<?php
namespace app\components;

use yii\base\Behavior;

class MyBehavior2 extends Behavior
{

    public function foo2()
    {
        echo '这是行为测试2';
    }
}