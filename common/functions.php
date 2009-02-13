<?php
/**
 * AXION公共函数库
 * @author kellenqian
 * @copyright techua.com
 */

/**
 * 快速打印数据
 * 只有开启DEBUG模式有效
 *
 * @param mix $mix_target
 * @param bool $bool_isBreakPoint 开启则打印完成后停止程序执行
 * @return null
 */
function P($mix_target,$bool_isBreakPoint = false){
	if($_ENV['DEBUG_LEVEL'] < 1) {
		//return false;
	}
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
	
	if($bool_isBreakPoint){
		exit($str);
	}else{
		echo $str;
	}
}

function daddslashes($string, $force = 0) {
	if(!defined('MAGIC_QUOTES_GPC')){
		define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
	}
	
	if(!MAGIC_QUOTES_GPC || $force) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = daddslashes($val, $force);
			}
		} else {
			$string = addslashes($string);
		}
	}
	return $string;
}
?>