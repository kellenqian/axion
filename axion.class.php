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
	 * 是否载入了缓存文件
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
		if (substr ( PHP_VERSION, 0, 3 ) < 5.2)
			exit ( 'Axion Framework Requires PHP Version 5.2.x' );
		
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
		 * 定义当前PHP是否运行于CLI模式下的标志
		 */
		define ( 'IS_CLI', PHP_SAPI == 'cli' ? true : false );
		
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
				$isSHM = true;
			}
		}
		
		/**
		 * 定义是否使用了共享内存
		 */
		define ( 'IS_SHM', $isSHM ? true : false );
		
		/**
		 * 定义框架默认使用的临时目录路径
		 */
		define ( 'TEMP_PATH', OS == 'windows' ? getenv ( 'TEMP' ) : $appTmpPath );
		
		/**
		 * 定义当前AXION所在路径
		 */
		if (! defined ( 'AXION_PATH' )) {
			define ( 'AXION_PATH', dirname ( __FILE__ ) );
		}
		
		/**
		 * 检测应用程序是否定义了合法的路径
		 */
		if (! defined ( 'APPLICATION_PATH' )) {
			exit ( 'Please DEFINE "APPLICATION_PATH"' );
		} elseif (! (APPLICATION_PATH)) {
			exit ( '"APPLICATION_PATH" is Illegal' );
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
		 * 加载AXION基础函数库
		 */
		require AXION_PATH . DS . 'common/functions.php';
		
		/**
		 * 加载AXION启动所必须的类
		 */
		require AXION_PATH . DS . 'lib' . DS . 'axion' . DS .'config.class.php';
		require AXION_PATH . DS . 'lib' . DS . 'axion' . DS .'application.class.php';
		
		/**
		 * 加载AXION框架默认配置文件
		 */
		AXION_CONFIG::loadConfigFile ( AXION_PATH . DS . 'common' . DS . 'config.xml' );
		
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
	public static function loadCachedClass(){
		
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
		/*@todo 需要完成二次启动自动加载相应文件的功能 */
		$package_array = split ( '_', $package_name );
		$file_array [] = strtolower ( join ( DS, $package_array ) );
		array_push ( $package_array, array_pop ( $package_array ) . '.class' );
		$file_array [] = strtolower ( join ( DS, $package_array ) );
		
		$path_array = explode ( PATH_SEPARATOR, get_include_path () );
		
		foreach ( $path_array as $path ) {
			foreach ( $file_array as $file ) {
				$fullPath = trim ( $path, DS ) . DS . $file . '.php';
				if (file_exists ( $fullPath )) {
					require_once $fullPath;
					self::$loaded_class [$package_name] = $fullPath;
					self::$new_class_found = true;
				}
			}
		}
	}
	
	/**
	 * 框架析构函数
	 *
	 */
	public function __destruct(){
		self::$AXION_RUN_TIME = microtime(true);
	}
}

?>