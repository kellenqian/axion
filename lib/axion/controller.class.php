<?php
class AXION_CONTROLLER implements AXION_INTERFACE_CONTROLLER, AXION_INTERFACE_RUNNABLE {
	private $context = array ();
	protected $responseTo;
	
	public function __call($name, $arguments) {
		//省的IDE总出讨厌的黄色下划线……
		$arguments;
		$name;
		return $this->actionNotFound ( );
	}
	
	public function __set($name, $value) {
		$this->context [$name] = $value;
	}
	
	public function __get($name) {
		return $this->context [$name];
	}
	
	public function __clone() {
		return $this;
	}
	
	public function __sleep() {
		return $this->context;
	}
	
	public function __wakeup() {
	
	}
	
	public function __toString() {
		return __CLASS__;
	}
	
	public function actionNotFound() {
		return '在' . __CLASS__ . '控制器下没有找到对应的方法';
	}
	
	final public function getContext() {
		return $this->context;
	}
	
	final public function responseTo($responseType = '') {
		if ($responseType) {
			$this->responseTo = $responseType;
			return $responseType;
		} else {
			return $this->responseTo;
		}
	}
	
	public function run() {
	
	}
}
?>