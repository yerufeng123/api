<?php
namespace app\components;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\helpers\DebugHelper;
use app\helpers\ToolsHelper;

/**
 *控制器基类
 * @author gaoxiangdong<gxd_dnjlw@163.com>
 * @copyright 2017-03-19
 */

class BaseController extends Controller
{
	private $operate_starttime=0;
	private $operate_endtime=0;
	private $operate_params=[];
	private $operate_data=[];
	private $response;

	public function beforeAction($action){
		if(DebugHelper::getInstance()->validateDebug()){
			$this->operate_starttime=microtime(true);
			$this->operate_params=ToolsHelper::getYPS();
		}
		$this->response=Yii::$app->response;
        return parent::beforeAction($action);
	}

	public function afterAction($action,$result){
		if(DebugHelper::getInstance()->validateDebug()){
			$this->operate_endtime=microtime(true);
			//添加debug
	    	$data=[
	    		'operate_module'     => Yii::$app->controller->module->id,
	    		'operate_controller'     => Yii::$app->controller->id,
	            'operate_action'  => Yii::$app->controller->action->id,
	            'operate_params'  => $operate_params,
	            'operate_result'  => $operate_data,
	            'operate_extension'  => [],
	            'operate_runtime' => $this->operate_endtime - $this->operate_starttime,
	        ];
	        DebugHelper::getInstance()->addDebug($data);
	        $this->response->data['debug']=DebugHelper::getInstance()->getDebug();
		}
        return parent::afterAction($action,$result);
	}
	/**
    * 1开头是系统错误码，业务错误码，从2开始
    * 10000 操作已完成
    * 10001 未知错误
    */
    public function returnSuc($data,$code=10000,$msg='success',$format='json',$header=[]){
    	$this->_return($code,$msg,$data,$format,$header);
    }

    public function returnErr($code,$msg,$data=[],$format='json',$header=[]){
    	$this->_return($code,$msg,$data,$format,$header);
    }

    private function _return($code,$msg,$data,$format,$header){
    	$this->response->format=$this->_getFormat($format);
    	$this->response->data=[
    		'code'  => $code,
    		'msg'   => $msg,
    		'data'  => $data,
    	];
    	foreach ($header as $key => $value) {
    		$this->response->headers->set($key,$value);
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
