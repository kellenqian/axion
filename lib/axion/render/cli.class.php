<?php
Class AXION_RENDER_CLI{
	private $ctrlInstance;
	private $context;
	
	public function __construct($controllerInstance){
		$this->ctrlInstance = $controllerInstance;
		$this->context = $this->ctrlInstance->getContext();
	}
	
	public function getResult(){
		$str= '';
		foreach ($this->context as $k => $v){
			$str .= $k."\n".$v."\n";
		}
		return $str;
	}
}
?>