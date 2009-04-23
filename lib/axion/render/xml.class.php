<?php
	/**
	 * RENDER渲染器--XML
	 * @desc 使用XML的方式输出结果
	 * @author [Alone] alonedistian@gmail.com〗
	 * @package PHPDoc
	 */
	class AXION_RENDER_XML implements AXION_INTERFACE_RENDER
	{
		protected $obj_controller;
		
		public function render()
		{
			$_obj_xml = new AXION_UTIL_XML();
			$_arr_paras = $this->obj_controller->getContext();
			foreach ( $_arr_paras as $_str_key => $_void_value ) 
				$_obj_xml->appendElement( $_str_key, $_void_value );
			
			$_obj_xml->getXMLDocument();
		}
		
		public function addController( $controller )
		{
			$this->obj_controller = $controller;
		}
	}
?>