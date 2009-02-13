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

//框架目录常量
define ( 'AXION_LIB_PATH', AXION_PATH . DS . 'lib' ); //AXION框架代码库目录


//项目目录常量
define ( 'APP_LIB_PATH', APPLICATION_PATH . DS . 'lib' ); //APPLICATION框架代码库目录
/* @todo 暂时只定义了必须的目录*/

/**
 * 设置自动加载目录
 */
$currentIncludePath = get_include_path ();
$axionIncludePath = AXION_LIB_PATH . DS . PATH_SEPARATOR;
$appIncludePath = APP_LIB_PATH . DS . PATH_SEPARATOR;

set_include_path ( $axionIncludePath . $appIncludePath . $currentIncludePath );

/**
 * 加载AXION基础函数库
 */
require AXION_PATH . DS .'common/functions.php';

/**
 * 载入框架启动必要的组件
 */
require 'application/base.class.php';
require 'interface/interface.class.php';
require 'application/application.class.php';
require 'config/config.class.php';

/**
 * 记录框架初始化完成时间 
 */
$_ENV ['AXION_LOADED_FILE_TIME'] = microtime ( true );
?>