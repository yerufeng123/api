<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\BackgroundAsset;

BackgroundAsset::register($this);
?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <?php $this->head() ?>
</head>
<body>

//这块明天看源码 <?php $this->beginBody() ?>
<?php echo $content;?>
<?php $this->endBody() ?>

<?php $this->head() ?>
<?php $this->beginPage() ?>
<?php $this->endPage();?>


</body>
</html>
