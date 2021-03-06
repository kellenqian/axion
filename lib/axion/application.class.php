<?php
/**
 * AXION应用程序LOADER
 * 
 * loader完成两部分工作
 * 
 * 1、对于应用程序底层的基础的配置
 * 以及初始化工作。
 * 
 * 包括初始化控制器、过滤器、调度器
 * 
 * 设置错误级别
 * 
 * 创建临时目录
 * 
 * 设置时区等
 * 
 * 2、启动应用程序完成具体业务流程
 * 
 * @package AXION
 * @author kellenqian
 * @copyright techua.com
 *
 */

class AXION_APPLICATION {
	/**
	 * 应用程序唯一码 
	 *
	 * @var md5 string
	 */
	private static $uniqueId;
	
	/**
	 * ACTION对象实例
	 *
	 * @var AXION_CONTROLLER_INTERFACE
	 */
	private static $actionInstance;
	
	/**
	 * 当前请求模块名
	 *
	 * @var string
	 */
	private static $module;
	
	/**
	 * 当前请求动作名
	 *
	 * @var string
	 */
	private static $controller;
	
	/**
	 * 当前请求方法类型
	 *
	 * @var string
	 */
	private static $method;
	
	/**
	 * 当前请求渲染器名
	 *
	 * @var string
	 */
	private static $responseFormat;
	
	public static function getInstance() {
		static $_applicationInstance;
		if ($_applicationInstance) {
			return $_applicationInstance;
		}
		
		$_applicationInstance = new self ();
		return $_applicationInstance;
	}
	
	/**
	 * 构造函数
	 *
	 * 初始化应用程序
	 */
	public function __construct() {
		/**
		 * 计算应用程序的唯一ID
		 */
		self::$uniqueId = md5 ( APPLICATION_PATH );
		
		/**
		 * 定义应用程序程序所需的临时文件目录常量
		 */
		define ( 'DATA_CACHE_PATH', TEMP_PATH . DS . 'axion_' . self::$uniqueId . DS . 'datacache' );
		define ( 'DB_CACHE_PATH', TEMP_PATH . DS . 'axion_' . self::$uniqueId . DS . 'dbcache' );
		define ( 'CODE_CACHE_PATH', TEMP_PATH . DS . 'axion_' . self::$uniqueId . DS . 'codecache' );
		
		/**
		 * 定义应用程序各个库路径常量
		 */
		define ( 'APP_LIB_PATH', APPLICATION_PATH . DS . 'lib' );
		define ( 'APP_CONFIG_PATH', APPLICATION_PATH . DS . 'config' );
		define ( 'APP_CONTROLLER_PATH', APP_LIB_PATH . DS . 'controller' );
		define ( 'APP_MODEL_PATH', APP_LIB_PATH . DS . 'model' );
		define ( 'APP_TEMPLATE_PATH', APP_LIB_PATH . DS . 'template' );
		
		/**
		 * 加载应用程序配置文件
		 */
		$userConfigFile = APP_CONFIG_PATH . DS . 'config.xml';
		if (file_exists ( $userConfigFile )) {
			AXION_CONFIG::loadConfigFile ( $userConfigFile, 'axion', true );
		}
		
		/**
		 * 设置应用程序自动加载目录
		 */
		Axion::addIncludePath ( APP_LIB_PATH ); //应用程序程序库目录
		Axion::addIncludePath ( APP_CONTROLLER_PATH ); //控制器目录
		Axion::addIncludePath ( APP_MODEL_PATH ); //模型目录
		

		/**
		 * 注册默认错误处理函数
		 */
		$debugLevel = AXION_CONFIG::GET ( 'axion.debug.level' );
		switch ($debugLevel) {
			case 1 :
				$level = E_ALL;
				/**
				 * 确保无法被错误函数捕捉的错误可以打印
				 */
				ini_set ( 'display_errors', 1 );
				break;
			case 0 :
				$level = E_ERROR | E_PARSE;
				/**
				 * 部署模式关闭错误信息
				 */
				ini_set ( 'display_errors', 0 );
				break;
		}
		
		/**
		 * 设置程序错误提示开关
		 */
		error_reporting ( $level );
		
		set_error_handler ( array ($this, 'errorHandler' ), $level );
		
		/**
		 * 注册程序结束清理函数
		 */
		register_shutdown_function ( array ($this, 'shutdownHandler' ) );
		
		/**
		 * 注册默认异常处理函数
		 */
		set_exception_handler ( array ($this, 'exceptionHandler' ) );
		
		/**
		 * 创建应用程序所需的临时文件目录
		 */
		$serverInitDone = true;
		if (! file_exists ( APPLICATION_PATH . DS . 'serverInit.done' )) {
			$tmpDirs = array ('data_cache' => DATA_CACHE_PATH, 'db_cache' => DB_CACHE_PATH, 'view_cache' => VIEW_CACHE_PATH, 'code_cache' => CODE_CACHE_PATH );
			foreach ( $tmpDirs as $v ) {
				if (! is_dir ( $v )) {
					AXION_UTIL_FILE::mkdir ( $v, 0755 );
				}
			}
			$serverInitDone = false;
		}
		define ( 'IS_SERVER_INIT_DONE', $serverInitDone );
		
		/**
		 * 开启SESSION支持
		 */
		session_start (); //@todo 暂时开启默认SESSION支持，回头需要改为自定义版本
		

		/**
		 * 设置时区
		 */
		date_default_timezone_set ( AXION_CONFIG::get ( 'axion.misc.timezone' ) );
	}
	
	/**
	 * 运行应用程序
	 */
	public function run() {
		
		$dispatcher = AXION_DISPATCHER::getInstance ();
		
		$params = $dispatcher->analyse ();
		
		$module = $params ['module'];
		
		$controller = $params ['controller'];
		
		$appClass = $module . '_' . $controller;
		
		if (! class_exists ( $appClass )) {
			throw new AXION_EXCEPTION ( '无法找到控制器' );
		}
		
		define ( 'ACTION_ClASS', $appClass );
		
		self::$module = $module;
		
		self::$controller = $controller;
		
		/**
		 * 合并调度后的URL参数到$_GET 与 $_REQUEST
		 */
		if (isset ( $params ['params'] )) {
			$_GET = array_merge ( $_GET, $params ['params'] );
			$_REQUEST = array_merge ( $_REQUEST, $_GET );
		}
		
		//捕获控制器的所有非法输出
		//ob_start ();
		

		//实例化控制器对象
		$controller = new $appClass ();
		
		if (! $controller instanceof AXION_INTERFACE_CONTROLLER) {
			throw new AXION_EXCEPTION ( '非法的控制器对象' );
		}
		
		self::$actionInstance = $controller;
		
		//执行action
		$controller->run ();
		
		//获取控制器响应模式
		$response = AXION_REQUEST::getResponseFormat ();
		
		//定义响应模式常量
		self::$responseFormat = $response;
		
		//获取控制器请求方法
		$method = AXION_REQUEST::getRequestMethod ();
		self::$method = $method;
		
		//设置控制器响应模式
		if (! $controller->responseTo ()) {
			$controller->responseTo ( $response );
		}
		
		//记录程序执行数据
		//$initMessage = "Processing sel" . "Controller#" . self::$controller . " (for " . IP . " at " . date ( 'Y-m-d H:i:s' ) . ") [" . self::$method . "]";
		//Axion_log::log ( $initMessage, Axion_log::INFO, 'run' );
		

		//实例化渲染器对象
		$render = new AXION_RENDER ( $controller );
		
		//$extOutput = ob_get_contents ();
		

		//ob_end_clean ();
		

		//获取渲染后的数据
		$output = $render->render ();
		
		echo $output;
		
		if (! IS_SERVER_INIT_DONE) {
			file_put_contents ( APPLICATION_PATH . DS . 'serverInit.done', self::$uniqueId );
		}
	}
	
	/**
	 * 获取当前应用程序的唯一ID
	 *
	 * @return string
	 */
	public static function getUniqueId() {
		return self::$uniqueId;
	}
	
	public static function getActionInstance() {
		return self::$actionInstance;
	}
	
	public static function getModule() {
		return self::$module;
	}
	
	public static function getController() {
		return self::$controller;
	}
	
	public static function getMethod() {
		return self::$method;
	}
	
	public static function getResponseFormat() {
		return self::$responseFormat;
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
		$errcontext;
		$str = '在' . $errfile . '文件中的第' . $errline . '行发生了错误:' . $errstr;
		Axion_log::log ( $str, $errno );
	}
	
	/**
	 * 默认异常处理方法
	 *
	 * @param object $e
	 */
	public function exceptionHandler($e) {
		if ($e instanceof AXION_EXCEPTION) {
			p ( $e->__toString () );
		}
	}
	
	public function shutdownHandler() {
		$error = error_get_last ();
		if (! empty ( $error )) {
			$errlevel = $error ['type'];
			$errfile = $error ['file'];
			$errline = $error ['line'];
			$errstr = $error ['message'];
			
			if ($errlevel == (E_ERROR || E_PARSE)) {
				$str = '在' . $errfile . '文件中的第' . $errline . '行发生了错误:' . $errstr;
				Axion_log::log ( $str, $errlevel );
			}
		}
		
		$this->applicationTeminated ();
	}
	
	public function applicationTeminated() {
		$runtime = AXION_UTIL::excuteTime ();
		$memUseage = number_format ( memory_get_usage () / 1024 ) . 'k';
		
		Axion_log::log ( '程序执行时间:' . $runtime );
		Axion_log::log ( '本次内存使用:' . $memUseage );
		
		$logs = Axion_log::getLogPool ();
		
		if (Axion_log::isOverLimit ()) {
			array_shift ( $logs );
			$warningMessage = "超过日志容量限制(" . axion_log::getPoolLimit () . ")，日志系统将自头删除数据保障程序工作";
			$warning = array ('int_lv' => axion_log::WARNING, 'str_msg' => $warningMessage );
			array_unshift ( $logs, $warning );
		}
		
		if (IS_FIREPHP && AXION_CONFIG::get ( 'axion.debug.usefirephp' )) {
			$fb = AXION_UTIL_FIREPHP::getInstance ( true );
			foreach ( $logs as $v ) {
				switch ($v ['int_lv']) {
					case Axion_log::WARNING :
						$fb->warn ( $v ['str_msg'] );
						break;
					case Axion_log::NOTICE :
						$fb->info ( $v ['str_msg'] );
						break;
					case Axion_log::ERROR :
						$fb->error ( $v ['str_msg'] );
						break;
					case Axion_log::INFO :
						$fb->log ( $v ['str_msg'] );
						break;
				}
			}
		} else {
			P ( $logs );
		}
	}
}
?>