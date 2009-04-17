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
	 * 当前请求控制器名
	 *
	 * @var string
	 */
	private static $controller;
	
	/**
	 * 当前请求动作名
	 *
	 * @var string
	 */
	private static $action;
	
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
		 * 加载应用程序配置文件
		 */
		$userConfigFile = APPLICATION_PATH . DS . 'conf' . DS . 'config.xml';
		if (file_exists ( $userConfigFile )) {
			AXION_CONFIG::loadConfigFile ( $userConfigFile, 'axion', true );
		}
		
		/**
		 * 设置应用程序自动加载目录
		 */
		Axion::addIncludePath ( APPLICATION_PATH . DS . 'lib' . DS . 'controller' ); //控制器目录
		Axion::addIncludePath ( APPLICATION_PATH . DS . 'lib' . DS . 'model' ); //模型目录
		

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
		 * 定义应用程序程序所需的临时文件目录常量
		 */
		define ( 'DATA_CACHE_PATH', TEMP_PATH . DS . 'axion_' . self::$uniqueId . DS . 'datacache' );
		define ( 'DB_CACHE_PATH', TEMP_PATH . DS . 'axion_' . self::$uniqueId . DS . 'dbcache' );
		define ( 'VIEW_CACHE_PATH', TEMP_PATH . DS . 'axion_' . self::$uniqueId . DS . 'viewcache' );
		define ( 'CODE_CACHE_PATH', TEMP_PATH . DS . 'axion_' . self::$uniqueId . DS . 'codecache' );
		
		/**
		 * 定义应用程序各个库路径常量
		 */
		define ( 'APP_CONTROLLER_PATH', APPLICATION_PATH . DS . 'lib' . DS . 'controller' );
		define ( 'APP_MODEL_PATH', APPLICATION_PATH . DS . 'lib' . DS . 'model' );
		define ( 'APP_TEMPLATE_PATH', APPLICATION_PATH . DS . 'lib' . DS . 'template' );
		define ( 'APP_ORM_PATH', APPLICATION_PATH . DS . 'lib' . DS . 'orm' );
		
		/**
		 * ORM数据表结构映射文件存储路径
		 */
		/** @todo 不够优美，是否回头考虑移除? */
		define ( 'APP_ORM_MAP_PATH', APPLICATION_PATH . DS . 'lib' . DS . 'orm' );
		
		/**
		 * 创建应用程序所需的临时文件目录
		 */
		/**@todo 是否移除这部分的判断一次性完成？ **/
		$tmpDirs = array ('data_cache' => DATA_CACHE_PATH, 'db_cache' => DB_CACHE_PATH, 'view_cache' => VIEW_CACHE_PATH, 'code_cache' => CODE_CACHE_PATH );
		
		foreach ( $tmpDirs as $v ) {
			if (! is_dir ( $v )) {
				AXION_UTIL_FILE::mkdir ( $v, 0755 );
			}
		}
		
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
		$dispatcherClass = AXION_CONFIG::GET ( 'axion.dispatcher.class' );
		
		$dispatcher = new $dispatcherClass ( );
		
		if (! ($dispatcher instanceof AXION_INTERFACE_DISPATCHER)) {
			throw new AXION_EXCEPTION ( '无效的调度器对象' );
		}
		
		$params = $dispatcher->getParams ();
		AXION_CONFIG::set( 'axion.application', $params );
		$controller = $params ['controller'];
		
		$action = $params ['action'];
		
		$appClass = $controller . '_' . $action;
		
		if (! class_exists ( $appClass )) {
			throw new AXION_EXCEPTION ( '无法找到控制器' );
		}
		
		define ( 'ACTION_ClASS', $appClass );
		
		self::$controller = $controller;
		
		self::$action = $action;
		
		//捕获控制器的所有非法输出
		//ob_start ();
		
		//实例化控制器对象
		$action = new $appClass ( );
		
		if (! $action instanceof AXION_INTERFACE_CONTROLLER) {
			throw new AXION_EXCEPTION ( '非法的控制器对象' );
		}
		
		//执行action
		$action->run ();
		
		//获取控制器响应模式
		$response = AXION_REQUEST::getResponseFormat ();
		
		//定义响应模式常量
		self::$responseFormat = $response;
		
		//获取控制器请求方法
		$method = AXION_REQUEST::getRequestMethod();
		self::$method = $method;
		
		//设置控制器响应模式
		if (! $action->responseTo ()) {
			$action->responseTo ( $response );
		}
		
		//记录程序执行数据
		$initMessage = "Processing $controller"."Controller#".self::$action.
					   " (for ".IP." at ".date('Y-m-d H:i:s').") [".self::$method."]"; 
		Axion_log::log($initMessage,Axion_log::INFO,'run');
		
		//实例化渲染器对象
		$render = new AXION_RENDER ( $action );
		
		$extOutput = ob_get_contents ();

		//ob_end_clean ();
		
		//获取渲染后的数据
		$output = $render->render ();
		
//		var_dump ( $output );
//		
//		var_dump ( $extOutput );
	
	}
	
	/**
	 * 获取当前应用程序的唯一ID
	 *
	 * @return string
	 */
	public static function getUniqueId() {
		return self::$uniqueId;
	}
	
	public static function getController() {
		return self::$controller;
	}
	
	public static function getAction() {
		return self::$action;
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
		
		if (IS_FIREPHP) {
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
//			P ( $logs );
		}
	}
}
?>