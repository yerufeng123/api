<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\User;

/**
 * 登录控制器（唯一不需要继承Base控制器）
 * @author gaoxiangdong<gxd_dnjlw@163.com>
 * @copyright 2017-03-19
 */
class LoginController extends Controller
{
    /**
     *登录-页面
     */
    public function actionIndex(){
        return $this->renderPartial('login');
    }

    /**
     *登录-接口
     */
    public function actionLoginin(){
        // 当前用户的身份实例。未认证用户则为 Null 。
        $identity = Yii::$app->user->identity;
        var_dump($identity);

        // 当前用户的ID。 未认证用户则为 Null 。
        $id = Yii::$app->user->id;
        var_dump($id);

        // 判断当前用户是否是游客（未认证的）
        $isGuest = Yii::$app->user->isGuest;
        var_dump($isGuest);

                // 使用指定用户名获取用户身份实例。
        // 请注意，如果需要的话您可能要检验密码
        $identity = User::findOne(['username' => 'aaa']);
        var_dump($identity);
        // 登录用户
        Yii::$app->user->login($identity);

        // 当前用户的身份实例。未认证用户则为 Null 。
        

        // 当前用户的ID。 未认证用户则为 Null 。
        $id = Yii::$app->user->id;
        var_dump($id);

        // 判断当前用户是否是游客（未认证的）
        $isGuest = Yii::$app->user->isGuest;
        var_dump($isGuest);

    }

    /**
     *登出-接口
     */
    public function actionLoginout(){
        Yii::$app->user->logout();
    }
}
