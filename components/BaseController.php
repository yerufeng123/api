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
	private $operate_starttime=0;//debug开始时间
	private $operate_endtime=0;//debug结束时间
    private $operate_timestamp=0;//该操作执行时间，用于排序
	private $operate_params=[];//debug参数
	private $operate_data=[];//debug数据
	private $response;
    private $model;//代理模型

	public function beforeAction($action){
        $this->response=Yii::$app->response;
		if(DebugHelper::getInstance()->validateDebug()){
            $this->operate_timestamp=microtime(true)*10000;
			$this->operate_starttime=microtime(true);
			$this->operate_params=ToolsHelper::getYPS();
            unset($this->operate_params['debug']);
            $this->response->format=Response::FORMAT_JSON;
		}
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
	            'operate_params'  => $this->operate_params,
	            'operate_result'  => $this->operate_data,
	            'operate_extension'  => [],
	            'operate_runtime' => $this->operate_endtime - $this->operate_starttime,
                'timestamp'     => $this->operate_timestamp,
	        ];
	        $this->response->data['debug']=DebugHelper::getInstance()->addDebug($data)->getDebug();
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

    //覆写render方法，添加debug
    public function render($view, $params = [])
    {
        if(!DebugHelper::getInstance()->validateDebug()){
            return parent::render($view, $params);
        }
    }

    //覆写renderPartial方法，添加debug
    public function renderPartial($view, $params = [])
    {
        if(!DebugHelper::getInstance()->validateDebug()){
            return parent::renderPartial($view, $params);
        }
    }

    //获取Model模型
    public function setModel($modelobj){
        $this->model= $modelobj;
        return $this;
    }

    //代理方法
    public function __call($name,$array){
        $timestamp=microtime(true)*10000;
        $starttime=microtime(true);
        if(!is_callable([$this->model,$name])){
            return null;//返回这个表示内部模型名称对象不正确，不能正常实例化
        }
        try {
            $res=call_user_func_array(array($this->model,$name), $array);
            /**
             *@todo 下列捕捉yii 错误的方式是否兼容yii
             */
        } catch (\Exception $e) {
            $res = false;
        }
        $endtime=microtime(true);
        if(DebugHelper::getInstance()->validateDebug()){
            //添加debug
            $data=[
                'model_action'  => $name,
                'model_params'  => $array,
                'model_result'  => $res,
                'model_extension'  => [],
                'model_runtime' => $endtime - $starttime,
                'timestamp'     => $timestamp,
            ];
            $this->response->data['debug']=DebugHelper::getInstance()->addDebug($data)->getDebug();
        }
        return $res;
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
