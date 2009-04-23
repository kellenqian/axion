<?php
interface AXION_INTERFACE_CACHE{
	public function get($key);
	
	public function set($key , $value , $expire = '');
	
	public function delete($key);
	
	public function flush();
	
	public function setOptions(array $options);
}
?>