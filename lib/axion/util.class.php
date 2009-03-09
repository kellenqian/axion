<?php
Class AXION_UTIL{
	public static function excuteTime($startTime = '' ,$endTime = ''){
		$stime = $startTime ? $startTime : Axion::$AXION_START_TIME;
		$etime = $endTime ? $endTime : microtime(true);
		
		return number_format ( ($etime - $stime)*1000, 2 ) . 'ms ';
	}
}
?>