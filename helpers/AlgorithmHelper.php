<?php
/**
 *算法助手
 * @author gaoxiangdong<gxd_dnjlw@163.com>
 * @copyright 2017-03-22
 */

namespace app\helpers;

use Yii;

/**
 *
 */
class AlgorithmHelper
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
  /* * ************************* 算法类 ******************************** */
  /* * ************************** ********************************* */
  /* * *********************************************************************** */

  /*
   * ************************************************************
   *
   * 根据经纬度，计算2点间的距离
   * @param type $lng1 经度1
   * @param type $lat1 纬度1
   * @param type $lng2 经度2
   * @param type $lat2 纬度2
   * @param type $len_type 1:m 2:km
   * @param type $decimal 精度，小数点后的位数
   * @return number 返回距离值
   * @access public
   *
   * ***********************************************************
   */
  public static getDistance($lng1, $lat1, $lng2, $lat2, $len_type = 1, $decimal = 2)
  {
      define('EARTH_RADIUS', 6378.137); // 地球半径，假设地球是规则的球体
      define('PI', 3.1415926);
      $radLat1 = $lat1 * PI() / 180.0; // PI()圆周率
      $radLat2 = $lat2 * PI() / 180.0;
      $a = $radLat1 - $radLat2;
      $b = ($lng1 * PI() / 180.0) - ($lng2 * PI() / 180.0);
      $s = 2 * asin(sqrt(pow(sin($a / 2), 2) + cos($radLat1) * cos($radLat2) * pow(sin($b / 2), 2)));
      $s = $s * EARTH_RADIUS;
      $s = round($s * 1000);
      if ($len_type > 1) {
          $s /= 1000;
      }
      return round($s, $decimal);
  }

  /*
   * ************************************************************
   *
   * 抽奖算法1
   * 适用场景：奖品和中奖概率都可以单独配置
   * @param array $proArr 抽奖配置数组
   * @return string 返回中奖项的键名
   * @access public
   *
   * ***********************************************************
   */
  public static getLottery1($proArr)
  {

      /**
       * $proArr = array(
       * '0' => array('id'=>1,'prize'=>'平板电脑','v'=>1),
       * '1' => array('id'=>2,'prize'=>'数码相机','v'=>5),
       * '2' => array('id'=>3,'prize'=>'音箱设备','v'=>10),
       * '3' => array('id'=>4,'prize'=>'4G优盘','v'=>12),
       * '4' => array('id'=>5,'prize'=>'10Q币','v'=>22),
       * '5' => array('id'=>6,'prize'=>'下次没准就能中哦','v'=>50),
       * );
       */
      $newProArr = array();
      foreach ($proArr as $key => $proCur) {
          $newProArr[$key] = $proCur['v'];
      }
      $result = '';

      // 概率数组的总概率精度
      $proSum = array_sum($newProArr);

      // 概率数组循环
      foreach ($newProArr as $key => $proCur) {
          $randNum = mt_rand(1, $proSum);
          if ($randNum <= $proCur) {
              $result = $key;
              break;
          } else {
              $proSum -= $proCur;
          }
      }
      unset($proArr);
      unset($newProArr);
      return $result;
  }
}
