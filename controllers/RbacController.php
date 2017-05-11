<?php
namespace app\controllers;

use Yii;
use yii\web\Response;
use app\components\BaseController;
use app\helpers\ToolsHelper;

class RbacController extends BaseController
{
    public $layout = 'basic_nav1';
    public $appname = 'admin_rbac';
    public $auth=null;
    public $menulist=null;

    public function actions()
    {
        return [
            'app_add' => 'app\controllers\actions\rbac\ApplicationAddAction',//新增应用
        ];
    }

    public function init(){
        if(!$this->auth){
            $this->auth=Yii::$app->authManager;
            if(!Yii::$app->request->isAjax){
                $this->auth->loadFromCache();
            }
        }
        
        if($this->menulist === null && !Yii::$app->request->isAjax){
            $application=$this->auth->getApplicationByname($this->appname);
            $this->menulist=$this->auth->getMenuList($application);
            $view = Yii::$app->view;  
            $view->params['menulist']=$this->menulist;
        }
    }

    public function actionInit()
    {
        
        return $this->render('myauth');
        
    }

    /**
     *我的权限
     */
    public function actionIndex(){
        return $this->render('myauth');
    }

    /**
     *导航——权限管理
     */
    public function actionAuth(){
        return $this->render('myauth');
    }

    /**
     *导航——应用管理
     */
    public function actionApp(){
        $userId=1;
        /**
         *@todo:判断当前用户角色是否是超级管理员,如果是要返回全部的应用列表
         */
        $data['applist']=$this->auth->getApplicationsByUser($userId);
        return $this->renderAjax('appview',['data' => $data]);
    }

    /**
     *导航——菜单管理
     */
    public function actionMenu(){
        return $this->renderAjax('myauth');
    }

    /**
     *导航——角色管理
     */
    public function actionRole(){
        return $this->renderAjax('myauth');
    }
}