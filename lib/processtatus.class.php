<?php
	/**
	 * 进程状态池，用于保存代码执行期间产生的消息。
	 * @version v1.20
	 * @author [Alone] & [Nick Wang]〖alonedistian@gmail.com〗
	 * Date 2008-03-11
	 */
	/*********************************************
	☆		  				更新说明							☆
	**********************************************
	☆	v1.10:												☆
	☆		在类中提供新标识 int_extLv，					☆
	☆		用于允许在错误产生时由ProcessStatus类		☆
	☆		终止程序运行										☆
	☆	v1.11													☆
	☆		更新函数getAllMessage()						☆
	☆		加入参数$int_errorLevel，如果提供了该参数	☆
	☆		则仅输出不低于该错误等级的相关操作信息。		☆
	☆															☆
	☆	v1.12：												☆
	☆		去除标识 int_extLv	及相关的操作				☆
	☆															☆
	☆	v1.2;													☆
	☆		重写程序使之支持新框架AXION						☆
	☆															☆
	***********************************************/ 	
	class ProcessStatus 
	{
		protected static $obj_this;
		
		/**
		 * 错误中断标识
		 * @var integer
		 * @desc 当该标识不为0时， 如果产生错误的错误类型值大于或等于该值则允许在本类中终止程序执行
		 */
		protected $int_extLv = null;
				
		/**
		 * 消息数组array=(int_lv,str_lv,str_result,str_msg)
		 * @var array
		 */
		protected $arr_dataPool;
		
		protected $h_maxError;
		
		/**
		 * 异常信息队列
		 * @var array
		 */
		protected $ARR_ERR_LV = array( 1 => '消息' , 
												E_USER_NOTICE => '注意' , 
												E_USER_WARNING => '错误' , 
												E_USER_ERROR => '异常' );
		
		/**
		 * 错误处理标识字符串
		 * @var string
		 */
		public static $STR_PROCESS_TAG_NAME = 'SystemResponse';
		
		/**
		 * 错误等级：错误
		 * @var int
		 */
		public static $INT_ERR_ERROR = E_USER_ERROR;
		
		/**
		 * 错误等级：异常
		 * @var int
		 */
		public static $INT_ERR_WARNING = E_USER_WARNING;
		
		/**
		 * 错误等级：注意
		 * @var int
		 */
		public static $INT_ERR_NOTICE = E_USER_NOTICE;
		
		/**
		 * 错误等级：消息
		 * @var int
		 */
		public static $INT_ERR_WELL = 1;
		
		/**
		 * 整体状态$arr_dataPool中是否包含异常或错误等级的提示
		 * @var boolean
		 */
		protected $bool_isNice = true;
		
		/**
		 * 整体状态$arr_dataPool中最高的异常等级
		 * @var int
		 */
		protected $int_maxErrLv = 1;
		
		function __construct()
		{
		}//end function __construct
		
		/**
		 * 单件模式创建数据库对象
		 *
		 * @return unknown
		 */
		static function _init()
		{
			 if( ProcessStatus::$obj_this )
			 	return ProcessStatus::$obj_this;
			 else 
			 	ProcessStatus::$obj_this = new ProcessStatus();
			 	
			 return ProcessStatus::$obj_this;
		}//end function _init
		
		/**
		 * 创建新的状态信息记录到$arr_dataPool中
		 *
		 * @param int $int_lv				错误等级
		 * @param string $str_result		错误提示信息
		 * @return boolean
		 */
		public function newMessage( $int_lv , $str_result = null )
		{
			if( empty( $this->ARR_ERR_LV[ $int_lv ] ) )
				trigger_error( "无效的异常等级编号。" , ProcessStatus::$INT_ERR_ERROR );
				
			if( is_null( $str_result ) )
				$str_result = '';
			
			$this->arr_dataPool[] = array( 'int_lv' => $int_lv,
													 'str_lv' => $this->ARR_ERR_LV[$int_lv],
													 'str_result' => "{$this->ARR_ERR_LV[ $int_lv ]}:{$str_result}",
													 'str_msg' => $str_result );
													 
			if( $int_lv > ProcessStatus::$INT_ERR_NOTICE )
				$this->bool_isNice = false;
				
			if( $int_lv > $this->int_maxErrLv )
				$this->int_maxErrLv = $int_lv;
			
			if( !is_null( $this->int_extLv ) && $int_lv >= $this->int_extLv )
			{
				$this->output();
			}
				
			return true;
		}//function setMessage
		
		/**
		 * 获取当前整体状态
		 *
		 * @return boolean
		 */
		public function getState()
		{
			return $this->bool_isNice;
		}//function getState
		
		/**
		 * 获取当前整体中的最高错误等级
		 *
		 * @return int
		 */
		public function getMaxErrLv()
		{
			return $this->int_maxErrLv;
		}//function getMaxErrLv
		
		/**
		 * 获取当前整体中产生的最高错误类型名称
		 *
		 * @return string
		 */
		public function getMaxErrString()
		{
			return $this->ARR_ERR_LV[ $this->getMaxErrLv() ];
		}//function getMaxErrString
		
		/**
		 * 获取当前整体中产生的最高错误信息的完整信息数组
		 *
		 * @return array
		 */
		public function getMaxErr( )
		{
			return $this->h_maxError;
		}//end function getMaxErr
		
		/**
		 * 获取当前消息池的数据
		 *
		 * @return array
		 */
		public function getAllData()
		{
			return $this->arr_dataPool;
		}//function getAllData
	
		/**
		 * 获取当前消息池的完整数据，该数据包含了所有的信息。
		 *
		 * @return array
		 */
		public function getFullData()
		{
			$arr_result = array();
			$arr_result['sysOperateState'] = ( $this->getState() ? 'True' : 'False' );				//处理状态
			$arr_result['sysAlertLv'] = $this->getMaxErrLv();												//最高错误等级
			$arr_result['sysMsgString'] = $this->getAllMessage();											//完整信息提示
			$arr_result['sysMsgArray'] = $this->arr_dataPool;												//错误消息完整内容
			return $arr_result;
		}//function getFullData
		
		/**
		 * 获取当前消息池全部提示信息并将这些信息使用';'分割
		 *
		 * @param integer $int_errorLevel  需要获取的最低错误等级
		 * @return string
		 */
		public function getAllMessage( $int_errorLevel = null )
		{
			if( empty( $this->arr_dataPool ) )
				return STR_TAG_EMPTY;
				
			$_str_result = '';
			foreach (  $this->arr_dataPool as $arr_dataPoll ) 
			{
				if( is_null( $int_errorLevel ) || $int_errorLevel <= $arr_dataPoll['int_lv'] )
					$_str_result .= "{$arr_dataPoll['str_msg']};";
			}//foreach
			return $_str_result;
		}//end function getAllMessage
		
		/**
		 * 获取最后一次插入的消息数据
		 *
		 * @return array
		 */
		public function getLastMessage()
		{
			return $this->arr_dataPool[ count( $this->arr_dataPool ) - 1 ];
		}//function getLastMessage
		
		/**
		 * 与另一个ProcessStatus对象进行消息池组合
		 *
		 * @param ProcessStatusObject $processStatus
		 * @return boolean
		 */
		public function uniteFriend( $processStatus )
		{
			if( !is_a( $processStatus , 'ProcessStatus' ) )
			{
				$this->newMessage( ProcessStatus::$INT_ERR_ERROR , '进行合并ProcessStatus对象操作时传入的参数类型不匹配。' );
				return false;
			}//if
			
			$_arr_data = $processStatus->getAllData();
			if( !empty( $_arr_data ) )
			{
				foreach ( $_arr_data as $_arr_value )
					$this->arr_dataPool[] = $_arr_value;
			}//if
			$this->bool_isNice = ( $this->bool_isNice && $processStatus->bool_isNice );
			
			if( $this->int_maxErrLv < $processStatus->int_maxErrLv )
				$this->int_maxErrLv = $processStatus->int_maxErrLv;
			return true;
		}//function uniteFriend
		
		/**
		 * 清空当前消息池
		 *
		 * @return boolean
		 */
		public function clearMsg()
		{
			$this->arr_dataPool = array();
			$this->bool_isNice = true;
			return true;
		}//function 
		
		/**
		 * 输出当前错误信息
		 *
		 */
		public function output()
		{
			P( $this->getAllData() );
			exit;
		}//end function output
	}

	/**
	 * Finish
	 * o._.o
	 */
?>