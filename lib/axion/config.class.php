<?php
class AXION_CONFIG {
	private static $container;
	private static $locked = array ();
	
	private static function buildEvalCommand($package) {
		$packageTolower = strtolower ( $package );
		
		$hashKey = null;
		
		if (strpos ( $package, '.' )) {
			$packageArray = explode ( '.', $packageTolower );
			foreach ( $packageArray as $v ) {
				$hashKey .= "['$v']";
			}
		} else {
			$hashKey = "['$package']";
		}
		
		$run = '$configPointer = & self::$container' . $hashKey . ';';
		
		return $run;
	}
	
	public static function lock($package) {
		self::$locked [$package] = true;
	}
	
	public static function islock($package) {
		$packageTolower = strtolower ( $package );
		$hashKey = null;
		$hashKeyArray = array();
		if (strpos ( $package, '.' )) {
			$packageArray = explode ( '.', $packageTolower );
			foreach ( $packageArray as $v ) {
				$hashKeyArray[] = $v;
				$hashKey = join('.',$hashKeyArray);
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
	
	public static function set($package, $value, $lock = false) {
		if (self::islock ( $package )) {
			return false;
		}
		
		if (is_array ( $value )) {
			$value = array_change_key_case_recursive ( $value );
		}
		
		$run = self::buildEvalCommand ( $package );
		
		if (! $run) {
			return false;
		}
		
		eval ( $run );
	
		$configPointer = $value;
		
		/***/
		$arr = explode('.',$package);
		
		static $_arr;
		$p = & $_arr;
		foreach ($arr as $v){
			$p = &$p[$v]; 
		}
		$p = $value;
		p($_arr);
		/***/
		
		if ($lock) {
			self::lock ( $package );
		}
		
		return $configPointer;
	}
	
	public static function get($package = '') {
		if (empty ( $package )) {
			return self::$container;
		}
		
		$run = self::buildEvalCommand ( $package );
		
		if (! $run) {
			return false;
		}
		
		eval ( $run );
		
		return $configPointer;
	}
	
	public static function loadConfigFile($configFile, $package = 'axion' , $lock = false) {
		if (! file_exists ( $configFile )) {
			return false;
		}
		
		$newConfigArray = parse_ini_file ( $configFile, true );
		
		return self::set ( $package, $newConfigArray ,$lock);
	}
}
?>