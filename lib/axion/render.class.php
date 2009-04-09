<?php
class AXION_RENDER {
	private static $validRenders;
	private static $userDefinedRender = null;
	private $render;
	
	public function __construct($appInstance) {
		self::$validRenders = array ('html', 'xml', 'js', 'raw' );
		
		$responseTo = $appInstance->responseTo ();
		
		if (! $this->render instanceof AXION_INTERFACE_RENDER) {
			throw new AXION_EXCEPTION ( '非法的渲染器' );
		}
	}
	
	public function fetch() {
		return $this->render->fetch ();
	}
	
	public static function addRender($name, $class) {
		if (is_string ( $class )) {
			self::$validRenders [$name] = self::loadRender ( $class );
			return true;
		}
		
		if (is_object ( $class )) {
			self::$validRenders [$name] = $class;
			return true;
		}
		
		return false;
	}
	
	private static function loadRender($class) {
		if(class_exists($class)){
			return new $class;
		}
		
		throw new AXION_EXCEPTION('没有找到对应的渲染器对象');
	}
}
?>