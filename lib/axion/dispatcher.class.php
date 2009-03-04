<?php
Class AXION_DISPATCHER implements AXION_INTERFACE_DISPATCHER{
	private $controller;
	
	private $action;
	
	private $params;
	
	public function __construct(){
		p($_SERVER);	
		$pathinfo = trim($_SERVER['PATH_INFO'],'/');
		if(empty($pathinfo)){
			$this->controller = AXION_CONFIG::get('axion.controller.default');
			$this->action     = AXION_CONFIG::get('axion.action.default');
			$this->params     = $_SERVER['QUERY_STRING'];
		}
	}
	
	public function getParams(){
		$params['controller'] = $this->controller;
		$params['action']     = $this->action;
		$params['params']     = $this->params;
		
		return $params;
	}
}
?>