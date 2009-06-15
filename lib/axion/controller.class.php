<?php
/**
 * AXION控制器类
 *
 */
abstract class AXION_CONTROLLER implements AXION_INTERFACE_CONTROLLER{
	private $context = array();
	private $responseTo;
	
	
	/**
	 * 自动方法调用
	 *
	 * @param 方法名 $name
	 * @param 参数 $arguments
	 * @return unknown
	 */
	public function __call($name, $arguments) {
		//省的IDE总出讨厌的黄色下划线……
		$arguments;
		$name;
		return $this->actionNotFound ();
	}
	
	/**
	 * 自动属性设置
	 *
	 * @param 属性名 $name
	 * @param 值 $value
	 */
	final public function __set($name, $value) {
		$this->context [$name] = $value;
	}
	
	/**
	 * 自动获取属性
	 *
	 * @param 属性名 $name
	 * @return mix
	 */
	final public function __get($name) {
		if( isset( $this->context[$name] ) )
			return $this->context [$name];
		return false;
	}
	
	/**
	 * 控制器克隆方法
	 *
	 * @return object
	 */
	public function __clone() {
		return $this;
	}
	
	/**
	 * 控制器冷冻方法
	 *
	 * @return mix
	 */
	public function __sleep() {
		return $this->context;
	}
	
	/**
	 * 控制器解冻方法
	 *
	 */
	public function __wakeup() {
	
	}
	
	/**
	 * 控制器打印方法
	 *
	 * @return unknown
	 */
	public function __toString() {
		return $this->getClassName();
	}
	
	/**
	 * 获取当前控制器名
	 *
	 * @return string
	 */
	public function getClassName(){
		return get_class( $this );
	}
	
	/**
	 * 获取当前控制器数据
	 *
	 * @return mix
	 */
	final public function getContext() {
		return $this->context;
	}
	
	/**
	 * 设置控制器数据
	 *
	 * @param 属性名 $name
	 * @param 数据 $value
	 */
	final public function assign($name , $value){
		$this->__set($name,$value);
	}
	
	/**
	 * 设置控制器响应类型
	 *
	 * @param string $responseType (xml,html,js,raw)
	 * @return string
	 */
	final public function responseTo($responseType = '') {
		if ($responseType) {
			$this->responseTo = $responseType;
			return $responseType;
		} else {
			return $this->responseTo;
		}
	}
	
	/**
	 * 页面跳转临时数据存储方法
	 *
	 * @param 消息名称 $key
	 * @param 消息 $value
	 * @return bool
	 */
	final protected function flash($key, $value = '') {
		if (! $value) {
			if (isset ( $_SESSION ['_flash'] )) {
				return $_SESSION ['_flash'] [$key];
			}else{
				return false;
			}
		}
		$_SESSION ['_flash'] [$key] = $value;
		return true;
	}
	
	/**
	 * 默认控制器执行入口
	 *
	 * @return unknown
	 */
	public function run() {
		return 'default run Function';
	}
	
	
	/**
	 * 控制器析构函数
	 *
	 */
	public function __destruct() {
	
	}
}
?>