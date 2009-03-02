<?php
/**
 * AXION框架默认异常处理类
 * 
 * @package AXION
 * @author kellenqian
 * @copyright techua.com
 *
 */
Class AXION_EXCEPTION extends Exception{
	/**
	 * 异常级别
	 * 
	 * 默认为错误（该级别表示如果抛出异常则停止程序工作）
	 *
	 * @var int
	 */
	protected $level;
	
	/**
	 * 构造函数
	 *
	 * @param string $message 错误信息
	 * @param unknown_type $level 错误级别
	 */
	public function __construct($message , $level = E_ERROR){
		parent::__construct($message);
		$this->level = $level;
	}
	
	/**
	 * 查询是否为严重错误
	 *
	 * @return bool
	 */
	public function isHalt(){
		return $this->level == E_ERROR ? true : false;
	}
}
?>