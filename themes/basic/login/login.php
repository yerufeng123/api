<?php
use yii\helpers\Html;
use yii\web\JqueryAsset;
use app\themes\basic\BackgroundAsset;


/* @var $this \yii\web\View */
/* @var $content string */

$this->title='登录页';
JqueryAsset::register($this);
BackgroundAsset::register($this);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
	<meta charset="<?= Yii::$app->charset ?>">
	<title><?= Html::encode($this->title)?></title>
	<!--[if IE 9]>
	    <link rel="stylesheet" media="screen" href="<?= Yii::getAlias('@basic') ?>/css/style.ie9.css"/>
	<![endif]-->
	<!--[if IE 8]>
	    <link rel="stylesheet" media="screen" href="<?= Yii::getAlias('@basic') ?>/css/style.ie8.css"/>
	<![endif]-->
	<!--[if lt IE 9]>
		<script src="<?= Yii::getAlias('@basic') ?>/js/plugins/css3-mediaqueries.js"></script>
	<![endif]-->
	<?php $this->head() ?>
	</head>
<body class="loginpage">
	<?php $this->beginBody() ?>
	<div class="loginbox">
    	<div class="loginboxinner">
        	
            <div class="logo">
            	<h1 class="logo">Ama.<span>Admin</span></h1>
				<span class="slogan">后台管理系统</span>
            </div><!--logo-->
            
            <br clear="all" /><br />
            
            <div class="nousername">
				<div class="loginmsg">密码不正确.</div>
            </div><!--nousername-->
            
            <div class="nopassword">
				<div class="loginmsg">密码不正确.</div>
                <div class="loginf">
                    <div class="thumb"><img alt="" src="<?= Yii::getAlias('@basic') ?>/images/thumbs/avatar1.png" /></div>
                    <div class="userlogged">
                        <h4></h4>
                        <a href="index.html">Not <span></span>?</a> 
                    </div>
                </div><!--loginf-->
            </div><!--nopassword-->
            
            <form id="login" action="dashboard.html" method="post">
            	
                <div class="username">
                	<div class="usernameinner">
                    	<input type="text" name="username" id="username" />
                    </div>
                </div>
                
                <div class="password">
                	<div class="passwordinner">
                    	<input type="password" name="password" id="password" />
                    </div>
                </div>
                
                <button>登录</button>
                
                <div class="keep"><input type="checkbox" /> 记住密码</div>
            
            </form>
            
        </div><!--loginboxinner-->
    </div><!--loginbox-->
	<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage();?>

