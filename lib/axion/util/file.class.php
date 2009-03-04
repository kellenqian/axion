<?php
class AXION_UTIL_FILE {
	public static function mkdir($pathname, $mode = 0777) {
		if (! is_dir ( $pathname )) {
			return mkdir ( $pathname, $mode, true );
		}
		return false;
	}
}
?>