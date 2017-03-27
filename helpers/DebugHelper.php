<?php
/**
 *调试助手
 * @author gaoxiangdong<gxd_dnjlw@163.com>
 * @copyright 2017-03-22
 */

namespace app\helpers;

use Yii;
use app\helpers\ToolsHelper;
use yii\helpers\ArrayHelper;

/**
 *
 */
class DebugHelper
{
  private  $STARTTIME=0;//毫秒级时间戳
  private  $ENDTIME=0;//毫秒级时间戳
  private  $_debug=array();//调试数据
  private  static $debugObj;//debug单例

  private function __construct(){}

  public static function getInstance(){
    if(!self::$debugObj){
      self::$debugObj=new self();
    }
    return self::$debugObj;
  }
  
  /**
   *添加debug
   */	
  public function addDebug($value){
    $this->_debug[]=$value;
    return $this;
  }

  /**
   *获取debug
   */ 
  public function getDebug(){
    if($this->validateDebug()){
      ArrayHelper::multisort($this->_debug, 'timestamp', SORT_ASC);
      return $this->_debug;
    }
    return [];
  }

  /**
   *校验debug
   */
  public function validateDebug(){
  	$debug=ToolsHelper::getYP('debug');
    $debugConfig=ToolsHelper::getYP('debugConfig',4);

    if($debug && ToolsHelper::decodePwd($debug,'',$debugConfig['debugName']) == $debugConfig['debugKey']){
      return true;
    }
    return false;
  }
}
