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
		return false;
	}
	static $_pcount;
	$lable = ++ $_pcount;
	$debug = debug_backtrace ();
	$str = '<pre>';
	if ($lable) {
		$str .= 'Debug Lable:<strong>' . $lable . '</strong><br/>';
	}
	$str .= '在<strong>' . $debug [0] ['file'] . '</strong>文件的第<strong>' . $debug [0] ['line'] . '</strong>行输出了：<BR/>';
	
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

function loadConfig($str_type = 'axion'){
	if($str_type == 'axion'){
		$_config = require AXION_COMMON_PATH.DS.'default.php';
	}elseif($str_type == 'application'){
		$_config = require APP_CONFIG_PATH.DS.'config.php';
	}
	
	$_ENV = array_merge($_ENV , array_change_key_case($_config,CASE_UPPER));
	
	return true;
}
?>