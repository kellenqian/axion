<?php
Class AXION_UTIL{
	public static function excuteTime($endTime = ''){
		$time = $endTime ? $endTime : microtime(true);
		
		return number_format ( ($time - AXION::$AXION_START_TIME)*1000, 2 ) . 'ms ';
	}
}
?>