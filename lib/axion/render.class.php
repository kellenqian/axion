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
		
		if(!$this->render instanceof AXION_INTERFACE_RENDER ){
			throw new AXION_EXCEPTION('非法的渲染器');
		}
	}
	
	public function fetch(){
		return $this->render->fetch();
	}
	
	public function display(){
		echo $this->fetch();
	}
}
?>