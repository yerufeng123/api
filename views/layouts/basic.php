<?php
use yii\helpers\Html;
use app\themes\basic\BackgroundAsset;


/* @var $this \yii\web\View */
/* @var $content string */
BackgroundAsset::register($this);
$this->registerCssFile("@basic/css/style.ie9.css",['condition' => 'IE9']);
$this->registerCssFile("@basic/css/style.ie8.css",['condition' => 'IE8']);
?>

<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
<meta charset="<?= Yii::$app->charset ?>">
<title><?= Html::encode($this->title)?></title>
<?php $this->head() ?>
</head>
<?php $this->beginBody() ?>
<?= $content ?>
<?php $this->endBody() ?>
</html>
<?php $this->endPage();?>

