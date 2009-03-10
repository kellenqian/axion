<?php
class AXION_SESSION {
	private static $sessionInstance;
	private $sessionTimeout;
	private $sessionHandler;
	private $sessionHashkey;
	
	public function session_start() {
		$sessionConfig = AXION_CONFIG::get ( 'axion.session' );
		$this->sessionHandler = $sessionConfig ['handler'];
		$this->sessionTimeout = $sessionConfig ['timeout'];
	
		if (! self::$sessionInstance) {
			if(in_array($this->sessionHandler,array('file','memcached'))){
				$class = 'AXION_SESSION_'.$this->sessionHandler;
			}else{
				$class = $this->sessionHandler;
			}
			if (class_exists ( $class )) {
				self::$sessionInstance = new $class ( );
			} else {
				throw new AXION_EXCEPTION ( '无法找到对应的SESSION控制器' );
			}
		}
		
		session_set_save_handler ( array (self::$sessionInstance, 'open' ), array (self::$sessionInstance, 'close' ), array (self::$sessionInstance, 'read' ), array (self::$sessionInstance, 'write' ), array (self::$sessionInstance, 'destroy' ), array (self::$sessionInstance, 'gc' ) );
		
		session_start ();
	}
}
?>