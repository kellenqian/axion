<?php
class AXION_CACHE {
	/**
	 * 获取缓存对象实例
	 *
	 * @param string $cacheHandlerName
	 * @return AXION_INTERFACE_CACHE
	 */
	public static function getInstance($cacheHandlerName = '') {
		static $_handler = array();
		$cacheHandlerName = $cacheHandlerName ? 
							$cacheHandlerName :
							AXION_CONFIG::get('axion.cache.handler');
				
		if( !empty( $_handler[$cacheHandlerName] ) ){
			return $_handler[$cacheHandlerName];
		}else{
			$cacheInstance = self::factory($cacheHandlerName);
			$_handler[$cacheHandlerName] = $cacheInstance;
			
			return $cacheInstance;
		}
	}
	
	private static function factory($handlerName){
		$className = strtolower($handlerName);
		/**@todo 有必要吗？直接给对应对象的详细路径即可 */
		if(in_array($className , array('file','memcached'))){
			$className = 'AXION_CACHE_'.$className;
		}
		
		$instance = new $className;
		
		if($instance instanceof AXION_INTERFACE_CACHE){
			$config = AXION_CONFIG::get('axion.cache');
			$instance->setOptions($config);
			return $instance;
		}else{
			throw new AXION_EXCEPTION('该对象并非合法的缓存对象');
		}
	}
}
?>