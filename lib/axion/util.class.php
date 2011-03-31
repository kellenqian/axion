<?php
class AXION_UTIL {
	public static function excuteTime($startTime = '', $endTime = '') {
		$stime = $startTime ? $startTime : Axion::$startTime;
		$etime = $endTime ? $endTime : microtime ( true );
		
		return number_format ( ($etime - $stime) * 1000, 5 ) . 'ms ';
	}
}
?>