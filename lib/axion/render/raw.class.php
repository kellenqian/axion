<?php
class AXION_RENDER_RAW implements AXION_INTERFACE_RENDER {
	private $ctrlInstance;
	private $context;
	
	public function __construct($controllerInstance) {
		$this->ctrlInstance = $controllerInstance;
		$this->context = $this->ctrlInstance->getContext ();
	}
	
	public function fetch() {
		return print_r($this->context,true);
	}
}
?>