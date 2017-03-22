<?php
namespace app\components;

use Yii;

/**
 *工具类
 * @author gaoxiangdong<gxd_dnjlw@163.com>
 * @copyright 2017-03-22
 */

class Tools
{
  
  /**
   *
   */	
  public static function returnSuc($data=array()){
    self::returnRes(0,'success',$data);
  }

  public static function returnErr($code,$message){
    self::returnRes($code,$message);
  }

  public static function returnRes($code,$message,$data=array()){
    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    Yii::$app->response->data=[
      'code'=>$code,
      'msg'=>$message,
      'data'=>$data,
    ];
  }
}
