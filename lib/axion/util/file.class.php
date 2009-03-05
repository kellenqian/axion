<?php
/**
 * AXION工具类
 * 
 * @package AXION
 * @author kellenqian
 * @copyright techua.com
 *
 */
class AXION_UTIL_FILE {
	/**
	 * 递归建目录
	 *
	 * @param 目录地址 $pathname
	 * @param 目录权限 $mode
	 * @return bool
	 */
	public static function mkdir($pathname, $mode = 0777) {
		if (! is_dir ( $pathname )) {
			return mkdir ( $pathname, $mode, true );
		}
		return false;
	}
}
?>