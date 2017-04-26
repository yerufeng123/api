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
            $this->auth->loadFromCache();
        }
        
        if($this->menulist === null){
            $application=$this->auth->getApplicationByname($this->appname);
            $this->menulist=$this->auth->getMenuList($application);
            $view = Yii::$app->view;  
            $view->params['menulist']=$this->menulist;
        }
    }

    public function actionInit()
    {
        $userId=1;
        $application=$this->auth->getApplicationByname($this->appname);
        $menu=$this->auth->getNavigation('admin_rbac_basic_rbac_auth');
        //$menu->url='/rbac/role';
        //$menu->pic='editor';
        $menu->sort=3;
        $this->auth->update('admin_rbac_basic_rbac_auth',$menu);die;

        //$menu->url='/rbac/app_add';
        //$menu->description='新增应用';
        //$this->auth->update('admin_api_basic_rbac_application_add',$menu);
        //$menu02->url='/rbac/myauth_myauth';
        //$menu02->description='我的权限';
        //$this->auth->update('admin_api_basic_rbac_myauth_myauth',$menu02);die;
        //$menu02=$this->auth->getNavigation('admin_api_basic_rbac_myauth_approve');
        //$menu02=$this->auth->addMenuChild($menu,$menu02);
        //$this->auth->addMenuChild($menu,$menu02);

        //$application->name='admin_api';
        //$this->auth->update('admin_api_header',$application);die;
        //$menu=$this->auth->getNavigation('admin_api'.Yii::$app->controller->module->id.Yii::$app->controller->id.'myauth');
        //$this->auth->remove($menu);die;
         //$menu01=$this->auth->getNavigation('admin_api_basic_rbac_application');
         //$menu=$this->auth->createNavigation('admin_api'.'_'.Yii::$app->controller->module->id.'_'.Yii::$app->controller->id.'_'.'application_add',$application->name);
         //$menu->description='新增管理';
         //$this->auth->add($menu);
         //$this->auth->addMenuChild($menu01,$menu);
        //$response=Yii::$app->response;
        //$response->format=Response::FORMAT_JSON;
        //return $response->data=$this->auth->getMenuList($application);
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
        return $this->render('myauth');
    }

    /**
     *导航——菜单管理
     */
    public function actionMenu(){
        return $this->render('myauth');
    }

    /**
     *导航——角色管理
     */
    public function actionRole(){
        return $this->render('myauth');
    }
}