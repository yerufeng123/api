<?php
namespace app\components;

use yii\base\Component;
use yii\web\Response;

class BaseComponent extends Component
{

    /**
    * 1开头是系统错误码，业务错误码，从2开始
    * 10000 操作已完成
    * 10001 操作失败
    * 10002 校验错误
    * 10003 请求的必要参数缺失
    * 10004 请求参数长度超出限制
    */
    public function returnSuc($code=10000,$msg='success',$data=[],$format='json',$header=[]){

    }

    public function returnErr($code,$msg,$data=[],$format='json',$header=[]){
    	$this->_return($code,$msg,$data,$format,$header);
    }

    private function _return($code,$msg,$data,$format,$header){
    	$response=Yii::$app->response;
    	$response->format=$this->_getFormat($format);
    	$response->data=[
    		'code'  => $code,
    		'msg'   => $msg,
    		'data'  => $data,
    	];
    	foreach ($header as $key => $value) {
    		$response->headers->set($key,$value);
    	}

    }

    private function _getFormat($format){
    	
    	switch ($format) {
    		case 'html':
    			$formatparam=Response::FORMAT_HTML;
    			break;
    		case 'jsonp':
    			$formatparam=Response::FORMAT_JSONP;
    			break;
    		case 'xml':
    			$formatparam=Response::FORMAT_XML;
    			break;
    		case 'raw':
    			$formatparam=Response::FORMAT_RAW;
    		default:
    			$formatparam=Response::FORMAT_JSON;
    			break;
    	}
    	return $formatparam;
    }
}