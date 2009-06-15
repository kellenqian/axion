<?php
class AXION_DISPATCHER_DEFAULT implements AXION_INTERFACE_DISPATCHER {
	private $module;
	private $controller;
	private $params;
	
	private $rule = array ();
	
	public function analyse() {
	
	}
	
	public function setRule($rule) {
		if (empty ( $rule )) {
			return false;
		}
		array_unshift ( $this->rule, $rule );
		return true;
	}
	
	public function parseURI() {
		$pathinfo = $_SERVER['PATH_INFO'];
		
		if(empty($pathinfo)){
			return false;
		}
	}
}
?>