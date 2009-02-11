<?php
/**
 * Axion框架初始化文件
 * @author kellenqian
 * @copyright techua.com
 */

/**
 * 记录程序开始执行的时间点
 */
$_ENV ['AXION_START_TIME'] = microtime ( true );

/**
 * 定义自适应系统的目录分隔符
 * linux:'/' windows:'\'
 */
define ( 'DS', DIRECTORY_SEPARATOR );

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

//统一命名常量
define ( 'COMMON', 'common' ); //统一通用目录命名
define ( 'LIB', 'lib' ); //统一库目录命名
define ( 'CONTROLLER', 'controller' ); //统一控制器目录命名
define ( 'MODEL', 'model' ); //统一数据模型目录命名
define ( 'VIEW', 'view' ); //统一视图目录命名
define ( 'DISPATCHER', 'dispatcher' ); //统一调度器目录命名


//框架目录常量
define ( 'AXION_COMMON_PATH', AXION_PATH . DS . COMMON ); //AXION配置文件、基础函数库目录
define ( 'AXION_LIB_PATH', AXION_PATH . DS . LIB ); //AXION框架代码库目录
define ( 'AXION_APPLICATION_PATH', AXION_LIB_PATH . DS . 'application' ); //AXION应用程序初始化器目录
define ( 'AXION_CONTROLLER_PATH', AXION_LIB_PATH . DS . CONTROLLER ); //AXION应用程序控制器目录
define ( 'AXION_MODEL_PATH', AXION_LIB_PATH . DS . MODEL ); //AXION应用程序数据模型目录
define ( 'AXION_VIEW_PATH', AXION_LIB_PATH . DS . VIEW ); //AXION应用程序视图目录
define ( 'AXION_DISPATCHER_PATH', AXION_LIB_PATH . DS . DISPATCHER ); //AXION应用程序调度器目录
define ( 'AXION_DATABASE_PATH', AXION_LIB_PATH . DS . 'database' ); //AXION应用程序数据库驱动目录


//项目目录常量
define ( 'APP_COMMON_PATH', APPLICATION_PATH . DS . COMMON ); //APPLICATION配置文件、基础函数库目录
define ( 'APP_LIB_PATH', APPLICATION_PATH . DS . LIB ); //APPLICATION框架代码库目录
define ( 'APP_CONTROLLER_PATH', APP_LIB_PATH . DS . CONTROLLER ); //APPLICATION应用程序控制器目录
define ( 'APP_MODEL_PATH', APP_LIB_PATH . DS . MODEL ); //APPLICATION应用程序数据模型目录
define ( 'APP_VIEW_PATH', APP_LIB_PATH . DS . VIEW ); //APPLICATION应用程序视图目录
define ( 'APP_DISPATCHER_PATH', APP_LIB_PATH . DS . DISPATCHER ); //APPLICATION应用程序调度器目录
/* @todo 暂时只定义了必须的目录*/

/**
 * 加载AXION基础函数库
 */
require AXION_COMMON_PATH . DS . 'functions.php';

/**
 * 加载AXION默认配置文件
 */
$_config = require AXION_COMMON_PATH . DS . 'default.php';
$_ENV = array_merge ( $_ENV, array_change_key_case ( $_config, CASE_UPPER ) );

/**
 * 载入框架启动必要的组件
 */
require AXION_APPLICATION_PATH . DS . 'base.class.php';
require AXION_APPLICATION_PATH . DS . 'application.class.php';

/**
 * 记录框架初始化完成时间 
 */
$_ENV ['AXION_LOADED_FILE_TIME'] = microtime ( true );
?>