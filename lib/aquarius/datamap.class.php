<?php
	/**
	 * 数据表结构映射类
	 * @desc 该类从原有的TableSeed类中拆分后形成，将有关数据格式相关的操作部分拆分出来形成该类
	 * @author [Alone] alonedistian@gmail.com〗
	 * @version 0.1
	 * @package PHPDoc
	 */
	class DataMap
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
		public static $str_typeInt = 'int';
		
		/**
		 * 参数验证类型：浮点型
		 * @var string
		 */
		public static $str_typeFloat = 'flt';
		
		/**
		 * 参数验证类型：字符串型
		 * @var string
		 */
		public static $str_typeString = 'str';
		
		/**
		 * 参数验证类型：邮件地址型
		 * @var string
		 */
		public static $str_typeMail = 'mail';
		
		/**
		 * 参数验证类型：日期型
		 * @var string
		 */
		public static $str_typeDate = 'date';
		
		protected $obj_processStatus;
		
		/**
		 * 定义基本数据类型默认指标
		 * @desc 指标类型包括 ：
		 * 		name				: 当前列中文名称，作为自动验证生成错误提示信息时的显示名称；
		 * 		value				: 当前值；
		 * 		defValue			: 默认值；
		 * 		isExpression	: 标识当前值是否为一个表达式，如果不是则在生成相关的SQL语句时在值的两端使用引号包括；
		 * 		length			: 当前值的长度 如果该值为空，则不进行验证。否则对于int类型最大值为10E(length)，对于其他类型则要求串长度不超过该值；
		 * 		isNull			: 标识当前值是否允许为空；
		 * 		preg				: 当前值需要匹配的正则表达式；
		 * 		unsigned			: 当前值是否允许为负数；
		 * 		unique			: 当前值是否允许与已经存在信息重复；
		 * 		type				: 当前值类型;
		 * 		vType				: 显示元素类型	text, password, select, checkbox,……
		 * 		action			: 允许参与的操作  包括  : all=>全部； u=>UPDATE； i=>INSERT; s=>SELECT; n=>不允许任何操作
		 * @var array
		 */
		protected $arr_billFormat = array();
		
		
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
			$this->obj_processStatus = ProcessStatus::_init();
			
			$this->arr_bill = array();
			
			$this->arr_billFormat = array( 
				//数值
				DataMap::$str_typeInt		=> array( 'value'				=> null,
																 'defValue'			=> 0,
																 'isExpression'	=> true,
																 'length'			=> false,
																 'isNull'			=> false,
																 'preg'				=> '',
																 'unsigned'			=> true,
																 'unique'			=> false,
																 'vType'				=> 'text',
																 'action'			=> 'a',
																 ),
				//浮点
				DataMap::$str_typeFloat		=> array( 'value'				=> null,
																 'defValue'			=> 0.00,
																 'isExpression'	=> true,
																 'length'			=> false,
																 'isNull'			=> false,
																 'preg'				=> '',
																 'unsigned'			=> true,
																 'unique'			=> false,
																 'vType'				=> 'text',
																 'action'			=> 'a',
																 ),
				//字符串
				DataMap::$str_typeString	=> array( 'value'				=> null,
																 'defValue'			=> null,
																 'isExpression'	=> false,
																 'length'			=> false,
																 'isNull'			=> false,
																 'preg'				=> '',
																 'unsigned'			=> true,
																 'unique'			=> false,
																 'vType' 			=> 'text',
																 'action'			=> 'a',
																 ),
				//邮件地址
				DataMap::$str_typeMail		=> array( 'value'				=> null,
																 'defValue'			=> null,
																 'isExpression'	=> false,
																 'length'			=>64,
																 'isNull'			=> false,
																 'preg'				=> REG_MAIL,
																 'unsigned'			=> true,
																 'unique'			=> false,
																 'vType'				=> 'text',
																 'action'			=> 'a',
																 ),
				//日期
				DataMap::$str_typeDate		=> array( 'value'				=> null,
																 'defValue'			=> 'CURRENT_TIMESTAMP',
																 'isExpression'	=> true,
																 'length'			=> INT_TAG_ZERO,
																 'isNull'			=> false,
																 'preg'				=> '',
																 'unsigned'			=> true,
																 'unique'			=> false,
																 'vType'				=> 'text',
																 'action'			=> 'a',
																 )
													);
		
			//初始化数据结构
			$this->initSelf();
			if( empty( $this->str_tableKey ) )
			{
				$this->obj_processStatus->newMessage( ProcessStatus::$INT_ERR_ERROR, '请指定数据表关键字' );
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
			
			/**
			 * 给格式化数据结构
			 */
			foreach ( $this->arr_bill as $_void_key => $_arr_value ) 
				$this->arr_bill[$_void_key] = $this->formatParameter( $_arr_value );
				
			return true;
		}//function __construct
				
		public function __set( $str_name, $str_value )
		{
			if( isset( $this->arr_bill[$str_name] ) )
				$this->arr_bill[$str_name]['value'] = $str_value;
			else 
				$this->obj_processStatus->newMessage( ProcessStatus::$INT_ERR_NOTICE, '未获得指定的数据结构' );
				
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
		 * 补齐指定数据的验证信息
		 * @desc 验证关键性属性（类型，显示名称，列名称，）是否存在，验证数据类型是否存在定义。如果验证成功则补齐未定义内容。
		 *
		 * @param array $arr_paraInfo
		 * @return array $arr_paraInfo
		 */
		protected function formatParameter( $arr_paraInfo )
		{
			/**
			 * 验证当前参数的相关属性是否完整
			 * 1.必须指定参数类型；
			 * 2.指定的参数类型系统中必须存在；
			 * 3.必须指定参数的显示名称。
			 */
			if( empty( $arr_paraInfo['type'] ) )
			{
				$this->obj_processStatus->newMessage( ProcessStatus::$INT_ERR_ERROR , '对于该参数必须指定一个属于类型以进行自动验证。' );
				return false;
			}//if
			
			$_str_thisParaType = $arr_paraInfo['type'];
			
			if( empty( $_str_thisParaType[$_str_thisParaType] ) )
			{
				$this->obj_processStatus->newMessage( ProcessStatus::$INT_ERR_ERROR , '当前系统中不支持此参数类型，您可以为该参数选择默认的数值（int）或字符串类型（str）。' );
				return false;
			}//if
			
			if( empty( $arr_paraInfo['name'] ) )
			{
				$this->obj_processStatus->newMessage( ProcessStatus::$INT_ERR_ERROR , '请为该参数指定一个显示名称。' );
				return false;
			}//if
			
			if( empty( $arr_paraInfo['value'] ) )
				$arr_paraInfo['value'] = $this->arr_billFormat[$_str_thisParaType]['value'];
			if( empty( $arr_paraInfo['defValue'] ) )
				$arr_paraInfo['defValue'] = $this->arr_billFormat[$_str_thisParaType]['defValue'];
			if( empty( $arr_paraInfo['isExpression'] ) )
				$arr_paraInfo['isExpression'] = $this->arr_billFormat[$_str_thisParaType]['isExpression'];
			if( empty( $arr_paraInfo['length'] ) )
				$arr_paraInfo['length'] = $this->arr_billFormat[$_str_thisParaType]['length'];
			if( empty( $arr_paraInfo['isNull'] ) )
				$arr_paraInfo['isNull'] = $this->arr_billFormat[$_str_thisParaType]['isNull'];
			if( empty( $arr_paraInfo['preg'] ) )
				$arr_paraInfo['preg'] = $this->arr_billFormat[$_str_thisParaType]['preg'];
			if( empty( $arr_paraInfo['unsigned'] ) )
				$arr_paraInfo['unsigned'] = $this->arr_billFormat[$_str_thisParaType]['unsigned'];
			if( empty( $arr_paraInfo['unique'] ) )
				$arr_paraInfo['unique'] = $this->arr_billFormat[$_str_thisParaType]['unique'];
						
//			if( $arr_paraInfo['isNull'] && ( empty( $arr_paraInfo['value'] ) ) )
//			{
//				$arr_paraInfo['isExpression'] = true;
//				$arr_paraInfo['value'] = 'NULL';
//			}//if

			if( empty( $arr_paraInfo['vType'] ) )
				$arr_paraInfo['vType'] = $this->arr_billFormat[$_str_thisParaType]['vType'];
				
			if( empty( $arr_paraInfo['action'] ) )
				$arr_paraInfo['action'] = $this->arr_billFormat[$_str_thisParaType]['action'];
			
			return $arr_paraInfo;
		}//end function formatParameter
	
		
		/**
		 * 根据参数的验证信息验证指定数据的合法性，并将相关的结果保存到obj_processStatus中
		 *
		 * @param array $arr_paraInfo
		 * @return boolean
		 */
		protected function checkParameter( $str_key, $arr_paraInfo )
		{
			if( !$arr_paraInfo )
				return false;
			
			//验证参数是否可以为空、格式及长度是否符合要求			
			if( $arr_paraInfo['isNull'] && empty( $arr_paraInfo['value'] ) )
				return true;
				
			if( is_null( $arr_paraInfo['value'] ) )
				$arr_paraInfo['value'] = $arr_paraInfo['defValue'];
			
			/**
			 * 参数验证外键关系
			 */
			
			/**
			 * 验证参数是否允许重复
			 */
			if( $arr_paraInfo['unique'] )
			{
				if( !$this->obj_MySQL )
					$this->obj_MySQL = MySQL::_init();
				$_str_SQLQuery = "SELECT * FROM `{$this->str_tableName}` WHERE `{$str_key}` = '{$arr_paraInfo['value']}' ";
				//如果当前数据映射中存在索引主键则验证唯一键属性时忽略与该值的唯一性冲突
				if( !is_null( $this->arr_bill[$this->str_primayKey]['value'] ) )
					$_str_SQLQuery .= " AND `{$this->str_primayKey}` <> '{$this->arr_bill[$this->str_primayKey]['value']}' ";
					
				$_void_result = $this->obj_MySQL->querySQL( $_str_SQLQuery );
				if( !empty( $_void_result ) )
				{
					$this->obj_processStatus->newMessage( ProcessStatus::$INT_ERR_WARNING, "已存在一个相同的{$this->arr_bill[$str_key]['name']}。" );
					return false;
				}//if
			}//if
				
			switch ( $arr_paraInfo['type'] )
			{
				case DataMap::$str_typeInt :	
					if( !checkInt( $arr_paraInfo['value'], ( empty( $arr_paraInfo['length'] ) ? null : pow( 10, $arr_paraInfo['length'] ) ) ) )
					{
						$this->obj_processStatus->newMessage( ProcessStatus::$INT_ERR_WARNING, "'{$arr_paraInfo['name']}'格式错误。" );
						return false;
					}//if
					else if( $arr_paraInfo['unsigned'] && ( $arr_paraInfo['value'] != abs( $arr_paraInfo['value'] ) ) )
					{
						$this->obj_processStatus->newMessage( ProcessStatus::$INT_ERR_WARNING, "'{$arr_paraInfo['name']}'格式错误。" );
						return false;
					}//else if
					break;
					
				case DataMap::$str_typeFloat :
					if( !checkFloat( $arr_paraInfo['value'], ( empty( $arr_paraInfo['length'] ) ? null : pow( 10, $arr_paraInfo['length'] ) ) ) )
					{
						$this->obj_processStatus->newMessage( ProcessStatus::$INT_ERR_WARNING, "'{$arr_paraInfo['name']}'格式错误。" );
						return false;
					}//if
					else if( $arr_paraInfo['unsigned'] && ( $arr_paraInfo['value'] != abs( $arr_paraInfo['value'] ) ) )
					{
						$this->obj_processStatus->newMessage( ProcessStatus::$INT_ERR_WARNING, "'{$arr_paraInfo['name']}'格式错误。" );
						return false;
					}//else if
					break;
					
				default :
					if( empty( $arr_paraInfo['value'] ) )
					{
						$this->obj_processStatus->newMessage( ProcessStatus::$INT_ERR_WARNING, "'{$arr_paraInfo['name']}'不能为空。" );
						return false;
					}//if
					elseif( !checkString( $arr_paraInfo['value'], ( empty( $arr_paraInfo['length'] ) ? null : $arr_paraInfo['length'] ), null, $arr_paraInfo['preg'] ) )
					{
						$this->obj_processStatus->newMessage( ProcessStatus::$INT_ERR_WARNING, "'{$arr_paraInfo['name']}'格式错误。" );
						return false;
					}//if
			}//switch
			return true;
		}//end function checkParameter
		
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
					//对于为赋值内容不做处理
					if( is_null( $_arr_value['value'] ) )
						continue;
						
					if( $_arr_value['action'] != 'a' && $_arr_value['action'] != 'u' )
						continue;
				}
				else 
				{
					if( $_arr_value['action'] != 'a' && $_arr_value['action'] != 'i' )
						continue;
				}
					
				//验证数据合法性				
				if( !$this->checkParameter( $_str_key, $_arr_value ) )
					$_bool_isOK = false;
								
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
				$this->obj_processStatus->newMessage( ProcessStatus::$INT_ERR_WARNING, '未获得修改标识' );
				return false;
			}
			
			$_str_valueString = $this->checkData( true );		//参数验证并获取对应部分的SQL语句
			
			if( $_str_valueString === false )
				return false;
			else if( empty( $_str_valueString ) )
				return '';
				
			return "UPDATE `{$this->str_tableName}` SET {$_str_valueString} WHERE `{$this->str_primayKey}` = '{$_int_ID}' ";
		}//function getUpdateSQL
		
		protected function createInput( $str_paraKey, $arr_paraInfo, $str_value )
		{
			$_arr_result = array( 'name' => $arr_paraInfo['name'] );
			$_str_jsChkString = $this->getJsChkString( $arr_paraInfo );
			$_arr_result['html'] = "<input type='{$arr_paraInfo['vType']}' name='{$str_paraKey}' value='{$str_value}' $_str_jsChkString />";
			return $_arr_result;
		}//end function createInput
		
		protected function createCheckBox( $str_paraKey, $arr_paraInfo, $str_value )
		{
			$_arr_result = array( 'name' => $arr_paraInfo['name'] );
			$_arr_result['html'] = "<input type='checkbox' name='{$str_paraKey}' value='1' ";
			if( $str_value )
				$_arr_result['html'] .= "checked='checked'";
				
			$_arr_result['html'] .= "/>";
			return $_arr_result;
		}//end function createCheckBox
		
		protected function createRadio( $str_paraKey, $arr_paraInfo, $void_value, $arr_paras = array() )
		{
			$_arr_result = array( 'name' => $arr_paraInfo['name'] );
			foreach ( $arr_paras as $_arr_paraInfo ) 
			{
				$_arr_result['html'] .= "{$_arr_paraInfo['key']}<input type='radio' name='{$str_paraKey}' value='{$_arr_paraInfo['value']}'";
				if( $_arr_paraInfo['value'] == $void_value )
					$_arr_result['html'] .= "selected='selected'";
				$_arr_result['html'] .= "/>";
			}//end foreach
			
			return $_arr_result;
		}//end function createRadio
		
		protected function createSelect( $str_paraKey, $arr_paraInfo, $str_value, $arr_paras = array() )
		{
			$_arr_result = array( 'name' => $arr_paraInfo['name'] );
			$_arr_result['html'] = "<select name='{$str_paraKey}'>";
			foreach ( $arr_paras as $_arr_paraInfo ) 
			{
				$_arr_result['html'] .= "<option value='{$_arr_paraInfo['value']}'";
				if( $_arr_paraInfo['value'] == $void_value )
					$_arr_result['html'] .= "selected='selected'";
				$_arr_result['html'] .= ">{$_arr_paraInfo['key']}</option>";
			}//end foreach
			$_arr_result['html'] .= "</select>";
			
			return $_arr_result;
		}//end function createSelect
		
		protected function createTextarea( $str_paraKey, $arr_paraInfo, $str_value )
		{
			$_arr_result = array( 'name' => $arr_paraInfo['name'] );
			$_arr_result['html'] = "<textarea name='{$str_paraKey}' >";
			$_arr_result['html'] .= $str_value;
			$_arr_result['html'] .= '</textarea>';
			
			return $_arr_result;
		}//end function createTextarea
		
		protected function getJsChkString( $arr_paraInfo )
		{
			$_str_chkString = " v_name='{$arr_paraInfo['name']}' ";
			$_str_chkString .= " value_Type='{$arr_paraInfo['type']}' ";
			if( $arr_paraInfo['length'] )
				$_str_chkString .= " max_Length='{$arr_paraInfo['length']}' ";
			if( !$arr_paraInfo['isNull'] )
				$_str_chkString .= " no_Empty='1' ";
			if( $arr_paraInfo['preg'] )
				$_str_chkString .= " preg_Exp='{$arr_paraInfo['preg']}' ";
			if( $arr_paraInfo['unique'] )
				$_str_chkString .= " unique='{$arr_paraInfo['unique']}' ";
			return $_str_chkString;
		}//end function getJsChkString
		
		
		public function showForm( $arr_paras = array() )
		{
			$_arr_result = array();
			
			foreach ( $this->arr_bill as $_void_key => $_arr_paraInfo ) 
			{
				$_str_para = $_arr_paraInfo['value'] ? $_arr_paraInfo['value'] : $_arr_paraInfo['defValue'];
					
				switch( $_arr_paraInfo['vType'] )
				{
					case 'textarea' :
						$_arr_result[] = $this->createTextarea( $_void_key, $_arr_paraInfo, $_str_para );
						break;
					case 'select' :
						$_arr_result[] = $this->createSelect( $_void_key, $_arr_paraInfo, $_str_para );
						break;
					case 'checkbox' :
						$_arr_result[] = $this->createCheckBox( $_void_key, $_arr_paraInfo, $_str_para );
						break;
					case 'radio' :
						$_arr_result[] = $this->createRadio( $_void_key, $_arr_paraInfo, $_str_para );
						break;
					default :
						$_arr_result[] = $this->createInput( $_void_key, $_arr_paraInfo, $_str_para );
						break;
				}//switch
			}//end foreach
			
			return $_arr_result;
		}//end function showForm
		
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
				$this->obj_MySQL = MySQL::_init();
				
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
				$this->obj_MySQL = MySQL::_init();
				
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
				$this->obj_MySQL = MySQL::_init();
				
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
					$this->obj_processStatus->newMessage( ProcessStatus::$INT_ERR_WARNING, '未获得指定的数据结构' );
					return false;
				}
			}
			
			return $this->arr_bill;
		}//end function getBill
	}//class DataMap
	
	/**
	 * Finish
	 * o._.o
	 */
?>