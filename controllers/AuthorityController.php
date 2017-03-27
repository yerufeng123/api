<?php
namespace app\controllers;

use app\components\BaseController;

class AuthorityController extends BaseController
{
    public function actionIndex()
    {
        return $this->renderPartial('index');
    }
}