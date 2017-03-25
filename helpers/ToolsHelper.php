<?php
/**
 *工具助手
 * @author gaoxiangdong<gxd_dnjlw@163.com>
 * @copyright 2017-03-22
 */

namespace app\helpers;

use Yii;

/**
 *
 */
class ToolsHelper
{
  /**
   * 项目全局自定义函数库：驼峰命名法
   * 本文件仅存放项目通用的一些工具函数，项目私有函数，请另建文件存放
   * 追加函数，请务必保持统一注释风格，注释中要包含 函数功能、参数、返回值类型
   * 分类导航：转换类、工具类、排序类、算法类、过滤类、拼割类
   */

  /**
   * 2015-4-16
   *
   * @author gaoxiangdong
   * @version V1.0.0
   */

  /* * *********************************************************************** */
  /* * ************************** ********************************* */
  /* * ************************* 工具类 ******************************** */
  /* * ************************** ********************************* */
  /* * *********************************************************************** */

  /*
   * ************************************************************
   *
   * 项目密码的加密
   * 备注：在此处可以修改加密规则，如果需要账号和密码进行关联，请传入账号，不需要可以不传(此方法不可逆转)
   * @param string $password 要加密的密码
   * @param string $username 用户账号
   * @param string $string 干扰字符串
   * @return string 返回md5加密后的字符串
   * @access public
   *
   * ***********************************************************
   */
  public static function setPwd($password, $username = '', $string = 'ganrao')
  {
      return empty($username) ? 
             md5($string . $password) : 
             md5($username . md5($string . $password));
  }

  /*
   * ************************************************************
   *
   * 项目密码的加密
   * 备注：在此处可以修改加密规则，如果需要账号和密码进行关联，请传入账号，不需要可以不传(此方法可以逆转)
   * @param string $password 要加密的密码
   * @param string $username 用户账号
   * @param string $string 干扰字符串
   * @return string 返回md5加密后的字符串
   * @access public
   *
   * ***********************************************************
   */
  public static function encodePwd($password, $username = '', $string = 'ganrao')
  {
      return empty($username) ? 
             base64_encode($string . $password) : 
             base64_encode($username . base64_encode($string . $password));
  }

  /*
   * ************************************************************
   *
   * 项目密码的解密
   * 备注：在此处可以修改加密规则，如果需要账号和密码进行关联，请传入账号，不需要可以不传(此方法可以逆转)
   * @param string $encodepassword 要解密的密码串
   * @param string $username 用户账号
   * @param string $string 干扰字符串
   * @return string 返回md5加密后的字符串
   * @access public
   *
   * ***********************************************************
   */
  public static function decodePwd($encodepassword, $username = '', $string = 'ganrao')
  {
      return empty($username) ? 
             substr(base64_decode($encodepassword),strlen($string)) : 
             substr(base64_decode(substr(base64_decode($encodepassword),strlen($username))),$strlen($string));
  }

  /*
   * ************************************************************
   *
   * 个人密码加密小函数
   * 备注：在此处可以修改加密规则
   * @param string $str 要加密的密码
   * @param integer $type 默认1：数字和小写字母 2：数字和大小写字母 3：混合类型（数字、大小写字母、字符）
   * @return string 返回加密后的字符串
   * @access public
   *
   * ***********************************************************
   */
  public static function setGaoPwd($str, $type = 1)
  {
      $len = strlen($str);
      $newstr = '';
      for ($i = 0; $i < $len; $i ++) {
          switch ($str{$i}) {
              case 'a':
                  if ($type == 3) {
                      $newstr .= '!';
                  } else {
                      $newstr .= '1';
                  }
                  break;
              case 'b':
                  if ($type == 3) {
                      $newstr .= '#';
                  } elseif ($type = 2) {
                      $newstr .= 'B';
                  } else {
                      $newstr .= '2';
                  }
                  break;
              case 'c':
                  if ($type == 3) {
                      $newstr .= '$';
                  } else {
                      $newstr .= '3';
                  }
                  break;
              case 'd':
                  if ($type == 3) {
                      $newstr .= '%';
                  } elseif ($type == 2) {
                      $newstr .= 'D';
                  } else {
                      $newstr .= '4';
                  }
                  break;
              case 'e':
                  if ($type == 3) {
                      $newstr .= '@';
                  } else {
                      $newstr .= '5';
                  }
                  break;
              case 'f':
                  if ($type == 3) {
                      $newstr .= '^';
                  } elseif ($type == 2) {
                      $newstr .= 'F';
                  } else {
                      $newstr .= '6';
                  }
                  break;
              case 'g':
                  if ($type == 3) {
                      $newstr .= '&';
                  } else {
                      $newstr .= '7';
                  }
                  break;
              case 'h':
                  if ($type == 3) {
                      $newstr .= '*';
                  } elseif ($type == 2) {
                      $newstr .= 'H';
                  } else {
                      $newstr .= '8';
                  }
                  break;
              case 'i':
                  if ($type == 3) {
                      $newstr .= '_';
                  } else {
                      $newstr .= '9';
                  }
                  break;
              case '1':
                  if ($type == 2) {
                      $newstr .= 'A';
                  } else {
                      $newstr .= 'a';
                  }

                  break;
              case '2':
                  $newstr .= 'b';
                  break;
              case '3':
                  if ($type == 2) {
                      $newstr .= 'C';
                  } else {
                      $newstr .= 'c';
                  }
                  break;
              case '4':
                  $newstr .= 'd';
                  break;
              case '5':
                  if ($type == 2) {
                      $newstr .= 'E';
                  } else {
                      $newstr .= 'e';
                  }
                  break;
              case '6':
                  $newstr .= 'f';
                  break;
              case '7':
                  if ($type == 2) {
                      $newstr .= 'G';
                  } else {
                      $newstr .= 'g';
                  }
                  break;
              case '8':
                  $newstr .= 'h';
                  break;
              case '9':
                  if ($type == 2) {
                      $newstr .= 'I';
                  } else {
                      $newstr .= 'i';
                  }
                  break;
              default:
                  $newstr .= $str{$i};
          }
      }
      return $newstr;
  }

  /*
   * ************************************************************
   *
   * yii框架 session封装操作
   * @param string $param session键名
   * @param string $value session键值
   * @return null
   * @access public
   *
   * ***********************************************************
   */
  public static function getYS($param)
  {
      return Yii::app()->session[$param];
  }

  public static function setYS($param, $value)
  {
      Yii::app()->session[$param] = $value;
  }

  public static function unsetYS($param)
  {
      unset(Yii::app()->session[$param]);
  }

  /*
   * ************************************************************
   *
   * yii框架 cookie封装操作
   * @param string $param cookie键名
   * @param string $value cookie键值
   * @param inter $expire cookie有效期（默认为一周）
   * @return null
   * @access public
   *
   * ***********************************************************
   */
  public static function getYC($param)
  {
      $cookie = Yii::app()->request->getCookies();
      return $cookie[$param]->value;
  }

  public static function setYC($param, $value, $expire = 604800)
  {
      $cookie = new CHttpCookie($param, json_encode($value));
      $cookie->expire = time() + $expire; // 有限期30天
      Yii::app()->request->cookies[$param] = $cookie;
  }

  public static function unsetYC($param)
  {
      $cookie = Yii::app()->request->getCookies();
      unset($cookie[$param]);
  }

  /*
   * ************************************************************
   *
   * yii框架 获取参数
   * @param string $param 要获取的参数名
   * @param string $mode 要获取的类型1：get参数 2：post参数 3：get和post参数 4:配置文件中
   * @param string $default 默认值
   * @param string $type 强转类型
   * @return mix
   * @access public
   *
   * ***********************************************************
   */
  public static function getYP($param,$mode = 3,$default=null,$type=null)
  {
      $res=null;
      if ($mode == 1) {
          $res=Yii::$app->request->get($param,$default);
      } elseif ($mode == 2) {
          $res=Yii::$app->request->post($param,$default);
      } elseif($mode == 4){
          /**
           *@todo Yii::$app->params可能不让放isset里，回头检查一下
           */
          $res=isset(Yii::$app->params[$param]) ? Yii::$app->params[$param] : $default;
      }else{
          $get=Yii::$app->request->get($param);
          $post=Yii::$app->request->post($param);
          $res=$post ? $post : $get ? $get : $default;
      }

      if($res && $type){
        if(strtolower($type)=='int' || strtolower($type)=='integer'){
          return $res*1;
        }elseif(strtolower($type) == 'str' || strtolower($type) == 'string'){
          return (string)$res;
        }
      }
      return $res;
  }

  /*
   * ************************************************************
   *
   * yii框架 获取参数数组
   * @param string $mode 要获取的类型1：get参数 2：post参数 3：get和post参数 4:配置文件中
   * @return array
   * @access public
   *
   * ***********************************************************
   */
  public static function getYPS($mode = 3)
  {
      $res=[];
      if ($mode == 1) {
          $res=Yii::$app->request->get();
      } elseif ($mode == 2) {
          $res=Yii::$app->request->post();
      } elseif($mode == 4){
          $res=Yii::$app->params;
      }else{
          $get=Yii::$app->request->get();
          $post=Yii::$app->request->post();
          $res=array_merge($get,$post);
      }
      return $res;
  }

  public static function getImage($url, $filename = '', $type = 0)
  {
      if ($url == '') {
          return false;
      }
      if ($filename == '') {
          $ext = strrchr($url, '.');
          if ($ext != '.gif' && $ext != '.jpg') {
              return false;
          }
          $filename = time() . $ext;
      }
      // 文件保存路径
      if ($type) {
          $ch = curl_init();
          $timeout = 5;
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
          $img = curl_exec($ch);
          curl_close($ch);
      } else {
          ob_start();
          readfile($url);
          $img = ob_get_contents();
          ob_end_clean();
      }
      $size = strlen($img);
      // 文件大小
      $fp2 = @fopen($filename, 'a');
      fwrite($fp2, $img);
      fclose($fp2);
      return $filename;
  }

  /*
   * ************************************************************
   *
   * 获取访问者设备类型（待检测）
   * @return string 返回访问者的类型
   * @access public
   *
   * ***********************************************************
   */
  public static function getBrowserStyle()
  {
      if (stripos($_SERVER['HTTP_USER_AGENT'], "android") != false) {
          return 'android';
      } elseif (stripos($_SERVER['HTTP_USER_AGENT'], "ios") != false) {
          return 'ios';
      } elseif (stripos($_SERVER['HTTP_USER_AGENT'], "wp") != false) {
          return 'wp';
      } else {
          return 'pc';
      }
  }

  /* * *******************************************************我是华丽的分割线********************************************************** */
  /* * *******************************************************我是华丽的分割线********************************************************** */
  /* * *******************************************************我是华丽的分割线********************************************************** */

  /* * *********************************************************************** */
  /* * ************************** ********************************* */
  /* * ************************* 排序类 ******************************** */
  /* * ************************** ********************************* */
  /* * *********************************************************************** */

  /*
   * ************************************************************
   * @todo 准备放入数组类里
   * 多维数组按某个键值进行排序
   * @param type $arr：多维数组
   * @param type $keys：参考排序的键值
   * @param type $type：排序类型 asc升序 desc降序
   * @return type array数组类型
   * @access public
   *
   * ***********************************************************
   */
  public static function setArrSort($arr, $keys, $type = 'asc')
  {
      $keysvalue = $new_array = array();
      foreach ($arr as $k => $v) {
          $keysvalue[$k] = $v[$keys];
      }

      if ($type == 'asc') {
          asort($keysvalue);
      } else {
          arsort($keysvalue);
      }

      reset($keysvalue);

      foreach ($keysvalue as $k => $v) {
          $new_array[] = $arr[$k];
      }

      return $new_array;
  }
}
