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
	 * 默认为错误
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
}
?>