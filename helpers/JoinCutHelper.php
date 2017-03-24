<?php
/**
 *拼接和切割助手
 * @author gaoxiangdong<gxd_dnjlw@163.com>
 * @copyright 2017-03-22
 */

namespace app\helpers;

use Yii;

/**
 *
 */
class JoinCutHelper
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
  /* * ************************* 拼割类 ******************************** */
  /* * ************************** ********************************* */
  /* * *********************************************************************** */

  /*
   * ************************************************************
   *
   * 将本地图片字符串处理为完整的图片数组(待验证)
   * @param string $string 图片字符串
   * @param string $type 对单张图片处理模式 默认1：字符串模式返回 2：数组模式返回
   * @return mixed 返回补全的链接信息，可能是数组或者字符串
   * @access public
   *
   * ***********************************************************
   */
  public static setPicUrl($string, $type = '1')
  {
      /**
       * 例如 $string='uploads/Pic/1.jpg,uploads/Pic/2.jpg,uploads/Pic/3.jpg,uploads/Pic/4.jpg'
       * 返回 $arr[0]='http://www.tongchenghui123.com/uploads/Pic/1.jpg';
       * $arr[1]='http://www.tongchenghui123.com/uploads/Pic/2.jpg';
       * $arr[2]='http://www.tongchenghui123.com/uploads/Pic/3.jpg';
       * $arr[3]='http://www.tongchenghui123.com/uploads/Pic/4.jpg';
       */
      $arr = explode(',', trim($string));
      if (! empty($string)) {
          if (count($arr) > 1) {
              foreach ($arr as $key => $value) {
                  $arr[$key] = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $arr[$key];
              }
          } else {
              if ($type == '2') {
                  $arr[0] = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $arr[0];
              } else {
                  $arr = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $arr[0];
              }
          }
      } else {
          $arr = array();
      }
      return $arr;
  }

  /*
   * ************************************************************
   *
   * 截取指定长度的中英文混合字符串
   * @param string $str 等待截取的字符串
   * @param string $start 开始截取位置
   * @param string $length 截取的长度：不管是中文还是英文，都是一个长度
   * @param string $charset 字符编码
   * @param string $suffix 是否带省略号：true带省略，false不带省略符
   * @return $length 返回新的字符串
   * @access public
   *
   * ***********************************************************
   */
  public static msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true)
  {
      if (function_exists("mb_substr")) {
          if ($suffix)
              return mb_substr($str, $start, $length, $charset) . "…";
          return mb_substr($str, $start, $length, $charset);
      } elseif (function_exists('iconv_substr')) {
          if ($suffix)
              return iconv_substr($str, $start, $length, $charset) . "…";
          return iconv_substr($str, $start, $length, $charset);
      }
      $re['utf-8'] = "/[x01-x7f]|[xc2-xdf][x80-xbf]|[xe0-xef][x80-xbf]{2}|[xf0-xff][x80-xbf]{3}/";
      $re['gb2312'] = "/[x01-x7f]|[xb0-xf7][xa0-xfe]/";
      $re['gbk'] = "/[x01-x7f]|[x81-xfe][x40-xfe]/";
      $re['big5'] = "/[x01-x7f]|[x81-xfe]([x40-x7e]|xa1-xfe])/";
      preg_match_all($re[$charset], $str, $match);
      $slice = join("", array_slice($match[0], $start, $length));
      if ($suffix)
          return $slice . "…";
      return $slice;
  }

  /*
   * ************************************************************
   *
   * 从用符号拼接的长字符串中删除某个短字符串
   * @param string $oldStr 老字符串
   * @param string $delStr 要删除的字符串
   * @param string $splitstring 分割符，不支持字符串
   * @return string 返回新的字符串
   * @access public
   *
   * ***********************************************************
   */
  public static delString($oldStr, $delStr, $splitstring)
  {
      /**
       * 例如：$oldStr="uploads/Pic/1.jpg,uploads/Pic/2.jpg,uploads/Pic/3.jpg,uploads/Pic/4.jpg"
       * $delStr="uploads/Pic/1.jpg"
       * $newStr="uploads/Pic/2.jpg,uploads/Pic/3.jpg,uploads/Pic/4.jpg"
       */
      $arr = explode($splitstring, trim($oldStr, $splitstring));
      foreach ($arr as $k => $v) {
          if ($v == $delStr) {
              unset($arr[$k]);
          }
      }
      $newStr = implode($splitstring, $arr);

      return $newStr;
  }

  /*
   * ************************************************************
   *
   * 获取百分比数字（设置小数位数）
   * @param string $oldnum 待切割的数字
   * @param string $num 要保留的小数位数
   * @param int $type 返回的形式默认1：小数 2：百分比(82.35%算4位小数)
   * @return number 返回新的数字
   * @access public
   *
   * ***********************************************************
   */
  public static cutNumber($oldnum, $num, $type = 1)
  {
      $multnum = pow(10, floor($num));
      $newnum = floor($oldnum * $multnum) / $multnum;
      if ($type == 2) {
          $newnum = $newnum * 100;
      }

      return $newnum;
  }


  /*
   * ************************************************************
   *
   * 把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
   * @param array $para 需要拼接的数组
   * @param int $type 是否对字符串做编码 0：不做处理  1：对字符串进行 urlencode编码
   * @return string 拼接完成以后的字符串
   * @access public
   *
   * ***********************************************************
   */
  public static gxdCreateLink($para,$type=0) {
      $arg  = "";

      if($type=1){
          while (list ($key, $val) = each ($para)) {
              $arg.=$key."=".urlencode($val)."&";
          }
      }else{
          while (list ($key, $val) = each ($para)) {
              $arg.=$key."=".$val."&";
          }
      }

      //去掉最后一个&字符
      $arg = substr($arg,0,count($arg)-2);

      //如果存在转义字符，那么去掉转义
      if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}

      return $arg;
  }


  /*
   * ************************************************************
   *
   * 把多维数组拼接成字符串，并用特定符号  替代 符号 "
   * @param array $multiarr 需要拼接的多维数组
   * @param string $str 替换 " 的字符串或者符号
   * @return string 拼接完成以后的字符串
   * @access public
   *
   * ***********************************************************
   */
  public static gxdCreateMultiLink($multiarr,$str ="#") {
      $arg=str_replace('"',$str,json_encode($multiarr));
      return $arg;
  }
}
