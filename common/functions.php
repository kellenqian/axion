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

function P($mix_target, $bool_isBreakPoint = false, $return = false) {
	if (IS_CLI) {
		return P_cli ( $mix_target, $bool_isBreakPoint, $return );
	}
	return P_web ( $mix_target, $bool_isBreakPoint, $return );
}

/**
 * 快速打印数据(网页模式)
 *
 * @param mix $mix_target
 * @param bool $bool_isBreakPoint 开启则打印完成后停止程序执行
 * @return null
 */
function P_web($mix_target, $bool_isBreakPoint = false, $return = false) {
	static $_pcount;
	$lable = $return ? $_pcount : ++ $_pcount;
	
	if (is_array ( $mix_target )) {
		foreach ( $mix_target as $k => $v ) {
			if (is_bool ( $v ))
				$mix_target [$k] = $v == true ? '(bool)true' : '(bool)false';
			if ($v === null)
				$mix_target [$k] = '(null)';
			if (is_array ( $v ))
				$mix_target [$k] = p_web ( $v, false, true );
		}
	}
	
	$result = '';
	
	if (is_bool ( $mix_target ))
		$result = $mix_target == true ? '(bool)true' : '(bool)false';
	if ($mix_target === null)
		$result = '(null)';
	
	if ($return) {
		return $mix_target;
	}
	
	$output = htmlspecialchars ( print_r ( $mix_target, TRUE ), ENT_QUOTES );
	
	$debug = debug_backtrace ();
	
	$str = '<pre>';
	if ($lable) {
		$str .= 'Debug Lable:<strong>' . $lable . '</strong><br/>';
	}
	$str .= 'In <strong>' . $debug [0] ['file'] . '</strong> @Line <strong>' . $debug [0] ['line'] . '</strong><BR/>';
	$str .= $output . $result;
	$str .= '</pre>';
	
	if ($bool_isBreakPoint) {
		exit ( $str );
	}
	
	if (IS_FIREPHP && AXION_CONFIG::get ( 'axion.debug.usefirephp' )) {
		$str = strip_tags ( $str );
		AXION_UTIL_FIREPHP::getInstance ( true )->info ( $str, '打印数据' );
	} else {
		echo $str;
	}
	return;
}

/**
 * 快速打印数据(命令行模式)
 *
 * @param mix $mix_target
 * @param bool $bool_isBreakPoint 开启则打印完成后停止程序执行
 * @return null
 */
function P_cli($mix_target, $bool_isBreakPoint = false, $return = false) {
	static $_pcount;
	$lable = $return ? $_pcount : ++ $_pcount;
	
	if (is_array ( $mix_target )) {
		foreach ( $mix_target as $k => $v ) {
			if (is_bool ( $v ))
				$mix_target [$k] = $v == true ? '(bool)true' : '(bool)false';
			if ($v === null)
				$mix_target [$k] = '(null)';
			if (is_array ( $v ))
				$mix_target [$k] = p_cli ( $v, false, true );
		}
	}
	
	$result = '';
	
	if (is_bool ( $mix_target ))
		$result = $mix_target == true ? '(bool)true' : '(bool)false';
	if ($mix_target === null)
		$result = '(null)';
	
	if ($return) {
		return $mix_target;
	}
	
	$output = print_r ( $mix_target, TRUE );
	
	$debug = debug_backtrace ();
	
	$str = '';
	
	if ($lable) {
		$str .= 'Debug Lable:' . $lable ;
		$str = cprint($str,COR_HIGHLIGHT,true);
		echo $str;
	}
	$str  = 'In ' . $debug [0] ['file'] . ' @Line ' . $debug [0] ['line'];
	$str  = cprint($str,COR_RED,true);
	$output = cprint($output,COR_BLUE,true);
	$str .= $output . $result."\n";
	
	if ($bool_isBreakPoint) {
		exit ( $str );
	}
	
	echo $str;
	return;
}

/**
 * 
 * 在终端中打印带有颜色的文字
 * @param string $text
 * @param string $color
 */
function cprint($text, $color = "",$return = false) {
	if (! IS_CLI) {
		echo $text . "\n";
		return true;
	}
	$str = "\033[1;$color$text\033[0m\n";
	if($return)
	{
		return $str;
	}
	echo $str;
	return true;
}

/**
 * 递归转换对象内公共属性为数组
 *
 * @param object $object
 * @return array
 */
function object2array($object) {
	$return = NULL;
	
	if (is_array ( $object )) {
		foreach ( $object as $key => $value )
			$return [$key] = object2array ( $value );
	} else {
		$var = is_object ( $object ) ? get_object_vars ( $object ) : false;
		
		if ($var) {
			foreach ( $var as $key => $value )
				$return [$key] = ($key && $value === '') ? NULL : object2array ( $value );
		} else
			return $object;
	}
	
	return $return;
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