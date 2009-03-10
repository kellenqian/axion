<?php
class AXION_RENDER_CLI implements AXION_INTERFACE_RENDER {
	private $ctrlInstance;
	private $context;
	
	public function __construct($controllerInstance) {
		$this->ctrlInstance = $controllerInstance;
		$this->context = $this->ctrlInstance->getContext ();
	}
	
	public function fetch();
	
	public function getResult() {
		$str = '';
		foreach ( $this->context as $k => $v ) {
			$str .= $k . "\n" . $v . "\n";
		}
		return $str;
	}
}
?>