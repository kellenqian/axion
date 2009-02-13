<?php
abstract class Base {
	/**
	 * 框架版本号
	 *
	 * @var float
	 */
	protected $version = '0.01';
	
	/**
	 * 配置信息对象
	 *
	 * @var config
	 */
	protected $config;
	
	public function __construct(){
		$this->config = new Config;
	}
	
	public function set($c = array(1)){
		$this->config->set($c);
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