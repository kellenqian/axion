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
function P($mix_target, $bool_isBreakPoint = false , $return = false) {
	static $_pcount;
	$lable = $return ? $_pcount : ++ $_pcount;
	
	if (is_array ( $mix_target )) {
		foreach ( $mix_target as $k => $v ) {
			if (is_bool ( $v ))
				$mix_target [$k] = $v == true ? '(bool)true' : '(bool)false';
			if(is_null($v))
				$mix_target[$k] = '(null)';
			if(is_array($v))
				$mix_target[$k] = p($v,false,true);
		}
	}
	if (is_bool ( $mix_target ))
		$output = $mix_target == true ? '(bool)true' : '(bool)false';
	if(is_null($mix_target))
		$output = '(null)';
	
	if($return){
		return $mix_target;
	}
	
	$output = htmlspecialchars ( print_r ( $mix_target, TRUE ), ENT_QUOTES );
	
	$debug = debug_backtrace ();
	
	$str = '<pre>';
	if ($lable) {
		$str .= 'Debug Lable:<strong>' . $lable . '</strong><br/>';
	}
	$str .= 'In <strong>' . $debug [0] ['file'] . '</strong> @Line <strong>' . $debug [0] ['line'] . '</strong><BR/>';
	$str .= $output;
	$str .= '</pre>';
	
	if ($bool_isBreakPoint) {
		exit ( $str );
	}
	
	echo $str;
	return ;
}

/**
 * 递归转换对象内公共属性为数组
 *
 * @param object $object
 * @return array
 */
function object2array($object) 
{ 
    $return = NULL; 
       
    if(is_array($object)) 
    { 
        foreach($object as $key => $value) 
            $return[$key] = object2array($value); 
    } 
    else 
    { 
        $var = get_object_vars($object); 
           
        if($var) 
        { 
            foreach($var as $key => $value) 
                $return[$key] = ($key && $value === '') ? NULL : object2array($value); 
        } 
        else return $object; 
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