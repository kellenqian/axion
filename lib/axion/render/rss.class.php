<?php
	/**
	 * RENDER渲染器--RSS
	 * @desc 使用XML的方式输出结果
	 * @author [Alone] alonedistian@gmail.com〗
	 * @package PHPDoc
	 */
	class AXION_RENDER_RSS implements AXION_INTERFACE_RENDER
	{
		protected $obj_controller;
		
		public function render()
		{
		}
		
		public function addController( $controller )
		{
			$this->obj_controller = $controller;
		}
	}
?>