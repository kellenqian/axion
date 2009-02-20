<?php
Class AXION_CONFIG{
	private static $container = array();
	
	public static function setEntry($key , $value){
		self::$container[$key] = $value;	
	}
	
	public static function init(){
		if(true == file_exists($iniFile = AXION_PATH.DS.'common'.DS.'config.ini')){
			$axionConfig = parse_ini_file($iniFile,true);
			
			$axionConfig = array_change_key_case(array_map('array_change_key_case',$axionConfig));
			
			self::setEntry('default' , $axionConfig);
		}
	}
	
	public static function loadConfigFile($configFile){
		if(!file_exists($configFile)){
			return false;
		}
		
		$newConfigArray = parse_ini_file($configFile,true);
		
		$newConfig = array_change_key_case(array_map('array_change_key_case',$newConfigArray));
		
		self::setEntry('user_defined',$newConfig);
		
		return true;
	}
}
?>