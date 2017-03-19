<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

/**
 * 登录控制器（唯一不需要继承Base控制器）
 * @author gaoxiangdong<gxd_dnjlw@163.com>
 * @copyright 2017-03-19
 */
class LoginController extends Controller
{
    /**
     *登录
     */
    public function actionIndex(){
        return $this->renderPartial('login');
    }

    /**
     *登出
     */
    public function loginout(){

    }
}
