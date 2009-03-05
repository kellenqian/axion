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
	private $uniqueId;
	
	/**
	 * 构造函数
	 *
	 * 初始化应用程序
	 */
	public function __construct() {
		/**
		 * 计算应用程序的唯一ID
		 */
		$this->uniqueId = md5 ( APPLICATION_PATH );
		
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
		Axion::addIncludePath ( APPLICATION_PATH . DS . 'lib' . DS . 'controller' );//控制器目录
		Axion::addIncludePath ( APPLICATION_PATH . DS . 'lib' . DS . 'model' );//模型目录
		
		/**
		 * 注册默认错误处理函数
		 */
		$debugLevel = AXION_CONFIG::GET ( 'axion.debug.level' );
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
		 * 定义应用程序程序所需的临时文件目录常量
		 */
		define ( 'DATA_CACHE_PATH', TEMP_PATH . DS . 'axion_' . $this->uniqueId . DS . 'datacache' );
		define ( 'DB_CACHE_PATH', TEMP_PATH . DS . 'axion_' . $this->uniqueId . DS . 'dbcache' );
		define ( 'VIEW_CACHE_PATH', TEMP_PATH . DS . 'axion_' . $this->uniqueId . DS . 'viewcache' );
		define ( 'CODE_CACHE_PATH', TEMP_PATH . DS . 'axion_' . $this->uniqueId . DS . 'codecache' );
		
		/**
		 * 创建应用程序所需的临时文件目录
		 */
		$tmpDirs = array ('data_cache' => DATA_CACHE_PATH, 'db_cache' => DB_CACHE_PATH, 'view_cache' => VIEW_CACHE_PATH, 'code_cache' => CODE_CACHE_PATH );
		
		foreach ( $tmpDirs as $v ) {
			if (! is_dir ( $v )) {
				AXION_UTIL_FILE::mkdir ( $v, 0755 );
			}
		}
		
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
		p(IS_CLI);
		$appClass = $params ['controller'] . '_' . $params ['action'];
		
		if(class_exists($appClass)){
				
		}
		
		
	}
	
	/**
	 * 获取当前应用程序的唯一ID
	 *
	 * @return string
	 */
	public static function getUniqueId() {
		return $this->uniqueId;
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
		echo $errstr . "<br/>" . $errfile . "<br/>" . $errline . "<br/>";
	}
}
?>