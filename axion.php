<?php
class Axion {
	/**
	 * Axion框架初始化文件
	 * @author kellenqian
	 * @copyright techua.com
	 */
	public static $AXION_START_TIME;
	public static $AXION_LOADED_FILE_TIME;
	
	public static $new_class_found = false;
	public static $loaded_class = array();
	public static $load_cache_file;
	
	private static function initialize() {
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
		 * 定义框架默认使用的临时目录路径
		 */
		define('TEMP_PATH',OS == 'windows' ? getenv('TEMP') : '/tmp');
		
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
		} elseif (false !== strpos ( APPLICATION_PATH, '.' )) {
			exit ( '"APPLICATION_PATH" is Illegal' );
		}
		
		/**
		 * 定义AXION必要的常量
		 */
		
		//框架目录常量
		define ( 'AXION_LIB_PATH', AXION_PATH . DS . 'lib' ); //AXION框架代码库目录
		define ( 'AXION_KERNEL_PATH', AXION_LIB_PATH . DS . 'kernel' ); //AXION框架内核代码目录
		
		/**
		 * 设置框架includePath
		 */
		self::addIncludePath(AXION_KERNEL_PATH);
		
		/**
		 * 加载AXION基础函数库
		 */
		require AXION_PATH . DS . 'common/functions.php';
		
		/**
		 * 设置默认时区(估计这框架暂时也出不了国)
		 */
		date_default_timezone_set ( 'Asia/Shanghai' );
		
		/**
		 * 记录框架初始化完成时间 
		 */
		self::$AXION_LOADED_FILE_TIME = microtime ( true );
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
	 * @param string $className 类名
	 */
	public static function autoloadClass($className) {
		echo $className;
	}
	
	public function Run() {
		self::initialize ();
	}
}

/**
 * 框架启动函数
 *
 */
	
function run() {
	/**
	 * 注册框架自动加载函数
	 */
	spl_autoload_register ( array('Axion', 'autoloadClass') );

	$axion = new Axion ( );
	$axion->Run ();
}
?>