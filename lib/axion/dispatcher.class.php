<?php
class AXION_DISPATCHER {
	private $rule;
	
	public static function getInstance() {
		static $_handler = array ();
		$dispacherName = AXION_CONFIG::GET ( 'axion.dispatcher.class' );
		
		if (! empty ( $_handler [$dispacherName] )) {
			return $_handler [$dispacherName];
		} else {
			$dispacherInstance = self::factory ( $dispacherName );
			$_handler [$dispacherName] = $dispacherInstance;
			
			return $dispacherInstance;
		}
	}
	
	private static function factory($handlerName) {
		$className = strtolower ( $handlerName );
		
		$instance = new $className ( );
		
		if ($instance instanceof AXION_INTERFACE_DISPATCHER) {
			return $instance;
		} else {
			throw new AXION_EXCEPTION ( '该对象并非合法的调度器对象' );
		}
	}
}
?>