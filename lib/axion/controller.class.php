<?php
abstract class AXION_CONTROLLER implements AXION_INTERFACE_CONTROLLER, AXION_INTERFACE_RUNNABLE {
	private $context = array ();
	private $responseTo;
	protected $str_templateFileName;
	
	public function __call($name, $arguments) {
		//省的IDE总出讨厌的黄色下划线……
		$arguments;
		$name;
		return $this->actionNotFound ();
	}
	
	final public function __set($name, $value) {
		$this->context [$name] = $value;
	}
	
	final public function __get($name) {
		if( isset( $this->context[$name] ) )
			return $this->context [$name];
		return false;
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
	
	final protected function flash($key, $value = '') {
		if (! $value) {
			if (isset ( $_SESSION ['_flash'] )) {
				return $_SESSION ['_flash'] [$key];
			}else{
				return false;
			}
		}
		$_SESSION ['_flash'] [$key] = $value;
	}
	
	public function run() {
	
	}
	
	public function setTpl( $str_templateFileName )
	{
		$this->str_templateFileName = $str_templateFileName;
	}//end function setTpl
	
	public function getTpl()
	{
		return empty( $this->str_templateFileName ) ? false : $this->str_templateFileName;
	}//end function getTpl
	
	public function __destruct() {
	
	}
}
?>