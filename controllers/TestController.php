<?php

namespace app\controllers;

use Yii;
use app\models\Test;
use app\components\BaseController;

/**
 * CountryController implements the CRUD actions for Country model.
 */
class TestController extends BaseController
{
    public function actionTestLog(){
        echo '开始';
        Yii::trace('trace开始',__METHOD__);
        Yii::info('这就是个info',__METHOD__);
        Yii::warning('开始提醒了',__METHOD__);
        Yii::error('有点严重了',__METHOD__);
        echo '结束';
        $log=Yii::getFileTarget();
        var_dump($log);

        $time = microtime(true);   
        $log=new \yii\log\FileTarget();
        $log->logFile = Yii::$app->getRuntimePath() . '/logs/test.log'; 
        $log->messages[] = ['test',1,'test',$time];
        var_dump($log);
        $log->export();   
        die;
    }

    public function actionTestEvent(){
        $model=new Test();
        $model->on(Test::EVENT_HELLO,[$this,'event1'],array('wo'=>'bu fu'),false);
        $model->off(Test::EVENT_HELLO,[$model,'say']);
        $model->on(Test::EVENT_HELLO,function($event){
            echo $event->data;
        },'gun du zi');
        $model->trigger(Test::EVENT_HELLO);
    }

    public function event1($event){
        echo json_encode($event->data);
    }
}
