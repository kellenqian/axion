<?php
	/**
	 * 数据表结构校验
	 * @desc 该类从原有的TableSeed类中拆分后形成，将有关数据校验相关的操作部分拆分出来形成该类
	 * @author [Alone] alonedistian@gmail.com〗
	 * @version 0.1
	 * @package PHPDoc
	 */
	
	/**
	 * 数据查询接口
	 * -----------------------------------------------------------------------------------------------------------------------------------------------------------
	 * $obj = new dataschema( 'user' );
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
		
	class DataSchema
	{	
		protected $str_tableName;
		
		protected $obj_processStatus;
		
		/**
		 * 初始化，但真正的初始化是由initSelf方法完成的。
		 *
		 */
		protected $obj_MySQL;
		function __construct( $str_tableName )
		{
			$this->str_tableName = $str_tableName;
			$this->obj_MySQL = MySQL::_init();
			$this->obj_processStatus = ProcessStatus::_init();
		}//function __construct
		
		public function __call( $str_paraName, $arr_paras )
		{
			$_str_option = substr( strtolower( $str_paraName ), 0, 3 );
			$_void_param = substr( $str_paraName, 3 );
			switch( $_str_option )
			{
				//获取指定编号信息
				case 'get' :
					if( checkInt( $_void_param ) )
						$_void_result = $this->getDataByID( $_void_param );
					else 
					{
						if( empty( $_void_param ) )
						{
							$this->obj_processStatus->newMessage( ProcessStatus::$INT_ERR_WARNING, '缺少必要的参数' );
							return false;
						}//if
						array_unshift( $arr_paras, $_void_param );
						$_void_result = call_user_func_array( array( &$this, 'getListByKey' ), $arr_paras );
					}
					return $_void_result;
					
				//获取符合条件的前N条数据
				case 'top' :
					if( !$_void_param || !checkInt( $_void_param ) )
						$_void_param = 1;						
					array_unshift( $arr_paras, $_void_param );
					$_void_result = call_user_func_array( array( &$this, 'getTopList' ), $arr_paras );
					return $_void_result;
					
				//获取符合条件的最后N条数据
				case 'end' :
					if( !$_void_param || !checkInt( $_void_param ) )
						$_void_param = 1;
					array_unshift( $arr_paras, $_void_param * -1 );
					$_void_result = call_user_func_array( array( &$this, 'getTopList' ), $arr_paras );
					return $_void_result;
					
				default :
					$this->obj_processStatus->newMessage( ProcessStatus::$INT_ERR_WARNING, '参数解析错误' );
					return false;
			}//switch
		}//end function __call
		
		public function __get( $void_name )
		{
			$_str_option = substr( strtolower( $void_name ), 0, 3 );
			$_void_param = substr( $void_name, 3 );
			switch( $_str_option )
			{
				//获取指定编号信息
				case 'get' :
					if( checkInt( $_void_param ) )
						$_void_result = $this->getDataByID( $_void_param );
					else 
					{
						$this->obj_processStatus->newMessage( ProcessStatus::$INT_ERR_WARNING, '参数格式错误' );
						return false;
					}
					return $_void_result;
					
				//获取前N条数据
				case 'top' :
					if( !$_void_param || !checkInt( $_void_param ) )
						$_void_param = 1;					
					$_void_result = $this->getTopList( $_void_param );
					return $_void_result;
					
				case 'end' :
					if( !$_void_param || !checkInt( $_void_param ) )
						$_void_param = 1;						
					$_void_result = $this->getTopList( $_void_param * -1 );
					return $_void_result;
					
				default :
					$this->obj_processStatus->newMessage( ProcessStatus::$INT_ERR_WARNING, '参数解析错误' );
					return false;
			}//switch
		}//end function __get
		
		public function getData( $str_extSQLQuery )
		{
			if( !$this->obj_MySQL )
				$this->obj_MySQL = MySQL::_init();
			
			//创建一个用于存储结果及映射的数据对象
			$_obj_dataMap	= new $this->str_tableName();		
			
			//生成SQL查询语句
			$_str_SQLQuery				= "SELECT * FROM `{$_obj_dataMap->str_viewName}`";
			$_str_SQLQuery				.= $str_extSQLQuery;
			
			//获取SQL查询结果
			$_void_result				= $this->obj_MySQL->querySQL( $_str_SQLQuery );
			
			//SQL语句错误，该错误信息已经通过MySQL类自动记录
			if( $_void_result === false )
				return false;
				
			//未获得查询结果
			if( empty( $_void_result ) )
			{
				$this->obj_processStatus->newMessage( ProcessStatus::$INT_ERR_NOTICE , "未获得符合条件的数据，By SQL query >>> [{$_str_SQLQuery}] 。" );
				return false;
			}//if
			
			//生成查询结果集
			$_arr_result = array();
			foreach ( $_void_result as $_arr_values ) 
			{
				$_obj_tempDataMap = clone $_obj_dataMap;
				$_obj_tempDataMap->input( $_arr_values );
				$_arr_result[] = $_obj_tempDataMap;
			}//end foreach
			
			return $_arr_result;
		}//end function getData
		
		/**
		 * 根据主键编号获取一条或N条数据，取得数据的表是由str_viewName属性指定的
		 *
		 * @param int $int_id
		 * @return DataMap			数据映射对象
		 */
		public function getDataByID( $int_id )
		{
			//创建一个用于存储结果及映射的数据对象
			$_obj_dataMap	= new $this->str_tableName();
			
			//生成查询语句
			$_str_sql		= " WHERE `{$_obj_dataMap->str_primayKey}` = '{$int_id}' LIMIT 1";
			
			//获取查询结果
			$_void_result = $this->getData( $_str_sql );
			if( !$_void_result )
				return false;
			else 
				return $_void_result[0];
		}//function getDataByID
		
		/**
		 * 根据{$str_tableKey}_Name字段的值返回一条记录
		 *
		 * @param string 		$str_name
		 * @param string 		$str_tagName
		 * @return DataMap	数据映射对象
		 */
		public function getDataByName( $str_name )
		{	
			//创建一个用于存储结果及映射的数据对象
			$_obj_dataMap	= new $this->str_tableName();
			
			//生成查询语句
			$_str_sql = " WHERE `{$_obj_dataMap->str_tableKey}_Name` = '{$str_name}' LIMIT 1";
				
			//获取查询结果
			$_void_result = $this->getData( $_str_sql );
			if( !$_void_result )
				return false;
			else 
				return $_void_result[0];
		}//function getDataByName
		
		
		public function getListByKey( $str_key, $str_value, $int_limit = null, $str_extSQLQuery = null )
		{
			//生成查询语句
			$_str_SQLQuery			= " WHERE `{$str_key}` = '{$str_value}' ";
			if( $str_extSQLQuery )
				$_str_SQLQuery		.= " {$str_extSQLQuery} ";
			if( $int_limit )
				$_str_SQLQuery		.= " LIMIT {$int_limit} ";
			
			return $this->getData( $_str_SQLQuery );
		}//end function getListByKey
		
		
		public function getTopList( $int_limit, $str_key = null )
		{
			if( !$str_key )
			{
				//创建一个用于存储结果及映射的数据对象
				$_obj_dataMap	= new $this->str_tableName();
				$str_key = $_obj_dataMap->str_primayKey;
			}
			if( $int_limit < 0 )
				$_str_order = 'DESC';
			$int_limit = abs( $int_limit );
			$_str_SQLQuery = " ORDER BY `{$str_key}` {$_str_order} LIMIT {$int_limit}";
			return $this->getData( $_str_SQLQuery );
		}//end function getTopList
	}//class DataSchema
	
	/**
	 * Finish
	 * o._.o
	 */
?>