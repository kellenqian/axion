<?php
interface DispatcherInterface{
	public function dispatch();
	
	public function setOption($arr_option = array());
}
?>