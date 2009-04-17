<?php
Class AXION_DISPATCHER implements AXION_INTERFACE_DISPATCHER{
	private $controller;
	
	private $action;
	
	private $params;
	
	public function __construct(){
		
		if(empty($_SERVER['PATH_INFO'])){
			$this->controller = AXION_CONFIG::get('axion.controller.default');
			$this->action     = AXION_CONFIG::get('axion.action.default');
			AXION_CONFIG::set( 'axion.controller.outModel', 'html' );/* @todo 这是个什么东西 ？ */
		}else {
			$pathinfo = trim($_SERVER['PATH_INFO'],'/');
			$pathinfoArray = $this->parsePathinfo($pathinfo);
			$this->controller = $pathinfoArray['controller'];
			$this->action     = $pathinfoArray['action'];
			$this->params     = empty( $pathinfoArray['params'] ) ? '' : $pathinfoArray['params'];
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
			
			/**
			 * 根据URI获取输出方式
			 */
			$_arr_controller = explode( '.', $controller );
			if( sizeof( $_arr_controller ) != 1 )
			{
				$_str_outpubModel = array_pop( $_arr_controller );
				if( class_exists( "AXION_RENDER_{$_str_outpubModel}" ) )
					$_SERVER ['X-AXION-REQUEST-FORMAT'] = $_str_outpubModel;
					
				$controller = substr( $controller, 0, ( strlen( $_str_outpubModel ) + 1 ) * -1 );
			}
			
			return array('controller' => $controller , 'action'=> $action);
		}
		
		$controller = array_shift($pathinfoArray);
		$action		= array_shift($pathinfoArray);
		
		/**
		 * 根据URI获取输出方式
		 */
		$_arr_controller = explode( '.', $controller );
		if( sizeof( $_arr_controller ) != 1 )
		{
			$_str_outpubModel = array_pop( $_arr_controller );
			if( class_exists( "AXION_RENDER_{$_str_outpubModel}" ) )
					$_SERVER ['X-AXION-REQUEST-FORMAT'] = $_str_outpubModel;
			$controller = substr( $controller, 0, ( strlen( $_str_outpubModel ) + 1 ) * -1 );
		}
			
		$params = array();
		for ($i=0 ; $i < sizeof($pathinfoArray) ; $i++){
			if($pathinfoArray[$i]){
				if(isset($pathinfoArray[$i+1])){
					$params[$pathinfoArray[$i]] = $pathinfoArray[++$i];
				}
			}
		}
		
		return array('controller' => $controller , 'action' => $action , 'params' => $params);
	}
}
?>