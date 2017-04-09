<?php
/**
 *通讯类
 * @author gaoxiangdong<gxd_dnjlw@163.com>
 * @copyright 2017-03-22
 */
namespace app\components;

use yii\base\Component;
use app\helpers\DebugHelper;

class HttpUtil extends BaseComponent
{
    public static $errorMsg=null;//curl报错信息

    /*
   * ************************************************************
   *
   * CURL 请求（如果要监控，必须调用这个）
   * @param string $url 请求的链接
   * @return mixed json字符串 或 false
   * @access public
   *
   * ***********************************************************
   */
    public static function curl($url,$params,$method='GET'){
        $timestamp=microtime(true)*10000;
        $starttime=microtime(true);
        if($method == 'POST'){
            $res=self::http_post($url,$params);
        }else{
            $sendurl=$url.'?'.http_build_query(array_map('urlencode',$params));
            $res=self::http_get($sendurl);
        }
        $endtime=microtime(true);
        
        $data=[
            'http_action'  => $method,
            'http_url'     => $url,
            'http_params'  => $params,
            'http_result'  => $res,
            'http_extension'  => [
                'errmsg'=>self::$errorMsg,
            ],
            'http_runtime' => $endtime - $starttime,
            'timestamp'     => $timestamp,
        ];
        DebugHelper::getInstance()->addDebug($data);
    }

    /*
   * ************************************************************
   *
   * CURL get请求
   * @param string $url 请求的链接
   * @return mixed json字符串 或 false
   * @access public
   *
   * ***********************************************************
   */
  public static http_get($url)
  {
      $oCurl = curl_init();
      if (stripos($url, "https://") !== FALSE) {
          curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
          curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
          curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); // CURL_SSLVERSION_TLSv1
      }
      curl_setopt($oCurl, CURLOPT_URL, $url);
      curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
      $sContent = curl_exec($oCurl);
      $aStatus = curl_getinfo($oCurl);
      $errorMsg=curl_error($oCurl);
      curl_close($oCurl);
      if (intval($aStatus["http_code"]) == 200) {
          return $sContent;
      } else {
          self::$errorMsg=$errorMsg;
          return false;
      }
  }

  /*
   * ************************************************************
   *
   * CURL post请求
   * @param string $url 请求的链接
   * @param mixed $param 要传的参数（字符串或者数组）
   * @param boolean $post_file 控制是否将数组参数保持原样，true:保留数组  false:将数组拼接
   * @return mixed json字符串 或 false
   * @access public
   *
   * ***********************************************************
   */
  public static http_post($url, $param, $post_file = false)
  {
      $oCurl = curl_init();
      if (stripos($url, "https://") !== FALSE) {
          curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
          curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
          curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); // CURL_SSLVERSION_TLSv1
      }
      if (is_string($param) || $post_file) {
          $strPOST = $param;
      } else {
          $aPOST = array();
          foreach ($param as $key => $val) {
              $aPOST[] = $key . "=" . urlencode($val);
          }
          $strPOST = join("&", $aPOST);
      }
      curl_setopt($oCurl, CURLOPT_URL, $url);
      curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($oCurl, CURLOPT_POST, true);
      // curl_setopt($oCurl, CURLOPT_HEADER, 0);
      curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
      $sContent = curl_exec($oCurl);
      $aStatus = curl_getinfo($oCurl);
      $errorMsg=curl_error($oCurl);
      curl_close($oCurl);
      if (intval($aStatus["http_code"]) == 200) {
          return $sContent;
      } else {
          self::$errorMsg=$errorMsg;
          return false;
      }
  }
}