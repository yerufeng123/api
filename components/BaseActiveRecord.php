<?php
namespace app\components;

use yii\db\ActiveRecord;
use yii\web\Response;
use app\helpers\DebugHelper;

/**
 *AR模型基类
 * @author gaoxiangdong<gxd_dnjlw@163.com>
 * @copyright 2017-03-19
 */

class BaseActiveRecord extends ActiveRecord
{
    /**
    * 1开头是系统错误码，业务错误码，从2开始
    * 10000 操作已完成
    * 10001 未知错误
    */
    public function returnSuc($data,$code=10000,$msg='success'){
    	return $this->_return($code,$msg,$data);
    }

    public function returnErr($code,$msg,$data=[]){
    	return $this->_return($code,$msg,$data);
    }

    private function _return($code,$msg,$data){
    	$data=[
    		'code'  => $code,
    		'msg'   => $msg,
    		'data'  => $data,
    	];
    	return $data;
    }
}
