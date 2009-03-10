<?php
class AXION_CACHE_MEMCACHED implements AXION_INTERFACE_CACHE {
	private $instance;
	
	public function __construct() {
		if (! class_exists ( 'MEMCACHE', false )) {
			throw new AXION_EXCEPTION ( '无法找到MEMECACHED扩展' );
		}
		
		$this->instance = new Memcache ( );
		
		$config = AXION_CONFIG::get ( 'axion.connections.memcached.session' );
		
		if (sizeof ( $config ) > 1 && ! array_key_exists ( 'host', $config )) {
			for($i = 0; $i < sizeof ( $config ); $i ++) {
				$this->instance->addServer ( $config [$i] ['host'], $config [$i] ['port'], true, $config [$i] ['weight'], 1, 15, true, array ($this, 'failure' ) );
			}
		} else {
			$this->instance->addServer ( $config ['host'], $config ['port'], true, 0, 1, 15, true, array ($this, 'failure' ) );
		}
	}
	
	public function failure($host, $port) {
	
	}
	
	public function set($key, $value, $expire = '') {
	
	}
	
	public function get($key) {
	
	}
	
	public function delete($key) {
	
	}
	
	public function flush() {
	
	}
	
	public function setOptions(array $options){
		
	}
}
?>