<?php
/**
 *转换助手
 * @author gaoxiangdong<gxd_dnjlw@163.com>
 * @copyright 2017-03-22
 */

namespace app\helpers;

use Yii;

/**
 *
 */
class TransformHelper
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
  /* * ************************* 转换类 ******************************** */
  /* * ************************** ********************************* */
  /* * *********************************************************************** */

  /*
   * ************************************************************
   *
   * 使用特定function对数组中所有元素做处理
   * @param string &$array 要处理的字符串
   * @param string $function 要执行的函数
   * @return boolean $apply_to_keys_also 是否也应用到key上
   * @access private(内部调用)
   *
   * ***********************************************************
   */
  public static arrayRecursive(&$array, $function, $apply_to_keys_also = false)
  {
      static $recursive_counter = 0;
      if (++ $recursive_counter > 1000) {
          die('possible deep recursion attack');
      }
      foreach ($array as $key => $value) {
          if (is_array($value)) {
              arrayRecursive($array[$key], $function, $apply_to_keys_also);
          } else {
              $array[$key] = $function($value);
          }

          if ($apply_to_keys_also && is_string($key)) {
              $new_key = $function($key);
              if ($new_key != $key) {
                  $array[$new_key] = $array[$key];
                  unset($array[$key]);
              }
          }
      }
      $recursive_counter --;
  }

  /*
   * ************************************************************
   *
   * 将数组转换为JSON字符串（兼容中文）
   * @param array $array 要转换的数组
   * @return string 转换得到的json字符串
   * @access public
   *
   * ***********************************************************
   */
  public static TJSON($array)
  {
      arrayRecursive($array, 'urlencode', true);
      $json = json_encode($array);
      return urldecode($json);
  }

  /*
   * ************************************************************
   *
   * 将JSON转换为数组
   * @param string $web 要转换的json字符串
   * @return array 转换得到的数组
   * @access public
   *
   * ***********************************************************
   */
  public static TARRAY($web)
  {
      $arr = array();
      foreach ($web as $k => $w) {
          if (is_object($w))
              $arr[$k] = TARRAY($w); // 判断类型是不是object
          else
              $arr[$k] = $w;
      }
      return $arr;
  }

  /*
   * ************************************************************
   *
   * 文本换行转换成 \n(文本PHP_EOL to \n)
   * @param string $password 要转换的字符串
   * @return string 返回转换后的字符串
   * @access public
   *
   * ***********************************************************
   */
  public static eolToN($string)
  {
      return str_replace("\n", '\n', $string);
  }

  /*
   * ************************************************************
   *
   * \n还原成成文本换行 (\n to 文本PHP_EOL)
   * @param string $password 要转换的字符串
   * @return string 返回转换后的字符串
   * @access public
   *
   * ***********************************************************
   */
  public static nToEol($string)
  {
      return str_replace('\n', "\n", $string);
  }

  /*
   * ************************************************************
   *
   * 获取汉字拼音首字母（大写）
   * @param string $str 传入的汉字
   * @return char 返回新的大写字符
   * @access public
   *
   * ***********************************************************
   */
  public static getFirstChar($str)
  {
      if (empty($str)) {
          return '';
      }
      $fchar = ord($str{0});
      if ($fchar >= ord('A') && $fchar <= ord('z'))
          return strtoupper($str{0});
      $s1 = iconv('UTF-8', 'gb2312', $str);
      $s2 = iconv('gb2312', 'UTF-8', $s1);
      $s = $s2 == $str ? $s1 : $str;
      $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
      if ($asc >= - 20319 && $asc <= - 20284)
          return 'A';
      if ($asc >= - 20283 && $asc <= - 19776)
          return 'B';
      if ($asc >= - 19775 && $asc <= - 19219)
          return 'C';
      if ($asc >= - 19218 && $asc <= - 18711)
          return 'D';
      if ($asc >= - 18710 && $asc <= - 18527)
          return 'E';
      if ($asc >= - 18526 && $asc <= - 18240)
          return 'F';
      if ($asc >= - 18239 && $asc <= - 17923)
          return 'G';
      if ($asc >= - 17922 && $asc <= - 17418)
          return 'H';
      if ($asc >= - 17417 && $asc <= - 16475)
          return 'J';
      if ($asc >= - 16474 && $asc <= - 16213)
          return 'K';
      if ($asc >= - 16212 && $asc <= - 15641)
          return 'L';
      if ($asc >= - 15640 && $asc <= - 15166)
          return 'M';
      if ($asc >= - 15165 && $asc <= - 14923)
          return 'N';
      if ($asc >= - 14922 && $asc <= - 14915)
          return 'O';
      if ($asc >= - 14914 && $asc <= - 14631)
          return 'P';
      if ($asc >= - 14630 && $asc <= - 14150)
          return 'Q';
      if ($asc >= - 14149 && $asc <= - 14091)
          return 'R';
      if ($asc >= - 14090 && $asc <= - 13319)
          return 'S';
      if ($asc >= - 13318 && $asc <= - 12839)
          return 'T';
      if ($asc >= - 12838 && $asc <= - 12557)
          return 'W';
      if ($asc >= - 12556 && $asc <= - 11848)
          return 'X';
      if ($asc >= - 11847 && $asc <= - 11056)
          return 'Y';
      if ($asc >= - 11055 && $asc <= - 10247)
          return 'Z';
      return null;
  }


  /*
   * ************************************************************
   *
   * 将字符串中指定长度字符替换为指定字符
   * @param string $str 需要处理的字符串
   * @param string $replace 准备替换的字符
   * @param string $start 开始替换位置,0表示起始位置
   * @param string $length 替换长度，为负时，为距离末尾长度
   * @return string 返回新的字符串
   * @access public
   *
   * ***********************************************************
   */
  public static gxdSubstreplace($str,$replace,$start=0,$length=null){
      /**
       * 例：$str='6222022201825647856'
       *    gxdSubstreplace($str,'*',0,-4);
       *    $newstr='***************7856';
       */
      $newstr='';
      $len=strlen($str);//字符串长度
      if(empty($length) && $length != 0){
          $length=len;
      }
      for($i=0;$i<$len;$i++){
          if(($length>0 && $i>=$start && $i<$start+$length) || ($length <0 && $i>=$start && $i<$len+$length)){
              $newstr.=$replace;
          }else{
              $newstr.=$str{$i};
          }
      }
      return $newstr;
  }


  /*
   * ************************************************************
   *
   * 在字符串间隔指定长度处，插入指定字符串
   * @param string $str 需要处理的字符串
   * @param string $replace 准备替换的字符
   * @param string $start 开始替换位置,0表示起始位置
   * @param string $length 替换长度，为负时，为距离末尾长度
   * @return string 返回新的字符串
   * @access public
   *
   * ***********************************************************
   */
  public static gxdInsertstr($str,$insertstr,$step=1){
      /**
       * 例：$str='6222022201825647856'
       *    gxdInsertstr($str,' ',4);
       *    $newstr='6222 0222 0182 5647 856';
       */
      $newstr='';
      $len=strlen($str);//字符串长度
      for($i=0;$i<$len;$i++){
          if($i%$step == '0' && $i != 0){
              $newstr.=$insertstr;
          }
          $newstr.=$str{$i};
      }
      return $newstr;
  }


  /*
   * ************************************************************
   *
   * 转换数字为 钱币 显示模式
   * @param float $number 需要处理的数字(数字默认是元为单位)
   * @param int $model 输出模式 1:正规钱币分割，保留2位小数    2：小数部分全部保留
   * @return string 返回新的钱币模式字符串
   * @access public
   *
   * ***********************************************************
   */
  public static gxdNumToMoney($number,$model=1){
      /**
       * 例：$number=1500000.3354654
       *    gxdNumToMoney($number,2);
       *    $newstr=1,500,000.335,465,4;
       */
      $newstr='';
      $number=$number*1;//强转数字类型
      $number=$number.'';//再次强转为字符串类型
      $num=explode('.', $number);
      $integer=empty($num[0]) ? 0 : $num[0];//整数部分
      $decimal=empty($num[1]) ? 0 : $num[1];//小数部分
      $newinteger=strrev(gxdInsertstr(strrev($integer), ',',3));
      switch ($model){
          case 1:
              $newdecimal=substr($decimal, 0,2);
              break;
          default:
              $newdecimal=gxdInsertstr($decimal, ',',3);
      }
      if($length=strlen($newdecimal) <2){
          for($i=0;$i<2-$length;$i++){
              $newdecimal.='0';
          }
      }
      $newstr=$newinteger.'.'.$newdecimal;
      return $newstr;
  }


  /*
   * ************************************************************
   *
   * 转换多维数组 中的值为 urlencode后的值
   * @param array $array 需要处理的数组
   * @return string 返回新的数组
   * @access public
   *
   * ***********************************************************
   */
  public static urlencodeArray($array){
      $new_data = [];
      foreach($array as $key => $val){
          // 这里我对键也进行了urlencode
          $new_data[$key] = is_array($val) ? self::urlencodeArray($val) : urlencode($val);
      }
      return $new_data;
  }
}
