<?php
namespace app\controllers;

use Yii;
use yii\web\Response;
use app\components\BaseController;

class RbacController extends BaseController
{
    public $layout = 'basic_nav';

    public function actionInit()
    {
        
        $response=Yii::$app->response;
        $response->format=Response::FORMAT_JSON;
        $auth = Yii::$app->authManager;
        $userId=1;
        //创建一个应用
        //$a=microtime();
        //$application=$auth->createApplication('test_appname',$userId);
        //$b=microtime();
        //echo $b-$a;echo "A\n";$a=$b;
        //$auth->add($application);
        //$b=microtime();
        //echo $b-$a;echo "B\n";$a=$b;
        $application=$auth->getApplicationByName('test_appname');
        /*$b=microtime();
        echo $b-$a;echo "C\n";$a=$b;
        //var_dump($application);die;
        //创建两个导航菜单，一个操作菜单
        $menu01=$auth->createNavigation('menu01',$application->name);
           $b=microtime();
        echo $b-$a;echo "D\n";$a=$b;
        $menu02=$auth->createNavigation('menu02',$application->name);
                   $b=microtime();
        echo $b-$a;echo "E\n";$a=$b;
        $operate03=$auth->createOperate('operate03',$application->name);
                   $b=microtime();
        echo $b-$a;echo "F\n";$a=$b;
        $menu04=$auth->createNavigation('menu04',$application->name);
                   $b=microtime();
        echo $b-$a;echo "G\n";$a=$b;
        $operate05=$auth->createOperate('operate05',$application->name);
                   $b=microtime();
        echo $b-$a;echo "H\n";$a=$b;
        $operate06=$auth->createOperate('operate06',$application->name);
                   $b=microtime();
        echo $b-$a;echo "I\n";$a=$b;
        $operate07=$auth->createOperate('operate07',$application->name);
                   $b=microtime();
        echo $b-$a;echo "J\n";$a=$b;
        $menu08=$auth->createNavigation('menu08',$application->name);
        $auth->add($menu01);
                   $b=microtime();
        echo $b-$a;echo "K\n";$a=$b;
        $auth->add($menu02);
                   $b=microtime();
        echo $b-$a;echo "L\n";$a=$b;
        $auth->add($operate06);
                   $b=microtime();
        echo $b-$a;echo "M\n";$a=$b;
        $auth->add($operate07);
                   $b=microtime();
        echo $b-$a;echo "N\n";$a=$b;
        $auth->add($menu08);
                   $b=microtime();
        echo $b-$a;echo "O\n";$a=$b;
        $auth->add($operate03);
                   $b=microtime();
        echo $b-$a;echo "P\n";$a=$b;
        $auth->add($menu04);
                   $b=microtime();
        echo $b-$a;echo "Q\n";$a=$b;
        $auth->add($operate05);
                   $b=microtime();
        echo $b-$a;echo "R\n";$a=$b;
        //追加菜单2到菜单1,追加操作3到菜单2，追加操作5到菜单2
        $menu01=$auth->getNavigation('menu01');
                   $b=microtime();
        echo $b-$a;echo "S\n";$a=$b;
        $menu02=$auth->getNavigation('menu02');
                   $b=microtime();
        echo $b-$a;echo "T\n";$a=$b;
        $menu04=$auth->getNavigation('menu04');
                   $b=microtime();
        echo $b-$a;echo "U\n";$a=$b;
        $operate03=$auth->getOperate('operate03');
                   $b=microtime();
        echo $b-$a;echo "V\n";$a=$b;
        $operate05=$auth->getOperate('operate05');
                   $b=microtime();
        echo $b-$a;echo "W\n";$a=$b;
        $auth->addMenuChild($menu01,$menu02);
                   $b=microtime();
        echo $b-$a;echo "X\n";$a=$b;
        $auth->addMenuChild($menu02,$operate03);
                   $b=microtime();
        echo $b-$a;echo "Y\n";$a=$b;
        $auth->addMenuChild($menu02,$operate05);
                   $b=microtime();
        echo $b-$a;echo "Z\n";$a=$b;
        $auth->addMenuChild($menu08,$operate06);
                   $b=microtime();
        echo $b-$a;echo "A\n";$a=$b;
        $auth->addMenuChild($menu01,$menu08);
                   $b=microtime();
        echo $b-$a;echo "B\n";$a=$b;
        $auth->addMenuChild($menu08,$operate07);
                   $b=microtime();
        echo $b-$a;echo "C\n";$a=$b;
        $menulist=$auth->getMenuList($application);
                   $b=microtime();
        echo $b-$a;echo "D\n";$a=$b;
        return $response->data=$menulist;*/

        // 添加 "createPost" 权限
        $createPost = $auth->createPermission('createPost',$application->name);
        $createPost->description = 'Create a post';
        $auth->add($createPost);

        // 添加 "updatePost" 权限
        $updatePost = $auth->createPermission('updatePost',$application->name);
        $updatePost->description = 'Update post';
        $auth->add($updatePost);

        $createPost=$auth->getPermission('createPost');
        $updatePost=$auth->getPermission('updatePost');

        // 添加 "author" 角色并赋予 "createPost" 权限
        $author = $auth->createRole('author',$application->name);
        $auth->add($author);
        $auth->addChild($author, $createPost);
        $author=$auth->getRole('author');

        // 添加 "admin" 角色并赋予 "updatePost" 
		// 和 "author" 权限
        $admin = $auth->createRole('admin',$application->name);
        $auth->add($admin);
        $admin=$auth->getRole('admin');
        $auth->addChild($admin, $updatePost);
        $auth->addChild($admin, $author);

        // 为用户指派角色。其中 1 和 2 是由 IdentityInterface::getId() 返回的id （译者注：user表的id）
        // 通常在你的 User 模型中实现这个函数。
        $auth->assign($author, 1);
        $auth->assign($admin, 1);
    }

    /**
     *我的权限
     */
    public function actionIndex(){
        return $this->render('myauth');
    }
}