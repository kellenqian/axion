<?php
Class AXION_RENDER{
	private $render;
	
	public function __construct($appInstance){
		$responseTo = $appInstance->responseTo();
		switch ($responseTo){
			case 'html':
				$this->render = new AXION_RENDER_HTML($appInstance);
				break;
			case 'cli':
				$this->render = new AXION_RENDER_CLI($appInstance);
				break;
			case 'xml':
				$this->render = new AXION_RENDER_XML($appInstance);
				break;
			case 'js':
				$this->render = new AXION_RENDER_JS($appInstance);
				break;
			default:
				throw new AXION_EXCEPTION('找不到对应的渲染器');
		}
	}
	
	public function getRentData(){
		return $this->render->getResult();
	}
	
	public function output(){
		echo $this->getRentData();
	}
}
?>