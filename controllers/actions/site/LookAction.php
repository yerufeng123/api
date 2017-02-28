<?php
namespace app\controllers\actions\site;

use yii\base\Action;

class LookAction extends Action
{
    public function run($msg='The World is beautiful')
    {
        return $msg;
    }
}