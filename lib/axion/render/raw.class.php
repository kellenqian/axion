<?php
class AXION_RENDER_RAW implements AXION_INTERFACE_RENDER {
	private $ctrlInstance;
	private $context;
	
	public function __construct() {
	}
	
	public function render() {
		return print_r($this->context,true);
	}
	
	public function addController($controller){
		
	}
}
?>