<?php
Class AXION_DISPATCHER implements AXION_INTERFACE_DISPATCHER{
	private $controller;
	
	private $action;
	
	private $params;
	
	public function __construct(){
		if(empty($_SERVER['PATH_INFO'])){
			$this->controller = AXION_CONFIG::get('axion.controller.default');
			$this->action     = AXION_CONFIG::get('axion.action.default');
		}else {
			$pathinfo = trim($_SERVER['PATH_INFO'],'/');
			$pathinfoArray = $this->parsePathinfo($pathinfo);
			$this->controller = $pathinfoArray['controller'];
			$this->action     = $pathinfoArray['action'];
			$this->params     = $pathinfoArray['params'];
		}
	}
	
	public function getParams(){
		$params['controller'] = $this->controller;
		$params['action']     = $this->action;
		$params['params']     = $this->params;
		
		return $params;
	}
	
	private function parsePathInfo($pathinfo){
		$pathinfoArray = explode('/',$pathinfo);
		
		if(sizeof($pathinfoArray) === 1){
			$controller = $pathinfoArray[0];
			$action = AXION_CONFIG::get('axion.action.default');
			return array('controller' => $controller , 'action'=> $action);
		}
		
		$controller = array_shift($pathinfoArray);
		$action		= array_shift($pathinfoArray);
		
		$params = array();
		for ($i=0 ; $i < sizeof($pathinfoArray) ; $i++){
			if($pathinfoArray[$i]){
				$params[$pathinfoArray[$i]] = $pathinfoArray[++$i];
			}
		}
		
		return array('controller' => $controller , 'action' => $action , 'params' => $params);
	}
}
?>