<?php
	/**
	 * 数据表结构映射类
	 * @desc 该类从原有的TableSeed类中拆分后形成，将有关数据格式相关的操作部分拆分出来形成该类
	 * @author [Alone] alonedistian@gmail.com〗
	 * @version 0.1
	 * @package PHPDoc
	 */
	class Axion_orm_DataMap
	{
		/**
		 * 该表中自有数据所使用的表名关键字
		 * @var string
		 */
		protected $str_tableKey;
		/**
		 * 该表的物理表名
		 * @var string
		 */
		protected $str_tableName;
		/**
		 * 该表的视图名，当getDataByID方法执行时，其数据是由该属性指向的位置获取的
		 * @var string
		 */
		protected $str_viewName;
		
		/**
		 * 该表实际使用的主键字段名称
		 * @var string
		 */
		protected $str_primayKey;
		
		/**
		 * 参数验证类型：数值型
		 * @var string
		 */
		const TYPE_BOOLEAN = 'bool';
		
		/**
		 * 参数验证类型：数值型
		 * @var string
		 */
		const TYPE_INT = 'int';
		
		/**
		 * 参数验证类型：浮点型
		 * @var string
		 */
		const TYPE_FLOAT = 'flt';
		
		/**
		 * 参数验证类型：字符串型
		 * @var string
		 */
		const TYPE_STRING = 'str';
		
		/**
		 * 参数验证类型：邮件地址型
		 * @var string
		 */
		const TYPE_MAIL = 'mail';
		
		/**
		 * 参数验证类型：日期型
		 * @var string
		 */
		const TYPE_DATE = 'date';
		
		/**
		 * 消息处理池
		 *
		 * @var Axion_Axion_log
		 */
		protected $obj_Axion_log;
		
		
		/**
		 * 字段描述数组，如$this->arr_bill['name'] = array('value'=>'','isExpression'=>false,'withContext'=>false)，
		 * 当isExpression为true时value将使用不带引号的方式插入SQL中。
		 * 当withContext为true时getBill方法可以自动根据外键取得原始数据簇。
		 * 注意：对于isExpression为true的字段应豁免数据检测。
		 * @var array
		 */
		protected $arr_bill;
		
		/**
		 * 数据库连接句柄
		 *
		 * @var MySQL
		 */
		protected $obj_MySQL;
		
		/**
		 * 初始化，但真正的初始化是由initSelf方法完成的。
		 *
		 */
		public function __construct( )
		{
			$this->str_tableKey = substr( get_class( $this ), 2 );
			
			$this->obj_Axion_log = Axion_log::_init();
			
			$this->arr_bill = array();
		
			//初始化数据结构
			$this->initSelf();
			if( empty( $this->str_tableKey ) )
			{
				$this->obj_Axion_log->newMessage( Axion_log::$INT_ERR_ERROR, '请指定数据表关键字' );
				return false;
			}
			
			/**
			 * 验证必要的操作参数
			 * 默认情况下 
			 * 	数据表主表名称	= t_ + 数据表关键字; 
			 * 	数据搜索视图名称	= v_ + 数据表关键字;
			 * 	搜索索引键名称	= 数据表关键字 + _ID;
			 */
			if( empty( $this->str_tableName ) )
				$this->str_tableName = "t_{$this->str_tableKey}";
			if( empty( $this->str_viewName ) )
				$this->str_viewName = "t_{$this->str_tableKey}";
			if( empty( $this->str_primayKey ) )
				$this->str_primayKey = "{$this->str_tableKey}_ID";
				
			$this->obj_MySQL = null;
			
			return true;
		}//function __construct
				
		public function __set( $str_name, $void_value )
		{
			if( isset( $this->arr_bill[$str_name] ) )
			{
				if( is_array( $void_value ) )
					$this->arr_bill[$str_name] = $void_value;
				else
					$this->arr_bill[$str_name]['value'] = $void_value;
			}
			else 
				$this->obj_Axion_log->newMessage( Axion_log::$INT_ERR_NOTICE, '未获得指定的数据结构' );
				
			return true;
		}//end function __set
		
		public function __get( $void_name )
		{
			switch ( $void_name )
			{
				case 'str_primayKey' :
					return $this->str_primayKey;
				case 'str_tableKey' :
					return $this->str_tableKey;
				case 'str_tableName' :
					return $this->str_tableName;
				case 'str_viewName' :
					return $this->str_viewName;
				default :
				//获取数据表结构信息
				if( isset( $this->arr_bill[$void_name] ) )
					return $this->arr_bill[$void_name]['value'];
				return false;
			}
		}
		
		/**
		 * 构造数据表的描述信息数组
		 *
		 * @return boolean
		 */
		protected function initSelf()
		{
			/**
			 * 定义数据表结构描述
			 * @example  $this->arr_bill['c2'] = array( 'type' => 'int', 'name' => 'testColumnNo.1', 'isNull' => INT_BOOL_FALSE );
			 */
			
			return true;
		}//function initSelf
		
		/**
		 * 对arr_bill的数据进行合法性检查
		 *
		 * @return boolean
		 */
		protected function checkData( $bool_update = false )
		{
			$_bool_isOK		= true;			//验证状态标志位
			$_str_result	= '';				//保存SQL语句键值对
			foreach ( $this->arr_bill as $_str_key => $_arr_value ) 
			{
				//索引主键不在此处验证
				if( $_str_key == $this->str_primayKey )
					continue;
					
				//UPDATE操作验证
				if( $bool_update )
				{
					//对于未赋值或不参与更新操作的内容不做处理
					if( is_null( $_arr_value['value'] ) || ( $_arr_value['action'] != 'a' && $_arr_value['action'] != 'u' ) )
						continue;
				}
				else 
				{
					//对于不参与新建操作的内容不做处理
					if( $_arr_value['action'] != 'a' && $_arr_value['action'] != 'i' )
						continue;
				}
					
				//验证数据合法性				
//				if( !$this->checkParameter( $_str_key, $_arr_value ) )
//					$_bool_isOK = false;
				if( !Axion_orm_DataSchema::check( $_arr_value ) )
					$_bool_isOK = false;
				else 
				{
					/**
					 * 参数验证外键关系
					 */
					
					/**
					 * 验证参数是否允许重复
					 */
					if( $_arr_value['unique'] )
					{
						if( !$this->obj_MySQL )
							$this->obj_MySQL = Axion_db_MySQL::_init();
						$_str_SQLQuery = "SELECT * FROM `{$this->str_tableName}` WHERE `{$_str_key}` = '{$_arr_value['value']}' ";
						//如果当前数据映射中存在索引主键则验证唯一键属性时忽略与该值的唯一性冲突
						if( !is_null( $this->arr_bill[$this->str_primayKey]['value'] ) )
							$_str_SQLQuery .= " AND `{$this->str_primayKey}` <> '{$this->arr_bill[$this->str_primayKey]['value']}' ";
							
						$_void_result = $this->obj_MySQL->querySQL( $_str_SQLQuery );
						if( !empty( $_void_result ) )
						{
							$this->obj_Axion_log->newMessage( Axion_log::$INT_ERR_WARNING, "已存在一个相同的{$this->arr_bill[$_str_key]['name']}。" );
							return false;
						}//if
					}//if
				}//else
								
				/**
				 * 生成SQL语句键值对部分
				 * @desc isExpression  说明当前取值为一个表达式，则在生成的SQL语句中不会对齐两端用引号包括
				 */
				if( $_arr_value['isExpression'] )				
					$_str_result .= ", `{$_str_key}` = {$_arr_value['value']}";
				else
					$_str_result .= ", `{$_str_key}` = '{$_arr_value['value']}'";
			}
			
			if( !$_bool_isOK )
				$_str_result = $_bool_isOK;
			if( !empty( $_str_result ) )
				$_str_result = substr( $_str_result , 1 );
			
			//错误处理
			return $_str_result;
		}//function checkData
	
		/**
		 * 根据当前映射数据生成对应的INSERT SQL语句
		 *
		 * @return string  如果数据验证错误则返回false
		 */
		protected function getInsertSQL()
		{
			$_str_valueString = $this->checkData();		//参数验证并获取对应部分的SQL语句
			if( !$_str_valueString )
				return false;
				
			return " INSERT INTO `{$this->str_tableName}` SET  {$_str_valueString} ";
		}//function getInsertSQL
				
		/**
		 * 根据当前映射数据生成对应的UPDATE SQL语句
		 * @desc 数据更新使用当前映射数据中的索引键值作为更新标识，如果要修改更新对象则需要手动重新指定索引键值
		 *
		 * @return string  如果数据验证错误则返回false
		 */
		protected function getUpdateSQL( )
		{
			$_int_ID = $this->arr_bill[$this->str_primayKey]['value'];
				
			if( empty( $_int_ID ) )
			{
				$this->obj_Axion_log->newMessage( Axion_log::$INT_ERR_WARNING, '未获得修改标识' );
				return false;
			}
			
			$_str_valueString = $this->checkData( true );		//参数验证并获取对应部分的SQL语句
			
			if( $_str_valueString === false )
				return false;
			else if( empty( $_str_valueString ) )
				return '';
				
			return "UPDATE `{$this->str_tableName}` SET {$_str_valueString} WHERE `{$this->str_primayKey}` = '{$_int_ID}' ";
		}//function getUpdateSQL
		
		/**
		 * 插入一条记录
		 *
		 * @param array $arr_data
		 * @return boolean
		 */
		public function insertData()
		{
			$this->arr_bill[$this->str_primayKey]['value'] = null;
			$_str_sql = $this->getInsertSQL();
			
			if( !$_str_sql )
				return false;
				
			if( !$this->obj_MySQL )
				$this->obj_MySQL = Axion_db_MySQL::_init();
				
			if( !$this->obj_MySQL->querySQL( $_str_sql ) )
				return false;
			
			$this->arr_bill[$this->str_primayKey]['value'] = $this->obj_MySQL->getLastInsertID();
			return true;
		}//function insertData
		
		/**
		 * 根据当前映射数据更新数据库记录
		 * @desc 数据更新使用当前映射数据中的索引键值作为更新标识，如果要修改更新对象则需要手动重新指定索引键值
		 *
		 * @return boolean
		 */
		public function updateData()
		{
			if( !$this->obj_MySQL )
				$this->obj_MySQL = Axion_db_MySQL::_init();
				
			//验证参数并获取对应的UPDATE SQL语句
			$_str_sql = $this->getUpdateSQL();
			
			if( $_str_sql === false )
				return false;
			else if( empty( $_str_sql ) )
				return false;
			//执行UPDATE操作
			if( !$this->obj_MySQL->querySQL( $_str_sql ) )
				return false;
				
			return true;
		}//function updateData
		
		/**
		 * 删除记录
		 *
		 * @param &Object::Database $obj_MySQL
		 * @param int $int_ID
		 * @return int|false
		 */
		public function deleteData()
		{
			if( !$this->obj_MySQL )
				$this->obj_MySQL = Axion_db_MySQL::_init();
				
			//删除级联外键关系表
			$_str_SQLQuery =  "DELETE FROM `{$this->str_tableName}` WHERE `{$this->str_primayKey}` = '{$this->arr_bill[$this->str_primayKey]['value']}' ";
			//删除映射数据对应的实体数据
			if( !$this->obj_MySQL->querySQL( $_str_SQLQuery ) )
				return false;
				
			return $this->obj_MySQL->getAffectedRows();
		}//function deleteData	
		
		/**
		 * 将数据信息添加到映射数据中
		 * @desc  参数为一个数组，正常情况下，会将该数组中的键值对的取值赋值
		 * 给与其对应的映射数据的'value'中，
		 * 如果该数据中对应的键没有在映射数据中则会直接丢弃该数据不做任何处理
		 *
		 * @param array $arr_data	参数数组
		 * @return string
		 */
		public function input( $arr_data )
		{
			foreach ( $arr_data as $_void_key => $_void_value ) 
			{
				if( isset( $this->arr_bill[$_void_key] ) )
					$this->arr_bill[$_void_key]['value'] = $_void_value;
			}//end foreach
			
			return true;
		}//function getInsertSQLl
		
		/**
		 * 获取映射数据
		 *
		 * @param string $str_key			如果指定了该参数则返回与其匹配的映射数据，否则返回全部数据
		 * @return array
		 */
		public function getBill( $str_key = null )
		{
			if( !is_null( $str_key ) )
			{
				if( isset( $this->arr_bill[$str_key] ) )
					return $this->arr_bill[$str_key];
				else 
				{
					$this->obj_Axion_log->newMessage( Axion_log::$INT_ERR_WARNING, '未获得指定的数据结构' );
					return false;
				}
			}
			
			return $this->arr_bill;
		}//end function getBill
		
		/**
		 * 设置指定数据的属性
		 *
		 * @param string $str_key						数据列关键字
		 * @param string $str_attributeName			属性名称
		 * @param string $str_value					属性值
		 * @return boolean
		 */
		public function setBill( $str_key, $str_attributeName, $str_value )
		{
			if( !isset( $this->arr_bill[$str_key] ) )
			{
				$this->obj_Axion_log->newMessage( Axion_log::$INT_ERR_WARNING, '未获得指定的数据结构' );
				return false;
			}
			
			$this->arr_bill[$str_key][$str_attributeName] = $str_value;
			return true;
		}//end function setBill
	}//class Axion_orm_DataMap
	
	/**
	 * Finish
	 * o._.o
	 */
?>