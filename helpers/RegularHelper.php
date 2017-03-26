<?php
/**
 *正则验证助手
 * @author gaoxiangdong<gxd_dnjlw@163.com>
 * @copyright 2017-03-22
 */

namespace app\helpers;

use Yii;

/**
 *
 */
class RegularHelper
{
  /**
   * 项目全局自定义函数库：驼峰命名法
   * 本文件仅存放项目通用的一些工具函数，项目私有函数，请另建文件存放
   * 追加函数，请务必保持统一注释风格，注释中要包含 函数功能、参数、返回值类型
   * 分类导航：验证类、转换类、工具类、排序类、算法类、时间类、过滤类、拼割类
   */

  /**
   * 2015-4-16
   *
   * @author gaoxiangdong
   * @version V1.0.0
   */
  /* * *********************************************************************** */
  /* * ************************** ********************************* */
  /* * ************************* 验证类 ******************************** */
  /* * ************************** ********************************* */
  /* * *********************************************************************** */

  /*
   * ************************************************************
   *
   * 验证手机号是否规范
   * @test 要验证的字符串
   * @return boolean //$result匹配到的字符串数组 【0】是全匹配
   * @access public
   *
   * ***********************************************************
   */
  public static function checkPhone($phone)
  {
      /**
       * 匹配手机号码
       * 规则：
       * 手机号码基本格式：
       * 前面三位为：
       * 移动：134-139 147 150-152 157-159 182 183 187 188
       * 联通：130-132 155-156 185 186
       * 电信：133 153 180 189
       * 其他：177 170
       * 后面八位为：
       * 0-9位的数字
       */
      // $rule = "/^((13[0-9])|147|(15[0-35-9])|180|182|(18[5-9]))[0-9]{8}$/A";
      $rule = "/^((13[0-9])|147|(15[0-9])|(17[0-9])|18[0-9])[0-9]{8}$/A";
      preg_match($rule, $phone, $result);
      if (empty($result)) {
          return false;
      } else {
          return true;
      }
  }

  /*
   * ************************************************************
   *
   * 验证电话号码是否规范
   * @test 要验证的字符串
   * @return boolean //$result匹配到的字符串数组 【0】是全匹配
   * @access public
   *
   * ***********************************************************
   */
  public static function checkTel($test)
  {
      /**
       * 电话号码匹配
       * 电话号码规则：
       * 区号：3到5位，大部分都是四位，北京(010)和上海市(021)三位，西藏有部分五位，可以包裹在括号内也可以没有
       * 如果有区号由括号包裹，则在区号和号码之间可以有0到1个空格，如果区号没有由括号包裹，则区号和号码之间可以有两位长度的 或者-
       * 号码：7到8位的数字
       * 例如：(010) 12345678 或者 (010)12345678 或者 010 12345678 或者 010--12345678
       */
      $rule = '/^(\(((010)|(021)|(0\d{3,4}))\)( ?)([0-9]{7,8}))|((010|021|0\d{3,4}))([- ]{1,2})([0-9]{7,8})$/A';
      preg_match($rule, $test, $result);
      if (empty($result)) {
          return false;
      } else {
          return true;
      }
  }

  /*
   * ************************************************************
   *
   * 验证邮箱是否规范
   * @test 要验证的字符串
   * @return boolean //$result 匹配到的字符串数组 【0】是全匹配
   * @access public
   *
   * ***********************************************************
   */
  public static function checkEmail($test)
  {
      /**
       * 匹配邮箱
       * 规则：
       * 邮箱基本格式是 *****@**.**
       * @以前是一个 大小写的字母或者数字开头，紧跟0到多个大小写字母或者数字或 .
       *
       *
       *
       *
       * _ - 的字符串
       * @之后到.之前是 1到多个大小写字母或者数字的字符串
       * .之后是 1到多个 大小写字母或者数字或者.的字符串
       */
      $zhengze = '/^[a-zA-Z0-9][a-zA-Z0-9._-]*\@[a-zA-Z0-9]+\.[a-zA-Z0-9\.]+$/A';
      preg_match($zhengze, $test, $result);
      if (empty($result)) {
          return false;
      } else {
          return true;
      }
  }

  /*
   * ************************************************************
   *
   * 验证身份证号是否规范
   * @test 要验证的字符串
   * @return boolean //$result 匹配到的字符串数组 【0】是全匹配
   * @access public
   *
   * ***********************************************************
   */
  public static function checkIdentity($test)
  {
      /**
       * 匹配身份证号
       * 规则：
       * 15位纯数字或者18位纯数字或者17位数字加一位x
       */
      $rule = '/^(([0-9]{15})|([0-9]{18})|([0-9]{17}x))$/';
      preg_match($rule, $test, $result);
      if (empty($result)) {
          return false;
      } else {
          return true;
      }
  }

  /*
   * ************************************************************
   *
   * 验证邮编是否规范
   * @test 要验证的字符串
   * @return boolean //$result 匹配到的字符串数组 【0】是全匹配
   * @access public
   *
   * ***********************************************************
   */
  public static function checkZipcode($test)
  {
      /**
       * 匹配邮编
       * 规则：六位数字，第一位不能为0
       */
      $rule = '/^[1-9]\d{5}$/';
      preg_match($rule, $test, $result);
      if (empty($result)) {
          return false;
      } else {
          return true;
      }
  }

  /*
   * ************************************************************
   *
   * 验证IP是否规范
   * @test 要验证的字符串
   * @return boolean //$result 匹配到的字符串数组 【0】是全匹配
   * @access public
   *
   * ***********************************************************
   */
  public static function checkIp($test)
  {
      /**
       * 匹配ip
       * 规则：
       * *1.**2.**3.**4
       * *1可以是一位的 1-9，两位的01-99，三位的001-255
       * *2和**3可以是一位的0-9，两位的00-99,三位的000-255
       * *4可以是一位的 1-9，两位的01-99，三位的001-255
       * 四个参数必须存在
       */
      $rule = '/^((([1-9])|((0[1-9])|([1-9][0-9]))|((00[1-9])|(0[1-9][0-9])|((1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))))\.)((([0-9]{1,2})|(([0-1][0-9]{2})|(2[0-4][0-9])|(25[0-5])))\.){2}(([1-9])|((0[1-9])|([1-9][0-9]))|(00[1-9])|(0[1-9][0-9])|((1[0-9]{2})|(2[0-4][0-9])|(25[0-5])))$/';
      preg_match($rule, $test, $result);
      if (empty($result)) {
          return false;
      } else {
          return true;
      }
  }

  /*
   * ************************************************************
   *
   * 验证时间格式是否规范
   * @test 要验证的字符串
   * @return boolean //$result 匹配到的字符串数组 【0】是全匹配
   * @access public
   *
   * ***********************************************************
   */
  public static function checkTime($test)
  {
      /**
       * 匹配时间
       * 规则：
       * 形式可以为：
       * 年-月-日 小时:分钟:秒
       * 年-月-日 小时:分钟
       * 年-月-日
       * 年：1或2开头的四位数
       * 月：1位1到9的数；0或1开头的两位数，0开头的时候个位数是1到9的数，1开头的时候个位数是1到2的数
       * 日：1位1到9的数；0或1或2或3开头的两位数，0开头的时候个位数是1到9的数，1或2开头的时候个位数是0到9的数，3开头的时候个位数是0或1
       * 小时：0到9的一位数；0或1开头的两位数，个位是0到9；2开头的两位数，个位是0-3
       * 分钟：0到9的一位数；0到5开头的两位数，个位是0到9；
       * 分钟：0到9的一位数；0到5开头的两位数，各位是0到9
       */
      $rule = '/^(([1-2][0-9]{3}-)((([1-9])|(0[1-9])|(1[0-2]))-)((([1-9])|(0[1-9])|([1-2][0-9])|(3[0-1]))))( ((([0-9])|(([0-1][0-9])|(2[0-3]))):(([0-9])|([0-5][0-9]))(:(([0-9])|([0-5][0-9])))?))?$/';
      preg_match($rule, $test, $result);
      if (empty($result)) {
          return false;
      } else {
          return true;
      }
  }

  /*
   * ************************************************************
   *
   * 验证是否是中文
   * @test 要验证的字符串
   * @return boolean //$result 匹配到的字符串数组 【0】是全匹配
   * @access public
   *
   * ***********************************************************
   */
  public static function checkChinese($test)
  {
      // utf8下匹配中文
      $rule = '/([\x{4e00}-\x{9fa5}]){1}/u';
      preg_match_all($rule, $test, $result);
      if (empty($result)) {
          return false;
      } else {
          return true;
      }
  }

  /*
   * ************************************************************
   *
   * 验证url是否规范
   * @test 要验证的字符串
   * @return boolean //$result 匹配到的字符串数组 【0】是全匹配
   * @access public
   *
   * ***********************************************************
   */
  public static function checkUrl($test)
  {
      /**
       * 匹配url
       * url规则：
       * 例
       * 协议://域名（www/tieba/baike...）.名称.后缀/文件路径/文件名
       * http://zhidao.baidu.com/question/535596723.html
       * 协议://域名（www/tieba/baike...）.名称.后缀/文件路径/文件名?参数
       * www.lhrb.com.cn/portal.php?mod=view&aid=7412
       * 协议://域名（www/tieba/baike...）.名称.后缀/文件路径/文件名/参数
       * http://www.xugou.com.cn/yiji/erji/index.php/canshu/11
       *
       * 协议：可有可无，由大小写字母组成；不写协议则不应存在://，否则必须存在://
       * 域名：必须存在，由大小写字母组成
       * 名称：必须存在，字母数字汉字
       * 后缀：必须存在，大小写字母和.组成
       * 文件路径：可有可无，由大小写字母和数字组成
       * 文件名：可有可无，由大小写字母和数字组成
       * 参数:可有可无，存在则必须由?开头，即存在?开头就必须有相应的参数信息
       */
      $rule = '/^(([a-zA-Z]+)(:\/\/))?([a-zA-Z]+)\.(\w+)\.([\w.]+)(\/([\w]+)\/?)*(\/[a-zA-Z0-9]+\.(\w+))*(\/([\w]+)\/?)*(\?(\w+=?[\w]*))*((&?\w+=?[\w]*))*$/';
      preg_match($rule, $test, $result);
      if (empty($result)) {
          return false;
      } else {
          return true;
      }
  }

  /*
   * ************************************************************
   *
   * 验证经度
   * @test 要验证的字符串
   * @return boolean //$result匹配到的字符串数组 【0】是全匹配
   * @access public
   *
   * ***********************************************************
   */
  public static function checkLng($test)
  {
      /**
       * 经度匹配
       * 经度规则：-180到180度
       */
      $rule = '/^(-?((180)|(((1[0-7]\\d)|(\\d{1,2}))(\\.\\d+)?)))$/';
      preg_match($rule, $test, $result);
      if (empty($result)) {
          return false;
      } else {
          return true;
      }
  }

  /*
   * ************************************************************
   *
   * 验证纬度
   * @test 要验证的字符串
   * @return boolean //$result匹配到的字符串数组 【0】是全匹配
   * @access public
   *
   * ***********************************************************
   */
  public static function checkLat($test)
  {
      /**
       * 纬度匹配
       * 纬度规则：-90到90度
       */
      $rule = '/^(-?((90)|((([0-8]\\d)|(\\d{1}))(\\.\\d+)?)))$/';
      preg_match($rule, $test, $result);
      if (empty($result)) {
          return false;
      } else {
          return true;
      }
  }

  /*
   * ************************************************************
   *
   * 验证时间戳
   * @param mixed $time 要验证的时间变量
   * @param string $type 验证模式 默认1：严格验证 2：不严格验证（允许时间戳是字符串类型）
   * @return boolean
   * @access public
   *
   * ***********************************************************
   */
  public static function checkTimeStamp($time, $type = '1')
  {
      /**
       * 时间戳规则
       * 严格时间戳，必须为整形，不严格时间戳可以为字符串类型
       * 转化为整数类型必须大于等于1970-01-01 00:00:00 时间戳
       */
      if ($type == '2' || is_int($time)) {
          $time = $time * 1;
          if ($time >= strtotime('1970-01-01 00:00:00')) {
              return true;
          } else {
              return false;
          }
      }
      return false;
  }

  /*
   * ************************************************************
   *
   * 验证是否是微信浏览器
   * @return boolean
   * @access public
   *
   * ***********************************************************
   */
  public static function checkWeixinBrowser()
  {
      $user_agent = $_SERVER['HTTP_USER_AGENT'];
      if (strpos($user_agent, 'MicroMessenger') === false) {
          // 非微信
          return false;
      } else {
          return true;
      }
  }

  /*
   * ************************************************************
   *
   * 验证是否是电脑访问
   * @return boolean
   * @access public
   *
   * ***********************************************************
   */
  public static function checkIsPc()
  {
      if (stripos($_SERVER['HTTP_USER_AGENT'], "android") != false || stripos($_SERVER['HTTP_USER_AGENT'], "ios") != false || stripos($_SERVER['HTTP_USER_AGENT'], "wp") != false) {
          return false;
      } else {
          return true;
      }
  }
}