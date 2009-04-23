<?php
/**
 * 数值操作函数定义文件
 * @author [Alone] 〖alonedistian@gmail.com〗
 */
/*********************************************
	☆		  				更新说明							☆
 **********************************************
	☆															☆
 ***********************************************/

/***************************************************
	☆		  				IncludeFunction						☆
 ***************************************************
	☆	1.tranNumber			将阿拉伯数字翻译为中文大写数字	☆
	☆																	☆
 ***************************************************/
class AXION_UTIL_NUMBER {
	/**
	 * 将阿拉伯数字翻译为中文大写数字
	 * @desc 翻译上限16位阿拉伯数组
	 *
	 * @param string $str_Number 阿拉伯数字
	 * @param integer $int_lv 级别（万， 亿）不需指定该参数
	 * @return string
	 */
	public static function tranNumber($str_Number, $int_lv = 0) {
		
		$_str_result = "";
		$_arr_numberLv = array (0 => '', 1 => '万', 2 => '亿', 3 => '万' );
		$_arr_numberFormat = array (0 => '', 1 => '拾', 2 => '佰', 3 => '仟' );
		$_arr_numberName = array (0 => '零', 1 => '壹', 2 => '贰', 3 => '叁', 4 => '肆', 5 => '伍', 6 => '陸', 7 => '柒', 8 => '捌', 9 => '玖' );
		$_arr_subNumber = str_split ( substr ( $str_Number, - 4 ) );
		$_int_numberLength = count ( $_arr_subNumber );
		
		for($_int_i = $_int_numberLength - 1; $_int_i >= 0; $_int_i --) {
			$_str_result = $_arr_numberName [$_arr_subNumber [$_int_i]] . (empty ( $_arr_subNumber [$_int_i] ) ? '' : $_arr_numberFormat [$_int_numberLength - $_int_i - 1]) . $_str_result;
		} //for
		

		$_str_result = str_replace ( array ('零', '零零', '零零零', '零零零零' ), '零', $_str_result );
		
		if (preg_match ( '/零$/', $_str_result ))
			$_str_result = substr ( $_str_result, 0, - 3 );
		if (preg_match ( '/^壹拾/', $_str_result ))
			$_str_result = substr ( $_str_result, 3 );
		
		$_str_result .= $_arr_numberLv [$int_lv];
		
		if (strlen ( $str_Number ) > 4)
			$_str_result = tranNumber ( substr ( $str_Number, 0, - 4 ), $int_lv + 1 ) . $_str_result;
		else {
			if (preg_match ( '/^零/', $_str_result ))
				$_str_result = substr ( $_str_result, 3 );
			if (empty ( $_str_result ))
				$_str_result = '零';
		} //else
		

		return $_str_result;
	} //end function tranData
}
?>