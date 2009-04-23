<?php
/**
 * AXION渲染器对象调度器
 *
 */
class AXION_RENDER {
	/**
	 * 渲染器列表
	 *
	 * @var mix
	 */
	private static $validRenders = array ('html' => 'AXION_RENDER_HTML', 'xml' => 'AXION_RENDER_XML', 'js' => 'AXION_RENDER_JS', 'raw' => 'AXION_RENDER_RAW' );
	
	/**
	 * 渲染器对象
	 *
	 * @var AXION_INTERFACE_RENDER
	 */
	private $render;
	
	/**
	 * 渲染后的数据内容
	 *
	 * @var string
	 */
	private $content;
	
	/**
	 * 构造方法
	 *
	 * @param object $appInstance 控制器实例
	 */
	public function __construct($appInstance) {
		if (! $appInstance instanceof AXION_INTERFACE_CONTROLLER) {
			throw new AXION_EXCEPTION ( '非法的控制器对象' );
		}
		
		$responseTo = $appInstance->responseTo ();
		
		$this->render = isset ( self::$validRenders [$responseTo] ) ? self::$validRenders [$responseTo] : new AXION_RENDER_RAW ( );
		
		if (is_string ( $this->render )) {
			$this->render = self::loadRender ( $this->render );
		}
		
		if (! $this->render instanceof AXION_INTERFACE_RENDER) {
			throw new AXION_EXCEPTION ( '非法的渲染器' );
		}
		
		$this->render->addController ( $appInstance );
	}
	
	/**
	 * 渲染数据方法
	 *
	 * @return string
	 */
	public function render() {
		$this->content = $this->render->render ();
		return $this->content;
	}
	
	/**
	 * 增加渲染器
	 *
	 * @param 渲染器名称 $name
	 * @param 渲染器类名或对象实例 $class
	 * @return bool
	 */
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
	
	/**
	 * 加载渲染器
	 *
	 * @param 渲染器名称 $class
	 * @return AXION_INTERFACE_RENDER
	 */
	private static function loadRender($class) {
		if (class_exists ( $class )) {
			return new $class ( );
		}
		
		throw new AXION_EXCEPTION ( '没有找到对应的渲染器对象' );
	}
}
?>