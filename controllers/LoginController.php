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
     *登录-页面(
     */
    public function actionIndex(){
        return $this->renderPartial('login');
    }

    /**
     *登录-接口
     */
    public function actionLoginin(){
        ob_end_flush();
        for($i = 1; $i <= 3000; $i++ ) print(" ");
        // 这一句话非常关键，cache的结构使得它的内容只有达到一定的大小才能从浏览器里输出
        // 换言之，如果cache的内容不达到一定的大小，它是不会在程序执行完毕前输出的。经
        // 过测试，我发现这个大小的底限是256个字符长。这意味着cache以后接收的内容都会
        // 源源不断的被发送出去。
        For($j = 1; $j <= 20; $j++) {
        echo $j."
        ";
        flush(); //这一部会使cache新增的内容被挤出去，显示到浏览器上
        sleep(1); //让程序"睡"一秒钟，会让你把效果看得更清楚
        }


        // Yii::$app->response->headers->add('Pragmassss', 'no-cache');
        // Yii::$app->response->format = \yii\web\Response::FORMAT_JSONP;
        // Yii::$app->response->content = 'ceshi neirong';
        // Yii::$app->response->data=['message' => 'hello world'];
        // $model=new User;
        // //接收用户输入的账号和密码
        //     $model->load(Yii::$app->request->post());

        // //检查当前用户是否已登录
        // if(!Yii::$app->user->isGuest){
        //     $this->redirect(['admin/index']);
        // }

        // //验证当前用户输入是否合法
        // if(!$model->validate()){
        //     //getErrors()
        // }

        // //验证当前用户账号和密码是否正确
        // $identity = User::findOne(['username' => 'aaa']);

        


        // // 当前用户的身份实例。未认证用户则为 Null 。
        // $identity = Yii::$app->user->identity;
        // var_dump($identity);

        // // 当前用户的ID。 未认证用户则为 Null 。
        // $id = Yii::$app->user->id;
        // var_dump($id);

        // // 判断当前用户是否是游客（未认证的）
        // $isGuest = Yii::$app->user->isGuest;
        // var_dump($isGuest);

        //         // 使用指定用户名获取用户身份实例。
        // // 请注意，如果需要的话您可能要检验密码
        // $identity = User::findOne(['username' => 'aaa']);
        // var_dump($identity);
        // // 登录用户
        // Yii::$app->user->login($identity);

        // // 当前用户的身份实例。未认证用户则为 Null 。
        

        // // 当前用户的ID。 未认证用户则为 Null 。
        // $id = Yii::$app->user->id;
        // var_dump($id);

        // // 判断当前用户是否是游客（未认证的）
        // $isGuest = Yii::$app->user->isGuest;
        // var_dump($isGuest);

    }

    /**
     *登出-接口
     */
    public function actionLoginout(){
        Yii::$app->user->logout();
    }
}
