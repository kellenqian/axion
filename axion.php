<?php
/**
 * Axion框架初始化文件
 * @author kellenqian
 * @copyright techua.com
 */

/**
 * 记录程序开始执行的时间点
 */
$_ENV['AXION_START_TIME'] = microtime(true);

/**
 * 定义自适应系统的目录分隔符
 * linux:'/' windows:'\'
 */
define('DS',DIRECTORY_SEPARATOR);


/**
 * 定义当前AXION所在路径
 */
if(!defined('AXION_PATH')){
	define('AXION_PATH' , dirname(__FILE__));
}

/**
 * 检测应用程序是否定义了合法的路径
 */
if(!defined('APPLICATION_PATH')){
	exit('Please DEFINE "APPLICATION_PATH"');
}elseif(strstr(APPLICATION_PATH,'.')){
	exit('"APPLICATION_PATH" is Illegal');
}

/**
 * 定义AXION必要的常量
 */
define('AXION_COMMON_PATH',AXION_PATH.DS.'common');//AXION配置文件、基础函数库目录
define('AXION_LIB_PATH',AXION_PATH.DS.'lib');//AXION框架代码库目录
define('AXION_APPLICATION_PATH',AXION_LIB_PATH.DS.'application');//AXION应用程序初始化器目录
define('AXION_CONTROLLER_PATH',AXION_LIB_PATH.DS.'controller');//AXION应用程序控制器目录
define('AXION_MODEL_PATH',AXION_LIB_PATH.DS.'model');//AXION应用程序数据模型目录
define('AXION_VIEW_PATH',AXION_LIB_PATH.DS.'view');//AXION应用程序视图目录
define('AXION_DISPATCHER_PATH',AXION_LIB_PATH.DS.'dispatcher');//AXION应用程序调度器目录
define('AXION_DATABASE_PATH',AXION_LIB_PATH.DS.'database');//AXION应用程序数据库驱动目录
/* @todo 暂时只定义了必须的目录*/

/**
 * 加载AXION基础函数库
 */
require AXION_COMMON_PATH.DS.'functions.php';

/**
 * 加载AXION默认配置文件
 */
loadConfig('axion');



/**
 * 注册自动加载类函数
 *
 * @param string $obj_className
 * @return null
 * @author kellenqian
 * @copyright techua.com
 */
function __autoload($str_className){
	
}
?>