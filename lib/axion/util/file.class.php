<?php
class AXION_UTIL_FILE {
	public static function mkdir($pathname, $mode) {
		is_dir ( dirname ( $pathname ) ) || self::mkdir ( dirname ( $pathname ), $mode );
		return is_dir ( $pathname ) || @mkdir ( $pathname, $mode );
	}
}
?>