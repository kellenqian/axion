<?php
/**
 * AXION公共函数库
 * @author kellenqian
 * @copyright techua.com
 */

/**
 * 快速打印数据
 *
 * @param mix $mix_target
 * @param bool $bool_isBreakPoint 开启则打印完成后停止程序执行
 * @return null
 */
function P($mix_target, $bool_isBreakPoint = false) {
	static $_pcount;
	$lable = ++ $_pcount;
	$debug = debug_backtrace ();
	$str = '<pre>';
	if ($lable) {
		$str .= 'Debug Lable:<strong>' . $lable . '</strong><br/>';
	}
	$str .= 'In <strong>' . $debug [0] ['file'] . '</strong> @Line <strong>' . $debug [0] ['line'] . '</strong><BR/>';
	
	if (is_array ( $mix_target )) {
		foreach ( $mix_target as $k => $v ) {
			if (is_bool ( $v ))
				$mix_target [$k] = $v == true ? '(bool)true' : '(bool)false';
		}
	}
	if (is_bool ( $mix_target ))
		$output = $mix_target == true ? '(bool)true' : '(bool)false';
	else
		$output = htmlspecialchars ( print_r ( $mix_target, TRUE ), ENT_QUOTES );
	$str .= $output;
	$str .= '</pre>';
	
	if ($bool_isBreakPoint) {
		exit ( $str );
	} else {
		echo $str;
	}
}

/**
 * 转换数组为对象
 *
 * @param array $array
 * @return object
 */
function array2obj($array) {
	foreach ( $array as $key => $value ) {
		if (! is_string ( $key )) {
			return false;
		}
		if (is_array ( $value )) {
			$array [$key] = array2obj ( $value );
		}
	}
	return ( object ) $array;
}

/**
 * 递归转换数组键大小写
 *
 * @param array $array
 * @param int $case
 * @return array
 */
function array_change_key_case_recursive($array, $case = CASE_LOWER) {
	if (is_array ( $array )) {
		foreach ( $array as $key => $value ) {
			if ($case == CASE_LOWER) {
				$casedArray [strtolower ( $key )] = array_change_key_case_recursive ( $value, $case );
			} else {
				$casedArray [strtoupper ( $key )] = array_change_key_case_recursive ( $value, $case );
			}
		}
	} else {
		$casedArray = $array;
	}
	return $casedArray;
}

/**
 * 递归数组转义其值
 *
 * @param mix $string
 * @param int $force
 * @return mix
 */
function daddslashes($string, $force = 0) {
	if (! defined ( 'MAGIC_QUOTES_GPC' )) {
		define ( 'MAGIC_QUOTES_GPC', get_magic_quotes_gpc () );
	}
	
	if (! MAGIC_QUOTES_GPC || $force) {
		if (is_array ( $string )) {
			foreach ( $string as $key => $val ) {
				$string [$key] = daddslashes ( $val, $force );
			}
		} else {
			$string = addslashes ( $string );
		}
	}
	return $string;
}
?>