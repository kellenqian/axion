<?php
class AXION_RENDER_RAW implements AXION_INTERFACE_RENDER {
	private $ctrlInstance;
	private $context;
	
	public function render() {
		if (! $this->context) {
			return '';
		}
		return print_r ( $this->context, true );
	}
	
	public function addController($controller) {
		$this->ctrlInstance = $controller;
		$this->context = $controller->getContext ();
	}
}
?>