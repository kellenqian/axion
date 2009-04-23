<?php
/**
 * 日志类，用于保存代码执行期间产生的消息。
 * @version v2.0
 * @author [Alone][alonedistian@gmail.com] & [Nick Wang] & [kellenqian] [kellen.qian@gmail.com]
 */
class Axion_log {
	/**
	 * 单件模式实例容器
	 *
	 * @var Axion_log
	 */
	private static $obj_this;
	
	/**
	 * 消息数组array=(int_lv,str_lv,str_result,str_msg)
	 * @var array
	 */
	private static $arr_dataPool;
	
	/**
	 * 整体状态$arr_dataPool中是否包含异常或错误等级的提示
	 * @var boolean
	 */
	private static $bool_isNice = true;
	
	/**
	 * 整体状态$arr_dataPool中最高的异常等级
	 * @var int
	 */
	private static $int_maxErrLv = self::INFO;
	
	/**
	 * 整体状态$arr_dataPool中最高的异常等级的日志
	 * @var int
	 */
	private static $arr_maxErrLog = array ();
	
	/**
	 * 异常信息队列
	 * @var array
	 */
	private static $arr_errLevelMap = array (self::INFO => '消息', self::NOTICE => '注意', self::WARNING => '警告', self::ERROR => '错误', self::PARSE => '编译', self::STRICT => '建议', self::UNDEFINED => '未定义' );
	
	/**
	 * 消息池最大条目数
	 * 默认 : 5000条
	 */
	private static $poolLimit = 5000;
	
	/**
	 * 当前消息池容量
	 *
	 * @var int
	 */
	private static $_poolSize = 0;
	
	/**
	 * 当前消息池是否已经超过容量标记
	 *
	 * @var bool
	 */
	private static $overLimit = false;
	
	/**
	 * 错误等级：错误
	 * @var int 1
	 */
	const ERROR = E_ERROR;
	
	/**
	 * 错误等级：警告
	 * @var int 2
	 */
	const WARNING = E_WARNING;
	
	/**
	 * 错误等级：编译
	 * @var int 4
	 */
	const PARSE = E_PARSE;
	
	/**
	 * 错误等级：注意
	 * @var int 8
	 */
	const NOTICE = E_NOTICE;
	
	/**
	 * 错误等级：建议
	 * @var int 2048
	 */
	const STRICT = E_STRICT;
	
	/**
	 * 错误等级：未定义的或用户自定义
	 * @var int 4096
	 */
	const UNDEFINED = 4096;
	
	/**
	 * 错误等级：消息
	 * @var int 9999
	 */
	const INFO = 9999;
	
	/**
	 * 单件模式创建数据库对象别名
	 * 
	 * 向下兼容过去代码
	 *
	 * @return Axion_log
	 */
	public static function getinstance() {
		if (self::$obj_this)
			return self::$obj_this;
		else
			self::$obj_this = new self ( );
		
		return self::$obj_this;
	} //end function _init
	

	/**
	 * 单件模式创建日志对象
	 *
	 * @return Axion_log
	 */
	public static function _init() {
		return self::getinstance ();
	} //end function getinstance
	

	/**
	 * 存储日志记录
	 *
	 * @param int $int_lv				错误等级
	 * @param string $str_result		错误提示信息
	 * @return boolean
	 */
	public static function log($str_result, $int_lv = self::INFO, $section = 'system') {
		if (empty ( $str_result )) {
			return false;
		}
		
		if (! isset ( self::$arr_errLevelMap [$int_lv] )) {
			$int_lv = self::UNDEFINED;
		}
		
		$dataMap = array ('int_lv' => $int_lv, 'str_msg' => $str_result );
		
		if (self::$_poolSize < self::$poolLimit) {
			self::$arr_dataPool [$section] [] = $dataMap;
			self::$_poolSize ++;
		} else {
			array_shift ( self::$arr_dataPool [$section] );
			array_push ( self::$arr_dataPool [$section], $dataMap );
			self::$overLimit = true;
		}
		
		if ($int_lv < self::NOTICE) {
			self::$bool_isNice = false;
		}
		
		if ($int_lv < self::$int_maxErrLv) {
			self::$int_maxErrLv = $int_lv;
			self::$arr_maxErrLog = $dataMap;
		}
		
		return true;
	} //function log
	

	/**
	 * 存储日志记录方法的别名
	 *
	 * 向下兼容过去代码
	 * 
	 * @param int $int_lv				错误等级
	 * @param string $str_result		错误提示信息
	 * @return boolean
	 */
	public static function newMessage($str_result = null, $int_lv = self::INFO) {
		return self::log ( $str_result, $int_lv );
	} //function newMessage
	

	/**
	 * 获取当前整体状态
	 *
	 * @return boolean
	 */
	public static function isNice() {
		return self::$bool_isNice;
	} //function getState
	

	/**
	 * 获取当前整体中的最高错误等级
	 *
	 * @return int
	 */
	public static function getMaxErrLv() {
		return self::$int_maxErrLv;
	} //function getMaxErrLv
	

	/**
	 * 获取当前整体中产生的最高错误信息列表
	 *
	 * @return string
	 */
	public static function getMaxErrLog() {
		return self::$arr_maxErrLog;
	} //function getMaxErrString
	

	/**
	 * 获取当前消息池的数据
	 *
	 * @return array
	 */
	public static function getLogPool() {
		return self::$arr_dataPool;
	} //function getAllData
	

	/**
	 * 获取当前消息池的完整数据，该数据包含了所有的信息。
	 *
	 * @return array
	 */
	public static function dump() {
		$arr_result = array ();
		$arr_result ['sysOperateState'] = (self::isNice () ? 'True' : 'False'); //处理状态
		$arr_result ['sysAlertLv'] = self::getMaxErrLv (); //最高错误等级
		$arr_result ['sysMsgArray'] = self::getLogPool (); //错误消息完整内容
		return $arr_result;
	} //function getFullData
	

	/**
	 * 获取最后一次插入的消息数据
	 *
	 * @return array
	 */
	public static function getLastMessage() {
		return self::$arr_dataPool [count ( self::$arr_dataPool ) - 1];
	} //function getLastMessage
	

	/**
	 * 清空当前消息池
	 *
	 * @return boolean
	 */
	public static function clearMsg() {
		self::$arr_dataPool = array ();
		self::$bool_isNice = true;
		return true;
	} //function 
	

	public static function changeMessagePollSize($num) {
		self::$poolLimit = $num;
	}
	
	public static function isOverLimit() {
		return self::$overLimit;
	}
	
	public static function getPoolLimit() {
		return self::$poolLimit;
	}
	
	public static function toApacheFormat() {
	
	}
}
?>