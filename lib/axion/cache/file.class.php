<?php
class AXION_CACHE_FILE implements AXION_INTERFACE_CACHE {
	protected $config = array ();
	
	protected static $_data;
	
	public function get($key) {
		$hash = md5 ( $key );
		if (self::$_data [$hash]) {
			return unserialize ( self::$_data [$hash] );
		}
		
		$fileName = $this->genFileName ( $key );
		
		if (file_exists ( $fileName ) && is_readable ( $fileName )) {
			$content = file_get_contents ( $fileName );
			
			$expireTime = substr ( $content, 0, 9 );
			
			$lastUpdate = filemtime ( $fileName );
			
			if ($expireTime != - 1 && TIME - $lastUpdate > $expireTime) {
				if (is_writeable ( $fileName )) {
					@unlink ( $fileName );
					return false;
				}
			}
			
			$content = substr ( $content, 9 );
			
			if (IS_SHM && function_exists ( 'gzuncompress' )) {
				$content = gzuncompress ( $content );
			}
			
			return unserialize ( $content );
		}
		
		return false;
	}
	
	public function set($key, $value, $expire = '') {
		$freeSpace = disk_free_space($this->config['path']);
		$freeSpaceMB = $freeSpace ? floor($freeSpace /1024/1024) : 0;
		
		if($freeSpaceMB < 10){
			Axion_log::getinstance()->newMessage('磁盘容量过小，无法存储数据缓存文件',AXION_LOG::WARNING);
			return false;
		}
		
		$hash = md5 ( $key );
		
		$expire = $expire ? $expire : $this->config ['expire'];
		
		$expire = sprintf ( '%09d', $expire );
		
		$data = serialize ( $value );
		
		self::$_data [$hash] = $data;
		
		if (! is_dir ( $this->config ['path'] )) {
			if (! AXION_UTIL_FILE::mkdir ( $this->config ['path'] )) {
				throw new AXION_EXCEPTION ( '无法创建Cache文件目录:' . $this->config ['path'] );
			}
		}
		
		$fileName = $this->genFileName ( $key );
		
		if (IS_SHM && function_exists ( 'gzcompress' )) {
			$data = gzcompress ( $data, 4 );
		}
		
		$data = $expire . $data;
		
		$result = file_put_contents ( $fileName, $data );
		
		if ($result) {
			clearstatcache ();
			return true;
		} else {
			return false;
		}
	}
	
	public function delete($key) {
		$fileName = $this->genFileName ( $key );
		
		if (is_writable ( $fileName )) {
			return unlink ( $fileName );
		}
		
		return false;
	}
	
	public function flush() {
	
	}
	
	protected function genFileName($key) {
		$hash = md5 ( $key );
		$fileName = $this->config ['path'] . DS . $this->config ['prefix'] . '_' . $hash . '.cache';
		
		return $fileName;
	}
	
	public function setOptions(array $options) {
		$options = array_change_key_case_recursive ( $options );
		
		foreach ( $options as $k => $v ) {
			$this->config [$k] = $v;
		}
		
		if (! $this->config ['path'])
			throw new AXION_EXCEPTION ( '必须配置Cache文件存储路径' );
	}
}
?>