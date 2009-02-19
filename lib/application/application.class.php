<?php
class Axion_Application extends Base  {
	public function __construct() {
		parent::__construct();
		$this->initialize ();
		$this->run;
	}
	
	public function initialize() {
		/**
		 * 转义GET/POST参数
		 */
		$_GET = daddslashes ( $_GET );
		$_POST = daddslashes ( $_POST );
		
		/**
		 * 注册自动加载函数
		 */
		spl_autoload_register ( array ('Application', 'appAutoload' ) );
		
		$_config = require_once AXION_PATH . DS . 'common/default.php';
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