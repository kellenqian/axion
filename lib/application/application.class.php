<?php
class Application extends Base {
	public function __construct() {
		$this->initialize ();
		$this->run;
		p(number_format(microtime(true) - $_ENV['AXION_START_TIME'],10));
	}
	
	public function initialize() {
		/**
		 * 转义GET/POST参数
		 */
		$_GET = daddslashes ( $_GET );
		$_POST = daddslashes ( $_POST );
		
		/**
		 * 设置项目自动加载目录
		 */
		$currentIncludePath = get_include_path ();
		$appIncludePath = APP_MODEL_PATH . DS . PATH_SEPARATOR . APP_DISPATCHER_PATH . DS . PATH_SEPARATOR;
		set_include_path ( $appIncludePath . $currentIncludePath );
		
		/**
		 * 注册自动加载函数
		 */
		spl_autoload_register ( array ($this, 'appAutoload' ) );
	}
	
	public function run() {
		
	}
	
	/**
	 * 应用程序自动加载函数
	 *
	 * @param string $str_className
	 * @return bool
	 */
	public function appAutoload($str_className) {
		if (! @include_once $str_className . '.class.php') {
			return false;
		} else {
			return true;
		}
	}
}

?>