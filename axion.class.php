<?php

/**
 * 注册框架自动加载函数
 */
spl_autoload_register ( array ('Axion', 'autoloadClass' ) );

/**
 * Axion框架核心类
 * 
 * 完成框架初始化工作
 * 
 * @package AXION
 * @author kellenqian
 * @copyright techua.com
 *
 */
class Axion {
	/**
	 * 框架开始执行时间
	 *
	 * @var float
	 */
	public static $AXION_START_TIME;
	/**
	 * 框架初始化完成时间
	 *
	 * @var float
	 */
	public static $AXION_INIT_TIME;
	/**
	 * 框架执行程序完成时间
	 *
	 * @var float
	 */
	public static $AXION_RUN_TIME;
	
	/**
	 * 是否发现新的加载文件
	 *
	 * @var bool
	 */
	public static $new_class_found = false;
	/**
	 * 已经载入的类
	 *
	 * @var array
	 */
	public static $loaded_class = array ();
	/**
	 * 代码缓存文件名
	 *
	 * @var unknown_type
	 */
	public static $load_cache_file;
	
	/**
	 * 框架初始化函数
	 *
	 */
	public function __construct() {
		/**
		 * 检测PHP版本，必须高于5.2.0
		 */
		if (version_compare ( PHP_VERSION, '5.2.0', '<' ))
			exit ( 'Axion Framework Requires PHP Version >= 5.2' );
		
		/**
		 * 记录程序开始执行的时间点
		 */
		self::$AXION_START_TIME = microtime ( true );
		
		/**
		 * 定义自适应系统的目录分隔符
		 * linux:'/' windows:'\'
		 */
		define ( 'DS', DIRECTORY_SEPARATOR );
		
		/**
		 * 定义当前时间戳
		 */
		define ( 'TIME', time () );
		
		/**
		 * 定义当前操作系统类型(估计没人会用除了这两种以外的系统吧)
		 */
		define ( 'OS', DS == '\\' ? 'windows' : 'linux' );
		
		/**
		 * 定义客户端浏览器版本
		 */
		$agent = strtolower ( $_SERVER ['HTTP_USER_AGENT'] );
		
		switch ($agent) {
			case ( bool ) strpos ( $agent, 'msie 6' ) :
				$browser = 'msie6';
				break;
			case ( bool ) strpos ( $agent, 'msie 7' ) :
				$browser = 'msie7';
				break;
			case ( bool ) strpos ( $agent, 'msie 8' ) :
				$browser = 'msie8';
				break;
			case ( bool ) strpos ( $agent, 'firefox' ) :
				$browser = 'firefox';
				break;
			case ( bool ) strpos ( $agent, 'chrome' ) :
				$browser = 'chrome';
				break;
			case ( bool ) strpos ( $agent, 'safari' ) :
				$browser = 'safari';
				break;
			case ( bool ) strstr ( $agent, 'opera' ) :
				$browser = 'opera';
				break;
			default :
				$browser = 'unknow';
		}
		define ( 'BROWSER', $browser );
		
		/**
		 * 定义当前浏览器为FIREFOX时是否安装了FIREPHP扩展
		 */
		$isFIREPHP = (($browser == 'firefox') && strpos ( $agent, 'firephp' )) ? true : false;
		define ( 'IS_FIREPHP', $isFIREPHP );
		
		/**
		 * 判断是否可以使用共享内存
		 */
		$isSHM = false;
		if (OS == 'linux') {
			/** 如果共享内存可写则使用内存作为临时目录存储空间 **/
			if (is_writable ( '/dev/shm' )) {
				$appTmpPath = '/dev/shm';
				$isSHM = true;
			} else {
				$appTmpPath = '/tmp';
			}
		}
		
		/**
		 * 定义是否使用了共享内存
		 */
		define ( 'IS_SHM', $isSHM );
		
		/**
		 * 定义框架当前是否运行于CLI模式下
		 */
		define ( 'IS_CLI', PHP_SAPI == 'cli' ? true : false );
		
		/**
		 * 定义框架默认使用的临时目录路径
		 */
		define ( 'TEMP_PATH', OS == 'windows' ? getenv ( 'TEMP' ) : $appTmpPath );
		
		/**
		 * 定义当前AXION所在路径
		 */
		if (! defined ( 'AXION_PATH' )) {
			define ( 'AXION_PATH', realpath ( dirname ( __FILE__ ) ) );
		}
		
		/**
		 * 定义当前应用程序所在目录
		 */
		if (! defined ( 'APPLICATION_PATH' )) {
			if (IS_CLI) {
				$pwd = $_ENV ['OLDPWD'];
				if (! $pwd) {
					exit ( 'PLEASE DEFINE "APPLICATION_PATH"' );
				}
			} else {
				$pwd = getcwd ();
			}
			define ( 'APPLICATION_PATH', $pwd );
		}
		
		/**
		 * 定义AXION必要的常量
		 */
		
		//框架目录常量
		define ( 'AXION_LIB_PATH', AXION_PATH . DS . 'lib' ); //AXION框架代码库目录
		

		/**
		 * 设置框架includePath
		 */
		self::addIncludePath ( AXION_LIB_PATH );
		
		/**
		 * 加载AXION基础常量库
		 */
		require AXION_PATH . DS . 'common/defines.php';
		
		/**
		 * 加载AXION基础函数库
		 */
		require AXION_PATH . DS . 'common/functions.php';
		
		/**
		 * 加载AXION启动所必须的类
		 */
		require AXION_PATH . DS . 'lib' . DS . 'axion' . DS . 'config.class.php';
		require AXION_PATH . DS . 'lib' . DS . 'axion' . DS . 'application.class.php';
		
		/**
		 * 注册默认异常处理函数
		 */
		set_exception_handler ( array ($this, 'exceptionHandler' ) );
		
		/**
		 * 加载AXION框架默认配置文件
		 */
		AXION_CONFIG::loadConfigFile ( AXION_PATH . DS . 'common' . DS . 'config.xml' );
		
		/**
		 * 加载框架缓存文件
		 */
		self::loadCachedClass ();
		
		/**
		 * 记录框架初始化完成时间 
		 */
		self::$AXION_INIT_TIME = microtime ( true );
	}
	
	/**
	 * 应用程序启动入口方法
	 *
	 */
	public function Run() {
		$app = new AXION_APPLICATION ( );
		$app->run ();
	}
	
	/**
	 * 载入可以缓存加载的文件
	 *
	 */
	private static function loadCachedClass() {
		if (AXION_CONFIG::GET ( 'axion.debug.level' ) == 1)
			return;
		
		if (! IS_CLI)
			$prefix = $_SERVER ['HTTP_HOST'] ? $_SERVER ['HTTP_HOST'] : '';
		else
			$prefix = join ( '_', $_SERVER ['argv'] );
		
		$url = $prefix . $_SERVER ['PHP_SELF'];
		$urlHash = md5 ( $url );
		$cacheFile = TEMP_PATH . DS . 'axion_' . md5 ( APPLICATION_PATH ) . DS . 'codecache' . DS . $urlHash . '.php';
		
		self::$load_cache_file = $cacheFile;
		if (file_exists ( $cacheFile )) {
			$codeFile = unserialize ( file_get_contents ( $cacheFile ) );
			
			self::$loaded_class = $codeFile;
			foreach ( $codeFile as $v ) {
				require_once $v;
			}
		}
	}
	
	/**
	 * 设置自动加载目录
	 *
	 * @param mix $path 目标目录
	 */
	public static function addIncludePath($path) {
		$currentIncludePath = explode ( PATH_SEPARATOR, get_include_path () );
		
		array_unshift ( $currentIncludePath, $path );
		
		$newIncludePath = join ( PATH_SEPARATOR, array_unique ( $currentIncludePath ) );
		
		set_include_path ( $newIncludePath );
	}
	
	/**
	 * 自动加载类方法
	 *
	 * @param string $package_name 类名
	 */
	public static function autoloadClass($package_name) {
		$package_array = split ( '_', $package_name );
		$file_array [] = strtolower ( join ( DS, $package_array ) );
		array_push ( $package_array, array_pop ( $package_array ) . '.class' );
		$file_array [] = strtolower ( join ( DS, $package_array ) );
		$path_array = explode ( PATH_SEPARATOR, get_include_path () );
		foreach ( $path_array as $path ) {
			foreach ( $file_array as $file ) {
				$fullPath = rtrim ( $path, DS ) . DS . $file . '.php';
				if (file_exists ( $fullPath )) {
					require_once $fullPath;
					self::$loaded_class [$package_name] = $fullPath;
					self::$new_class_found = true;
				}
			}
		}
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
	
	/**
	 * 框架析构函数
	 *
	 */
	public function __destruct() {
		/** 防止程序意外退出执行析构函数 */
		if (! defined ( 'APPLICATION_PATH' )) {
			exit ();
		}
		/**
		 * 存储本次程序新加载的文件列表
		 */
		if (self::$new_class_found) {
			file_put_contents ( self::$load_cache_file, serialize ( self::$loaded_class ) );
		}
		
		self::$AXION_RUN_TIME = microtime ( true );
		$runtime = AXION_UTIL::excuteTime ();
		$memUseage = number_format ( memory_get_usage () / 1024 ) . 'k';
		
		Axion_log::getinstance ()->newMessage ( '框架执行时间:' . $runtime );
		Axion_log::getinstance ()->newMessage ( '本次内存使用:' . $memUseage );
		
		$logs = Axion_log::getinstance ()->getAllData ();
		
		if (IS_FIREPHP) {
			$fb = AXION_UTIL_FIREPHP::getInstance ( true );
			foreach ( $logs as $v ) {
				switch ($v ['int_lv']) {
					case E_WARNING :
						$fb->warn ( $v ['str_msg'] );
						break;
					case E_NOTICE :
						$fb->info ( $v ['str_msg'] );
						break;
					case E_ERROR :
						$fb->error ( $v ['str_msg'] );
						break;
					case Axion_log::INFO :
						$fb->log ( $v ['str_msg'] );
						break;
					case Axion_log::EXCEPTION :
						$fb->error ( $v ['str_msg'] );
						break;
				}
			}
		}
	}
}

?>