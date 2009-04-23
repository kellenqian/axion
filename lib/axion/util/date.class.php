<?php
/**
 * 日期处理相关函数定义文件
 * @version v1.00
 * @author [Alone] 〖alonedistian@gmail.com〗
 * Date 2007-11-30
 */
/*********************************************
	☆		  				更新说明							☆
 **********************************************
	☆															☆
 ***********************************************/

/***************************************************
	☆		  				IncludeFunction						☆
 ***************************************************
	☆	1.cutDateMonth		计算指定日期（年-月）的前N个月的	☆
	☆							日期（年-月）；						☆
	☆	2.getDaysInMonth	获取指定日期（年-月）的该月天数的	☆
	☆							最大值；								☆
	☆	3.getSubDay			获取两个时间（年-月-日）之间的		☆
	☆							天数差；								☆
	☆	4.getSubMonth		获取连个时间（年-月）之间的月份差；☆
	☆																	☆
 ***************************************************/

class AXION_UTIL_DATE {
	
	/**
	 * 计算指定日期（年-月）的前N个月的日期（年-月）
	 *
	 * @param array $arr_date	$arr_date['y']年, $arr_date['m']月， $arr_date['d']日
	 * @param integer $int_months
	 * @return array
	 */
	public static function cutDateMonth($arr_date, $int_months) {
		//验证日期合法性
		if (! AXION_UTIL_VALIDATE::checkInt ( $int_months, 12, 1 ) || ! AXION_UTIL_VALIDATE::checkInt ( $arr_date ['m'], 12, 1 ))
			return false;
		
		$_int_aimMonths = $arr_date ['y'] * 12 + $arr_date ['m'];
		$_int_aimMonths -= $int_months;
		$_arr_resultDate = array ();
		$_arr_resultDate ['m'] = ($_int_aimMonths % 12) == 0 ? 12 : $_int_aimMonths % 12;
		$_arr_resultDate ['y'] = ($_int_aimMonths - $_arr_resultDate ['m']) / 12;
		return $_arr_resultDate;
	} //end function cutDateMonth()
	

	/**
	 * 获取指定日期（年-月）的该月天数最大值
	 *
	 * @param integer $int_year : 年
	 * @param integer $int_month ： 月
	 * @return integer
	 */
	public static function getDaysInMonth($int_year, $int_month) {
		//验证日期合法性
		if (! AXION_UTIL_VALIDATE::checkInt ( $int_month, 12, 1 ))
			return false;
		
		switch ($int_month) {
			case 4 :
			case 6 :
			case 9 :
			case 11 :
				return 30;
			case 2 :
				if (($int_year % 4 == 0 && $int_year % 100 != 0) || $int_year % 400 == 0)
					return 29;
				return 28;
			default :
				return 31;
		}
	} //function getDaysInMonth
	

	/**
	 * 获取两个时间（年-月-日）之间的天数差
	 *
	 * @param array $arr_startTime : 起始时间 $arr_startTime['y']年, $arr_startTime['m']月， $arr_startTime['d']日
	 * @param array $arr_endTime ： 截止时间 $arr_endTime['y']年, $arr_endTime['m']月， $arr_endTime['d']日 默认为单前系统时间
	 * @return integer : 2个时间之间的天数差
	 */
	public static function getSubDay($arr_startTime, $arr_endTime = '') {
		//合集日期差
		$_int_days = 0;
		
		//验证日期合法性
		if (! checkdate ( $arr_startTime ['m'], $arr_startTime ['d'], $arr_startTime ['y'] ))
			return false;
		
		if (empty ( $arr_endTime ))
			$arr_endTime = @getdate ();
		else {
			//验证日期合法性
			if (! checkdate ( $arr_endTime ['m'], $arr_endTime ['d'], $arr_endTime ['y'] ))
				return false;
			
			$arr_endTime = @getdate ( @mktime ( 0, 0, 0, $arr_endTime ['m'], $arr_endTime ['d'], $arr_endTime ['y'] ) );
		} //else
		

		$arr_startTime = @getdate ( @mktime ( 0, 0, 0, $arr_startTime ['m'], $arr_startTime ['d'], $arr_startTime ['y'] ) );
		
		if ($arr_endTime ['year'] != $arr_startTime ['year']) {
			for($int_i = 1; $int_i <= abs ( $arr_endTime ['year'] - $arr_startTime ['year'] ); $int_i ++) {
				$_arr_tempStartTime = @getdate ( @mktime ( 0, 0, 0, 1, 1, $arr_startTime ['year'] + $int_i ) );
				$_arr_tempEndTime = @getdate ( @mktime ( 23, 59, 59, 12, 31, $arr_startTime ['year'] + $int_i ) );
				$_int_days += $_arr_tempEndTime ['yday'] - $_arr_tempStartTime ['yday'];
			} //end for
			if ($arr_endTime ['year'] < $arr_startTime ['year'])
				$_int_days *= - 1;
		} //end if
		return $_int_days + $arr_endTime ['yday'] - $arr_startTime ['yday'];
	} //function getSubDay
	

	/**
	 * 获取两个时间（年-月）之间的月份差
	 *
	 * @param array $arr_startTime : 起始时间 $arr_startTime['y']年, $arr_startTime['m']月
	 * @param array $arr_endTime : 截止时间 $arr_endTime['y']年, $arr_endTime['m']月 默认为单前系统时间
	 */
	public static function getSubMonth($arr_startTime, $arr_endTime) {
		//验证日期合法性
		if (! AXION_UTIL_VALIDATE::checkInt ( $arr_startTime ['m'], 12, 1 ))
			return false;
		
		if (empty ( $arr_endTime ))
			$arr_endTime = @getdate ();
			//验证日期合法性
		else if (! AXION_UTIL_VALIDATE::checkInt ( $arr_endTime, 12, 1 ))
			return false;
		
		$arr_endTime ['y'] = $arr_endTime ['year'];
		$arr_endTime ['m'] = $arr_endTime ['mon'];
		
		return ($arr_endTime ['y'] - $arr_startTime ['y']) * 12 + $arr_endTime ['m'] - $arr_startTime ['m'];
	} //end function getSubMonth
}
/**
 * Finish
 * o._.o
 */

?>