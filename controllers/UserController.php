<?php

namespace app\controllers;

use app\components\BaseController;

class UserController extends BaseController
{
    public function actionView(){
    	return $this->renderPartial('view');
    }
}
