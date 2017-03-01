<?php

namespace app\controllers;

use yii\rest\ActiveController;

class UserController extends ActiveController
{
    public $modelClass = 'app\models\User';

    
    /**
     * 
     *
     * @return string
     */
    public function actionView($id)
    {
        echo 111;die;
        return User::findOne($id);
    }

    
}
