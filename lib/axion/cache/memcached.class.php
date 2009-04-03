<?php
class AXION_CACHE_MEMCACHED implements AXION_INTERFACE_CACHE {
	/**
	 * memcache实例对象
	 *
	 * @var Memcache
	 */
	private $instance;
	
	private $config;
	
	public function __construct() {
		if (! class_exists ( 'MEMCACHE', false )) {
			throw new AXION_EXCEPTION ( '无法找到MEMCACHED扩展' );
		}
		
		$this->instance = new Memcache ( );
		
		$config = AXION_CONFIG::get ( 'axion.connections.memcached.servers' );
		$count = sizeof ( $config );
		if ($count > 1 && ! array_key_exists ( 'host', $config )) {
			for($i = 0; $i < $count; $i ++) {
				if ($config [$i] ['weight'] > $count) {
					$config [$i] ['weight'] = $count;
				}
				$this->instance->addServer ( $config [$i] ['host'], $config [$i] ['port'], true, $config [$i] ['weight'], 1, 15, true, array ('AXION_CACHE_MEMCACHED', 'failure' ) );
			}
		} else {
			$this->instance->addServer ( $config ['host'], $config ['port'], false, 1, 1, 15, true, array ('AXION_CACHE_MEMCACHED', 'failure' ) );
		}
	}
	
	public function failure($host, $port) {
		Axion_log::getinstance()->newMessage("MEMCACHED服务器$host:$port发生故障",Axion_log::WARNING);
	}
	
	public function set($key, $value, $expire = '') {
		if (! $expire) {
			$expire = $this->config ['expire'];
		}
		return $this->instance->set ( $key, $value, 0, $expire );
	}
	
	public function get($key) {
		return $this->instance->get ( $key );
	}
	
	public function delete($key) {
		return $this->instance->delete ( $key );
	}
	
	public function flush() {
		return $this->instance->flush ();
	}
	
	public function getStatus() {
		return $this->instance->getExtendedStats ();
	}
	
	public function setOptions(array $options) {
		foreach ( $options as $k => $v ) {
			$this->config [$k] = $v;
		}
	}
}
?>