<?php
class AXION_UTIL_VALIDATE {
	/**
	 * 测试变量的值是否为字符串，及字符串是否符合标准
	 *
	 * @param string $str_para : 要测试的变量
	 * @param integer $int_maxLength : 限制字符串的最大长度
	 * @param integer $int_minLength ：限制字符串的最小长度
	 * @param string $str_preg ： 限制字符串必须符合的正则表达式
	 * @return boolean 标识给定字符串是否符合要求
	 */
	public static function checkString($str_para, $int_maxLength = null, $int_minLength = null, $str_preg = "") {
		if (empty ( $str_para ) && $int_minLength != - 1)
			return false;
			
		//测试参数变量类型
		if (! is_string ( $str_para ))
			return false;
			//测试参数的最大长度是否符合要求
		if (! is_null ( $int_maxLength ) && strlen ( $str_para ) > $int_maxLength)
			return false;
			
		//测试参数的最小长度是否符合要求
		if (! is_null ( $int_minLength ) && strlen ( $str_para ) < $int_minLength)
			return false;
			
		//测试参数是否符合正则表达式的要求
		if (! empty ( $str_preg ) && ! preg_match ( $str_preg, $str_para ))
			return false;
		
		return true;
	} //end static function checkString
	

	/**
	 * 检测变量的值是否为数值型变量，及数值是否符合要求（如 1.0|2.0 等 均被视为整数）
	 *
	 * @param integer $int_para ： 要测试的变量
	 * @param integer $int_max ： 限制数值的最大值
	 * @param integer $int_min ： 限制数值的最小值
	 * @return boolean ： 标识给定数值是否符合要求
	 */
	public static function checkInt($int_para, $int_max = null, $int_min = null) {
		//测试变量类型是否符合要求			
		if (strlen ( $int_para ) != strlen ( intval ( $int_para ) ))
			return false;
		
		if (floor ( $int_para ) != $int_para)
			return false;
			
		//测试变量大小是否符合要求
		if (! is_null ( $int_max ) && $int_para > $int_max)
			return false;
		
		if (! is_null ( $int_min ) && $int_para < $int_min)
			return false;
		
		return true;
	} //end static function checkInt	
	

	public static function checkFloat($int_para, $int_max = null, $int_min = null) {
		//测试变量类型是否符合要求			
		if (strlen ( $int_para ) != strlen ( sprintf ( '%.2f', $int_para ) )) {
			return false;
		}
		
		//测试变量大小是否符合要求
		if (! is_null ( $int_max ) && $int_para > $int_max)
			return false;
		
		if (! is_null ( $int_min ) && $int_para < $int_min)
			return false;
		
		return true;
	} //end function checkFloat
}
?>