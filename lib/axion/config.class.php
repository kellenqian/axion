<?php
class AXION_CONFIG {
	
	/**
	 * AXION框架统一配置类
	 * 
	 * 完成框架内部/应用程序部分的配置工作
	 * 
	 * @author kellenqian , Alone
	 * @package AXION
	 * @copyright techua.com
	 */
	
	
	/**
	 * 配置数据存储容器
	 *
	 * @var array
	 */
	private static $container = array();
	
	/**
	 * 锁容器
	 * 
	 * 用于存储锁模式的配置段的标识
	 *
	 * @var array
	 */
	private static $locked = array ();
	
	
	/**
	 * 注册配置信息
	 *
	 * @param string $package 配置段路径 如'axion.config.debug.level'
	 * @param mix $value 配置段值
	 * @return mix
	 */
	private static function register($package, $value = false) {
		$packageTolower = strtolower ( $package );
		
		$pointer = & self::$container;
		
		if (strpos ( $package, '.' )) {
			$packageArray = explode ( '.', $packageTolower );
			foreach ( $packageArray as $v ) {
				$pointer = & $pointer [$v];
			}
		} else {
			$pointer = & $pointer [$packageTolower];
		}
		
		if ($value) {
			$pointer = $value;
		}
		
		return $pointer;
	}
	
	/**
	 * 锁定配置值使其无法更改
	 * 
	 * 如果锁定一个配置的段的父段，则所有子路径继承配置锁
	 * 
	 * 如锁定axion.config，则axion.config.level也无法更改
	 *
	 * @param string $package 配置段路径
	 */
	public static function lock($package) {
		self::$locked [$package] = true;
	}
	
	/**
	 * 检测某一个配置段是否锁定
	 *
	 * @param string $package 配置段路径
	 * @return bool
	 */
	public static function islock($package) {
		$packageTolower = strtolower ( $package );
		$hashKey = null;
		$hashKeyArray = array ();
		if (strpos ( $package, '.' )) {
			$packageArray = explode ( '.', $packageTolower );
			foreach ( $packageArray as $v ) {
				$hashKeyArray [] = $v;
				$hashKey = join ( '.', $hashKeyArray );
				if (self::$locked [$hashKey]) {
					return true;
				}
			}
		} else {
			if (self::$locked [$package]) {
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * 设定配置数据
	 *
	 * @param string $package 配置段路径
	 * @param mix $value 配置值
	 * @param bool $lock 是否锁定
	 * @return mix 返回设置值
	 */
	public static function set($package, $value, $lock = false) {
		if (self::islock ( $package )) {
			throw new AXION_EXCEPTION('配置段锁定，拒绝设置',E_NOTICE);
		}
		
		if (is_array ( $value )) {
			$value = array_change_key_case_recursive ( $value );
		}
		
		$currentConfig = self::register ( $package, $value );
		
		if ($lock) {
			self::lock ( $package );
		}
		
		return $currentConfig;
	}
	
	/**
	 * 获取配置段数据
	 *
	 * @param string $package 配置段路径 置空返回全部配置数据
	 * @return mix 
	 */
	public static function get($package = '') {
		if (empty ( $package )) {
			return self::$container;
		}
		
		$packageTolower = strtolower($package);
		if(!strpos($packageTolower,'.')){
			return self::$container[$packageTolower];
		}
		
		$packageArray = explode('.',$packageTolower);
		
		$run = null;
		foreach ($packageArray as $v){
			$run[] = "['$v']";
		}
		
		$run = join('',$run);
		
		$run = '$config = self::$container'.$run.';';
		
		eval($run);
		
		return $config ? $config : false;
	}
	
	/**
	 * 读取配置文件到指定配置段中
	 *
	 * @param string $configFile 配置文件地址
	 * @param string $package 配置段路径
	 * @param bool $lock 锁开关
	 * @return self::set
	 */
	public static function loadConfigFile($configFile, $package = 'axion', $lock = false) {
		if (! file_exists ( $configFile )) {
			throw new AXION_EXCEPTION('找不到配置文件',E_ERROR);
		}
		
		$suffix = strtolower(substr($configFile , -3 , 3));
		
		switch ($suffix){
			case 'ini':
				$newConfigArray = self::loadIni($configFile);
				break;
			case 'xml':
				$newConfigArray = self::loadXml($configFile);
				break;
			case 'php':
				$newConfigArray = self::loadPHP($configFile);
				break;
			default:
				throw new AXION_EXCEPTION('未知配置文件格式',E_NOTICE);
				break;
		}
		return self::set ( $package, $newConfigArray, $lock );
	}
	
	/**
	 * 读取INI格式的配置文件
	 *
	 * @param string $configFile
	 * @return array
	 */
	private static function loadIni($configFile){
		return parse_ini_file($configFile,true);
	}
	
	/**
	 * 读取XML格式的配置文件
	 *
	 * @param string $configFile
	 * @return array
	 */
	private static function loadXml($configFile){
		$objs = simplexml_load_file($configFile);
		$arrays = object2array($objs);
		return $arrays;
	}
	
	/**
	 * 读取PHP格式的配置文件
	 *
	 * @param string $configFile
	 * @return array
	 */
	private static function loadPHP($configFile){
		/* @TODO 是否需要校验PHP格式为正确格式? */
		$array = require_once $configFile;
		return $array;
	}
}
?>