<?php
class AXION_APPLICATION {
	public function __construct() {
		/**
		 * 加载应用程序配置文件
		 */
		$userConfigFile = APPLICATION_PATH . DS . 'conf' . DS . 'config.xml';
		if (file_exists ( $userConfigFile )) {
			AXION_CONFIG::loadConfigFile ( $userConfigFile, 'axion', true );
		}
		
		/**
		 * 注册默认错误处理函数
		 */
		$debugLevel = AXION_CONFIG::get ( 'axion.debug.level' );
		switch ($debugLevel) {
			case 1 :
				$level = E_ALL;
				break;
			case 0 :
				$level = E_ERROR | E_PARSE;
				break;
		}
		
		/**
		 * 设置程序错误提示开关
		 * 
		 * 部署模式不打印错误
		 */
		error_reporting ( $level );
		
		set_error_handler ( array ($this, 'errorHandler' ), $level );
		
		/**
		 * 注册默认异常处理函数
		 */
		set_exception_handler ( array ($this, 'exceptionHandler' ) );
		
		/**
		 * 设置时区
		 */
		date_default_timezone_set ( AXION_CONFIG::get ( 'axion.misc.timezone' ) );
	}
	
	public function run(){
		
	}
	
	/**
	 * 默认错误处理方法
	 *
	 * @param int $errno 错误代码
	 * @param string $errstr 错误信息
	 * @param string $errfile 抛出错误的文件
	 * @param int $errline 出错行数
	 * @param array $errcontext
	 */
	public function errorHandler($errno, $errstr, $errfile, $errline, $errcontext) {
		
	}
	
	/**
	 * 默认异常处理方法
	 *
	 * @param object $e
	 */
	public function exceptionHandler($e) {
		if ($e instanceof AXION_EXCEPTION) {
			if ($e->isHalt ()) {
				//@todo 替换为自定义的终止函数
				echo $e->__toString ();
			}
		}
	}
}
?>