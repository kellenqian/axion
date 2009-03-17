<?php
	/**
	 * 从数据库中获取数据并使用获取到的数据生成对应的数据表结构映射类{DataMap}实例
	 * @desc 该类从原有的TableSeed类中拆分后形成，将有关数据校验相关的操作部分拆分出来形成该类
	 * @author [Alone] alonedistian@gmail.com〗
	 * @version 0.1
	 * @package PHPDoc
	 */
	
	/**
	 * 数据查询接口
	 * -----------------------------------------------------------------------------------------------------------------------------------------------------------
	 * class user extends Axion_orm_DataMap 
	 *	{
	 *		function initSelf()
	 *		{
	 *			$this->str_primayKey = 'uchk_ID';
	 *			$this->arr_bill = array(
	 *											'ID' => array(  'action' => 'n',  'defValue' => '',  'isExpression' => true,  'isNull' => true,  'maxLength' => NULL,  'minLength' => NULL,  'preg' => '',  'type' => 'int',  'unique' => false,  'unsigned' => true,  'vType' => 'text',  'value' => NULL,),
	 *											'Name' => array(  'action' => 'a',  'defValue' => '',  'isExpression' => false,  'isNull' => true,  'maxLength' => 64,  'minLength' => NULL,  'preg' => '',  'type' => 'str',  'unique' => true,  'unsigned' => false,  'vType' => 'text',  'value' => NULL,),
	 *											'PS' => array(  'action' => 'a',  'defValue' => '',  'isExpression' => false,  'isNull' => true,  'maxLength' => 64,  'minLength' => 64,  'preg' => '',  'type' => 'str',  'unique' => false,  'unsigned' => false,  'vType' => 'text',  'value' => NULL,),
	 *											);
	 *			parent::initSelf();
	 *		}//end function initSelf
	 *	}//class t_uchk
	 * 
	 * $obj = new user();
	 * -----------------------------------------------------------------------------------------------------------------------------------------------------------
	 * 
	 * -----------------------------------------------------------------------------------------------------------------------------------------------------------
	 * $obj->get[param0]( param1, param2, param3 );
	 * 	param0 : 取值
	 * 	param1 : 校验列名称		default:自动索引主键
	 * 	param2 : 数据条目		default:1
	 * 	param3 : 扩展查询语句	default:空
	 * 
	 * Example:
	 * $obj->get1;																												获取自动索引主键值为‘1’的数据
	 * $obj->get1();																											获取自动索引主键值为‘1’的数据
	 * $obj->getuser_Name( 'Alone' );																					获取'user_Name'数据列值为 'Alone'的数据
	 * $obj->getuser_Name( 'Alone', 10 );																				获取'user_Name'数据列值为 'Alone'的数据
	 * $obj->getuser_Name( 'Alone', 10, " AND `user_Disabled` != '1' ORDER BY `user_regTime` " );	获取'user_Name'数据列值为 'Alone' 且符合条件" AND `user_Disabled` != '1' ORDER BY `user_regTime` " 的前10条数据 
	 * -----------------------------------------------------------------------------------------------------------------------------------------------------------
	 * 
	 * -----------------------------------------------------------------------------------------------------------------------------------------------------------
	 * $obj->top[param0]( param1 );
	 * 	param0 : 获取条目数		default:1
	 * 	param1 : 排序列名称		default:自动索引主键
	 * 
	 * Example:
	 * $obj->top;																												获取按照自动索引主键正序排序的第一条数据
	 * $obj->top();																											获取按照自动索引主键正序排序的第一条数据
	 * $obj->top3();																											获取按照自动索引主键正序排序的前3条数据
	 * $obj->top3( 'user_regTime' );																						获取按照'user_retTime'正序排序的前3条数据
	 * -----------------------------------------------------------------------------------------------------------------------------------------------------------
	 * 
	 * -----------------------------------------------------------------------------------------------------------------------------------------------------------
	 * $obj->end[param0]( param1 );
	 * 	param0 : 获取条目数		default:1
	 * 	param1 : 排序列名称		default:自动索引主键
	 * 
	 * Example:
	 * $obj->end;																												获取按照自动索引主键倒序排序的第一条数据
	 * $obj->end();																											获取按照自动索引主键倒序排序的第一条数据
	 * $obj->end3();																											获取按照自动索引主键倒序排序的前3条数据
	 * $obj->end3( 'user_regTime' );																						获取按照'user_retTime'倒序排序的前3条数据
	 */
	
	class Axion_orm_DataSearch
	{
		/**
		 * 数据表名称
		 * @var string
		 */
		protected $str_tableName;
		
		/**
		 * 用于存储结果及映射的数据对象
		 *
		 * @var Axion_orm_DataMap
		 */
		protected $obj_dataMap;
		
		/**
		 * 初始化，但真正的初始化是由initSelf方法完成的。
		 *
		 */
		protected $obj_MySQL;
		
		function __construct()
		{
			$str_tableName				= 't_' . get_class( $this );
			$this->str_tableName		= $str_tableName;
			$this->obj_MySQL			= Axion_db_MySQL::_init();
			$this->obj_Axion_log		= Axion_log::_init();
			
			if( !class_exists( $this->str_tableName ) )
			{
				if( !file_exists( APP_ORM_MAP_PATH . DS ."{$this->str_tableName}.class.php" ) )
				{
					$_obj_initDataMap = new Axion_orm_InitDataMap( $this->str_tableName );
					$_obj_initDataMap->createFile( APP_ORM_MAP_PATH . DS );
				}
				require_once( APP_ORM_MAP_PATH . DS ."{$this->str_tableName}.class.php");
			}
			
			$this->obj_dataMap = new $this->str_tableName();
		} //function __construct
		
	
		/**
		 * 创建数据获取的快捷方式
		 *
		 * @param void $void_name
		 * @return void
		 */
		public function __call( $str_paraName, $arr_paras )
		{
			$_str_option = substr( strtolower( $str_paraName ), 0, 3 );
			$_void_param = substr( $str_paraName, 3 );
			switch( $_str_option )
			{
				//获取指定编号信息
				case 'get' :
					//使用数字参数则获取默认索引列为该值的1条数据
					if( checkInt( $_void_param ) )
						$_void_result = $this->getDataByID( $_void_param );
					else {
						if( empty( $_void_param ) )
						{
							$this->obj_Axion_log->newMessage( '缺少必要的参数', Axion_log::WARNING );
							return false;
						} //if
						array_unshift( $arr_paras, $_void_param );
						$_void_result = call_user_func_array( array( &$this, 'getListByKey' ), $arr_paras );
					}
					return $_void_result;
				
				//获取符合条件的前N条数据
				case 'top' :
					//如果不提供获取数据的条目数默认获取1条数据
					if( !$_void_param || !checkInt( $_void_param ) )
						$_void_param = 1;
					array_unshift( $arr_paras, $_void_param );
					$_void_result = call_user_func_array( array( &$this, 'getTopList' ), $arr_paras );
					return $_void_result;
				
				//获取符合条件的最后N条数据
				case 'end' :
					//如果不提供获取数据的条目数默认获取1条数据
					if( !$_void_param || ! checkInt( $_void_param ) )
						$_void_param = 1;
					array_unshift( $arr_paras, $_void_param * - 1 );
					$_void_result = call_user_func_array( array( &$this, 'getTopList' ), $arr_paras );
					return $_void_result;
				
				default :
					$this->obj_Axion_log->newMessage( '参数解析错误', Axion_log::WARNING );
					return false;
			} //switch
		} //end function __call
		
	
		/**
		 * 创建数据获取的快捷方式
		 *
		 * @param void $void_name
		 * @return void
		 */
		public function __get( $void_name )
		{
			$_str_option = substr( strtolower( $void_name ), 0, 3 );
			$_void_param = substr( $void_name, 3 );
			switch( $_str_option )
			{
				//获取指定编号信息
				case 'get' :
					if( AXION_UTIL_VALIDATE::checkInt( $_void_param ) )
						$_void_result = $this->getDataByID( $_void_param );
					else
					{
						$this->obj_Axion_log->newMessage( '参数格式错误', Axion_log::WARNING );
						return false;
					}
					return $_void_result;
				
				//获取前N条数据
				case 'top' :
					if( ! $_void_param || !AXION_UTIL_VALIDATE::checkInt( $_void_param ) )
						$_void_param = 1;
					$_void_result = $this->getTopList( $_void_param );
					return $_void_result;
				
				case 'end' :
					if( !$_void_param || !AXION_UTIL_VALIDATE::checkInt( $_void_param ) )
						$_void_param = 1;
					$_void_result = $this->getTopList( $_void_param * - 1 );
					return $_void_result;
				
				default :
					$this->obj_Axion_log->newMessage( '参数解析错误', Axion_log::WARNING );
					return false;
			} //switch
		} //end function __get
		
	
		/**
		 * 创建用于插入数据库操作的Axion_orm_DataMap实例
		 *
		 * @param array $arr_paras				映射数据
		 * @return Axion_orm_DataMap
		 */
		public function create( $arr_paras = null )
		{
			if( !empty( $arr_paras ) )
				$this->obj_dataMap->input( $arr_paras );
			
			return $this->obj_dataMap;
		} //end function create
		
	
		/**
		 * 根据扩展查询语句从当前操作的数据表中获取对应的结果集
		 *
		 * @param string $str_extSQLQuery
		 * @return array 如果成功获取数据则返回值为DataMap对象实例数组 否则返回FALSE
		 */
		public function getData( $str_extSQLQuery )
		{
			if( !$this->obj_MySQL )
				$this->obj_MySQL = Axion_db_MySQL::_init();
				
			//生成SQL查询语句
			$_str_SQLQuery = "SELECT * FROM `{$this->obj_dataMap->str_viewName}`";
			$_str_SQLQuery .= $str_extSQLQuery;
			
			//获取缓存数据
			$_void_result = $this->getCache( $_str_SQLQuery );
			if( $_void_result )
				return $_void_result;
				
			//获取SQL查询结果
			$_void_result = $this->obj_MySQL->querySQL( $_str_SQLQuery );
			
			//SQL语句错误，该错误信息已经通过MySQL类自动记录
			if( $_void_result === false )
				return false;
				
			//未获得查询结果
			if( empty( $_void_result ) )
			{
				$this->obj_Axion_log->newMessage( "未获得符合条件的数据，By SQL query >>>[{$_str_SQLQuery}]。", Axion_log::NOTICE );
				return false;
			} //if
			
	
			//生成查询结果集
			return $this->createResult( $_void_result );
		} //end function getData
		
	
		/**
		 * 根据主键编号获取一条或N条数据，取得数据的表是由str_viewName属性指定的
		 *
		 * @param int $int_id
		 * @return Axion_orm_DataMap			数据映射对象
		 */
		public function getDataByID( $int_id )
		{
			//生成查询语句
			$_str_sql = " WHERE `{$this->obj_dataMap->str_primayKey}` = '{$int_id}' LIMIT 1";
			
			//获取查询结果
			$_void_result = $this->getData( $_str_sql );
			if( !$_void_result )
				return false;
			else
				return $_void_result[0];
		} //function getDataByID
		
	
		/**
		 * 根据{$str_tableKey}_Name字段的值返回一条记录
		 *
		 * @param string 		$str_name
		 * @param string 		$str_tagName
		 * @return Axion_orm_DataMap	数据映射对象
		 */
		public function getDataByName( $str_name )
		{
			//生成查询语句
			$_str_sql = " WHERE `{$this->obj_dataMap->str_tableKey}_Name` = '{$str_name}' LIMIT 1";
			
			//获取查询结果
			$_void_result = $this->getData( $_str_sql );
			if( !$_void_result )
				return false;
			else
				return $_void_result[0];
		} //function getDataByName
		
	
		/**
		 * 根据指定关键字生成扩展查询语句，并通过getData()函数生成结果集
		 *
		 * @param string $str_key					关键字	
		 * @param string $str_value				取值
		 * @param integer $int_limit				获取条目
		 * @param string $str_extSQLQuery		其他SQL条件
		 * @return array
		 */
		public function getListByKey( $str_key, $str_value, $int_limit = null, $str_extSQLQuery = null )
		{
			//生成查询语句
			$_str_SQLQuery = " WHERE `{$str_key}` = '{$str_value}' ";
			if( $str_extSQLQuery )
				$_str_SQLQuery .= " {$str_extSQLQuery} ";
			if( $int_limit )
				$_str_SQLQuery .= " LIMIT {$int_limit} ";
			
			return $this->getData( $_str_SQLQuery );
		} //end function getListByKey
		
	
		/**
		 * 按排序顺序获取查询语句，并通过getData()函数生成结果集
		 *
		 * @param integer $int_limit				获取条目，如果条目数为负值则表示逆序排序
		 * @param string $str_key					排序关键字
		 * @return array
		 */
		public function getTopList( $int_limit, $str_key = null )
		{
			if( !$str_key  )
				$str_key = $this->obj_dataMap->str_primayKey;
			
			$_str_order = $int_limit < 0 ? 'DESC' : '';
			
			$int_limit = abs( $int_limit );
			$_str_SQLQuery = " ORDER BY `{$str_key}` {$_str_order} LIMIT {$int_limit}";
			return $this->getData( $_str_SQLQuery );
		} //end function getTopList
		
	
		protected function getCache( $str_SQLQuery )
		{
			return false;
		} //end function getCache
		
	
		/**
		 * 根据查询结果生成DataMap对象实例
		 *
		 * @param array $arr_paras			数据库查询结果
		 * @return array  Axion_orm_DataMap对象实例数组
		 */
		protected function createResult( $arr_paras )
		{
			//生成查询结果集
			$_arr_result = array();
			foreach( $arr_paras as $_arr_values )
			{
				$_obj_tempDataMap	= clone $this->obj_dataMap;
				$_obj_tempDataMap->input( $_arr_values );
				$_arr_result[]		= $_obj_tempDataMap;
			} //end foreach
			
	
			return $_arr_result;
		} //end function createResult
	} //class Axion_orm_DataSearch
	
	
	/**
	 * Finish
	 * o._.o
	 */
?>