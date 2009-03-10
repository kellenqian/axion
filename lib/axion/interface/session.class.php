<?php
interface AXION_INTERFACE_SESSION{
	public function open($savePath , $sessionName);
	
	public function close();
	
	public function read($key);
	
	public function write($key , $value);
	
	public function destroy($sessionId);
	
	public function gc($sessionLifetime);
}
?>