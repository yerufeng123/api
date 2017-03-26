<?php
/**
 *日期和时间助手
 * @author gaoxiangdong<gxd_dnjlw@163.com>
 * @copyright 2017-03-22
 */

namespace app\helpers;

use Yii;

/**
 *
 */
class DatetimeHelper
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
  /* * ************************* 时间类 ******************************** */
  /* * ************************** ********************************* */
  /* * *********************************************************************** */

  /*
   * ************************************************************
   *
   * 功能计算两个时间的时间差
   * @param string $time1 起始日期:时间字符串或者时间戳
   * @param string $time2 结束日期:时间字符串或者时间戳
   * @return array={[day]=>?,[hour]=>?,[minute]=>?,[second]=>?}
   * @access public
   *
   * ***********************************************************
   */
  public static getTimeDiff($time1, $time2)
  {
      $startdate = checkTimeStamp($time1) ? $time1 : strtotime($time1);
      $enddate = checkTimeStamp($time2) ? $time2 : strtotime($time2);
      $temp = $enddate - $startdate;
      $result['day'] = floor($temp / 86400);
      $result['hour'] = floor($temp % 86400 / 3600);
      $result['minute'] = floor($temp % 86400 % 3600 / 60);
      $result['second'] = floor($temp % 86400 % 3600 % 60);

      if ($result['day'] >= 0) {
          if ($result['hour'] == 0 && $result['minute'] == 0 && $result['second'] == 0) {
              $result['lastday'] = $result['day'];
          } else {
              $result['lastday'] = $result['day'] + 1;
          }
      } else {
          $result['lastday'] = '0';
      }
      return $result;
  }

  /*
   * ************************************************************
   *
   * 功能:获取精确度更高的时间戳
   * @param level 级别（小数位数【0~6】）
   * @param style 样式（是否显示小数点：1不显示2显示）
   * @return string 为了防止科学计数法，返回数值用字符串表示
   * @access public
   *
   * ***********************************************************
   */
  public static getTimestamps($level = 3, $style = '2')
  {
      if ($level * 1 > 6 || $level * 1 < 0) {
          $level = 6;
      }
      $newnum = '';
      list ($usec, $sec) = explode(" ", microtime());
      switch ($style) {
          case '1':
              $newnum = $sec . substr($usec, 2, $level * 1);
              break;
          case '2':
              $newnum = $sec . substr($usec, 1, 1 + $level * 1);
              break;
          default:
              $newnum = $sec . substr($usec, 2, $level * 1);
              break;
      }
      return $newnum;
  }

}
