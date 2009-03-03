<?php
Class AXION_CACHE_FILE implements AXION_INTERFACE_CACHE{
	private $cachePath;
	
	private $expierTime;
	
	private $filePrefix;
	
	private static $_data;
	
	public function get($key){
		$hash = md5($key);
		if(self::$_data[$hash]){
			return unserialize(self::$_data[$hash]);
		}
		
		$fileName = $this->genFileName($key);
		
		if(file_exists($fileName) && is_readable($fileName)){
			$lastUpdate = filemtime($fileName);
			if(TIME - $lastUpdate > $this->expierTime){
				if(is_writeable($fileName)){
					@unlink($fileName);
				}
			}
			return unserialize(file_get_contents($fileName));
		}
		
		return false;
	}
	
	public function set($key , $value){
		/*@todo 缺少设置单个文件超时时间的部分 */
		if(TEMP_FREE_SPACE <= 1){
			/* @todo 记录日志 说明文件无法写入 */
			return false;
		}
		
		$hash = md5($key);

		$serializedValue = serialize($value);
		
		self::$_data[$hash] = $serializedValue;
		
		if(!is_dir($this->cachePath)){
			if (!AXION_UTIL_FILE::mkdir($this->cachePath)){
				throw new AXION_EXCEPTION('无法创建Cache文件目录:'.$this->cachePath);	
			}
		}
		
		$fileName = $this->genFileName($key);
		
		return file_put_contents($fileName , $serializedValue);
	}
	
	public function delete($key){
		$fileName = $this->genFileName($key);
		
		if(is_writable($fileName)){
			return unlink($fileName);
		}
		
		return false;
	}
	
	public function flush(){
		
	}
	
	private function genFileName($key){
		$hash = md5($key);
		$fileName = $this->cachePath . DS . $this->filePrefix . '_' . $hash . '.cache';
		
		return $fileName;
	}
	
	public function setOptions(array $options){
		$options = array_change_key_case_recursive($options);
		
		if(!$options['cachepath']){
			throw new AXION_EXCEPTION('必须配置Cache文件存储路径');
		}
		
		$this->cachePath = rtrim($options['cachepath'],DS);
		$this->expierTime = $options['expiretime'] ? $options['expiretime'] : 60;
		$this->filePrefix = $options['prefix'] ? $options['prefix'] : 'axion';
	}
}
?>