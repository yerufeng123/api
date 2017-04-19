<?php
namespace app\controllers;

use Yii;
use app\components\BaseController;

class RbacController extends BaseController
{
    public $layout = 'basic_nav';

    public function actionInit()
    {
        $auth = Yii::$app->authManager;
        $userId=1;
        //创建一个应用
        //$application=$auth->createApplication('test_appname',$userId);
        //$auth->add($application);
        $application=$auth->getApplicationByName('test_appname');
        //var_dump($application);die;
        //创建两个导航菜单，一个操作菜单
        //$menu01=$auth->createNavigation('menu01',$application->name);
        //$menu02=$auth->createNavigation('menu02',$application->name);
        //$operate03=$auth->createOperate('operate03',$application->name);
        //$menu04=$auth->createNavigation('menu04',$application->name);
        //$operate05=$auth->createOperate('operate05',$application->name);
        //$auth->add($operate03);
        //$auth->add($menu04);
        //$auth->add($operate05);
        //追加菜单2到菜单1,追加操作3到菜单2，追加操作5到菜单2
        //$menu01=$auth->getNavigation('menu01');
        //$menu02=$auth->getNavigation('menu02');
        //$menu04=$auth->getNavigation('menu04');
        //$operate03=$auth->getOperate('operate03');
        //$operate05=$auth->getOperate('operate05');
        //$auth->addMenuChild($menu01,$menu02);
        //$auth->addMenuChild($menu02,$operate03);
        //$auth->addMenuChild($menu02,$operate05);
        $menulist=$auth->getMenuList($application);
        var_dump($menulist);die;

        // 添加 "createPost" 权限
        $createPost = $auth->createPermission('createPost',$application->name);
        $createPost->description = 'Create a post';
        $auth->add($createPost);
                echo '<pre>';
        var_dump($createPost);die;

        // 添加 "updatePost" 权限
        $updatePost = $auth->createPermission('updatePost');
        $updatePost->description = 'Update post';
        $auth->add($updatePost);

        // 添加 "author" 角色并赋予 "createPost" 权限
        $author = $auth->createRole('author');
        $auth->add($author);
        $auth->addChild($author, $createPost);

        // 添加 "admin" 角色并赋予 "updatePost" 
		// 和 "author" 权限
        $admin = $auth->createRole('admin');
        $auth->add($admin);
        $auth->addChild($admin, $updatePost);
        $auth->addChild($admin, $author);

        // 为用户指派角色。其中 1 和 2 是由 IdentityInterface::getId() 返回的id （译者注：user表的id）
        // 通常在你的 User 模型中实现这个函数。
        $auth->assign($author, 2);
        $auth->assign($admin, 1);
    }

    /**
     *我的权限
     */
    public function actionIndex(){
        return $this->render('myauth');
    }
}