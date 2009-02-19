<?php
abstract class Base {
	/**
	 * 框架版本号
	 *
	 * @var float
	 */
	protected $version = '0.01';
	
	/**
	 * 各组件配置信息对象
	 *
	 * @var config
	 */
	protected $config;
	
	/**
	 * 全局配置信息
	 *
	 * @var array
	 */
	static $_globalConfig = array();
	
	public function __construct(){
		$this->config = new Config;
		$this->config->set(self::$_globalConfig);
	}
	
	static function set()){
		
	}
	
	
	/**
	 * 返回框架版本信息
	 *
	 * @return $this->version;
	 */
	protected function version() {
		return $this->version;
	}
}
?>