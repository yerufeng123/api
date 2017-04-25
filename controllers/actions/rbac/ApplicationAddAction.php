<?php
namespace app\controllers\actions\rbac;

use yii\base\Action;

class ApplicationAddAction extends Action
{
    public function run($msg='The World is beautiful')
    {
        return $msg;
    }
}