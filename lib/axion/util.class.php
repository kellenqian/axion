<?php
class AXION_UTIL {
	public static function excuteTime($startTime = '', $endTime = '') {
		$stime = $startTime ? $startTime : Axion::$AXION_START_TIME;
		$etime = $endTime ? $endTime : microtime ( true );
		
		return number_format ( ($etime - $stime) * 1000, 2 ) . 'ms ';
	}
	
	/**
	 * 自定义MD5加密算法
	 *
	 * @param string $str_para	参数字符串
	 * @return string
	 */
	public static function encyptCode($str_para) {
		//将要加密的字符串转换为数组
		$_arr_paraCode = str_split ( strval ( $str_para ) );
		//运算种子
		$_arr_seed = array (65, 108, 111, 110, 101 );
		//系统MD5函数加密运算前的密码明文
		$_str_result = '';
		foreach ( $_arr_paraCode as $_int_key => $_str_code ) {
			$_int_codeAscii = ord ( $_str_code );
			if (isset ( $_arr_seed [$_int_key] ))
				$_int_codeAscii = $_int_codeAscii ^ $_arr_seed [$_int_key % 5];
			$_str_result .= sprintf ( '%c', $_int_codeAscii );
		} //end foreach
		

		return md5 ( $_str_result );
	} //end function encyptCode
	

	/**
	 * 去掉指定数组中所有值的前后空白，支持字符串及多级数组
	 *
	 * @param string|array $arr_paras
	 * @param boolean $bool_htmlConver
	 * @return array
	 */
	public static function trimArray($arr_paras, $bool_htmlConver = true) {
		if (! is_array ( $arr_paras ))
			return trim ( $arr_paras );
		
		foreach ( $arr_paras as $_str_key => $_void_value ) {
			if (is_array ( $_void_value ))
				$arr_paras [$_str_key] = trimArray ( $_void_value );
			else {
				$arr_paras [$_str_key] = trim ( $_void_value );
				if ($bool_htmlConver)
					$arr_paras [$_str_key] = htmlspecialchars ( $arr_paras [$_str_key] );
			}
		} //end foreach
		

		return $arr_paras;
	} //function trimArray()
}
?>