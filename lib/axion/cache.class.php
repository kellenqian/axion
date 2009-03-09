<?php
class AXION_CACHE {
	/**
	 * 获取缓存对象实例
	 *
	 * @param string $cacheHandlerName
	 * @return AXION_INTERFACE_CACHE
	 */
	public static function getInstance($cacheHandlerName) {
		static $_handler = array();
		$cacheHandlerName = $cacheHandlerName ? 
							$cacheHandlerName :
							AXION_CONFIG::get('axion.cache.handler');
		
		if($_handler[$cacheHandlerName]){
			return $_handler[$cacheHandlerName];
		}else{
			$cacheInstance = self::factory($cacheHandlerName);
			$_handler[$cacheHandlerName] = $cacheInstance;
			
			return $cacheInstance;
		}
	}
	
	private static function factory($handlerName){
		$className = strtolower($handlerName);
		if('file' == $className || 'memcached' == $className){
			$className = 'AXION_CACHE_'.$className;
		}
		
		$instance = new $className;
		
		if($instance instanceof AXION_INTERFACE_CACHE){
			$config = AXION_CONFIG::get('axion.cache');
			$config['path'] = $config['path'] ? $config['path'] : DATA_CACHE_PATH;
			$instance->setOptions($config);
			return $instance;
		}else{
			throw new AXION_EXCEPTION('该对象并非合法的缓存对象');
		}
	}
}
?>