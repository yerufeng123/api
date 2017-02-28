<?php
namespace app\components;

use yii\base\Behavior;

class MyBehavior3 extends Behavior
{

    public function foo3()
    {
        echo '这是行为测试3';
    }
}