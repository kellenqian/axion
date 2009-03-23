<?php
	/**
	 * 根据数据表结构生成对应的映射文件
	 * @desc 该文件保存在当前项目下的lib/orm目录中 使用“数据表名称”+“.class.php”作为文件名
	 * @author [Alone] alonedistian@gmail.com〗
	 * @version 0.1
	 * @package PHPDoc
	 */	
	class Axion_orm_InitDataMap 
	{
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
		 * 数据表名称
		 * @var string
		 */
		protected $str_tableName;
		
		/**
		 * 数据表关键字
		 * @var string
		 */
		protected $str_keyName;
		
		/**
		 * 数据列信息
		 * @desc 该属性为一个二维数组  用于保存使用 DESC `table_name`的方式获取到的数据列西悉尼
		 * @var array
		 */
		protected $arr_tableColumns;
		
		/**
		 * 消息池对象实例
		 *
		 * @var Axion_log
		 */
		protected $obj_Axion_log;
		
		/**
		 * 数据库对象实例
		 *
		 * @var Axion_db_MySQL
		 */
		protected $obj_MySQL;
		
		/**
		 * 初始化，但真正的初始化是由initSelf方法完成的。
		 *
		 */
		public function __construct( $str_tableName, $str_keyName = null )
		{
			//要生成DATAMAP文件的原型数据表
			$this->str_tableName = $str_tableName;
			
			if( is_null( $str_keyName ) )
				$str_keyName = substr( $this->str_tableName, 2 ) . '_ID';
			$this->str_keyName = $str_keyName;
			
			$this->obj_Axion_log = Axion_log::_init();
			
			$this->arr_billFormat = array(
			//布尔类型默认属性
			Axion_orm_DataMap::TYPE_BOOLEAN	=> array( 'value' => null, 'type' => Axion_orm_DataMap::TYPE_BOOLEAN,	'defValue' => 0,							'isExpression' => false,	'maxLength' => 1,		'minLength' => 1, 	'isNull' => false, 'preg' => '',					'unsigned' => true, 'unique' => false, 'vType' => 'checkbox',	'action' => 'a' ),
			//数值类型默认属性
			Axion_orm_DataMap::TYPE_INT		=> array( 'value' => null, 'type' => Axion_orm_DataMap::TYPE_INT,			'defValue' => 0,							'isExpression' => true,		'maxLength' => null,	'minLength' => null,	'isNull' => false, 'preg' => '',					'unsigned' => true, 'unique' => false, 'vType' => 'text',		'action' => 'a' ),
			//浮点类型默认属性
			Axion_orm_DataMap::TYPE_FLOAT		=> array( 'value' => null, 'type' => Axion_orm_DataMap::TYPE_FLOAT,		'defValue' => 0.00,						'isExpression' => true,		'maxLength' => null,	'minLength' => null,	'isNull' => false, 'preg' => '',					'unsigned' => true, 'unique' => false, 'vType' => 'text',		'action' => 'a' ),
			//字符串类型默认属性
			Axion_orm_DataMap::TYPE_STRING	=> array( 'value' => null, 'type' => Axion_orm_DataMap::TYPE_STRING,		'defValue' => null,						'isExpression' => false,	'maxLength' => 64,	'minLength' => null,	'isNull' => false, 'preg' => '',					'unsigned' => true, 'unique' => false, 'vType' => 'text',		'action' => 'a' ),
			//邮件地址类型默认属性
			Axion_orm_DataMap::TYPE_MAIL		=> array( 'value' => null, 'type' => Axion_orm_DataMap::TYPE_MAIL,		'defValue' => null,						'isExpression' => false,	'maxLength' => 64,	'minLength' => null,	'isNull' => false, 'preg' => REG_MAIL,			'unsigned' => true, 'unique' => false, 'vType' => 'text',		'action' => 'a' ),
			//日期类型默认属性
			Axion_orm_DataMap::TYPE_DATE		=> array( 'value' => null, 'type' => Axion_orm_DataMap::TYPE_DATE,		'defValue' => 'CURRENT_TIMESTAMP',	'isExpression' => true,		'maxLength' => 19,	'minLength' => 19,	'isNull' => false, 'preg' => REG_DATETIME,	'unsigned' => true, 'unique' => false, 'vType' => 'text',		'action' => 'n' ),
												  );
			
			$this->obj_MySQL = Axion_db_MySQL::_init();
			$this->arr_tableColumns = array();
			
			return true;
		} //function __construct
		
	
		/**
		 * 根据数据字段结构生成对应的类文件
		 *
		 * @param string $str_filePath		文件存储路径
		 * @return boolean
		 */
		function createFile( $str_filePath )
		{
			if( !$this->getColumnInfo() )
				return false;
			
			$_str_columnSet = "";
			foreach( $this->arr_tableColumns as $_int_key => $_arr_columenInfo ) 
			{
				if( !$_void_result = $this->formatColumn( $_arr_columenInfo ) )
					return false;
				
				$_str_columnName = $_void_result['Field'];
				unset( $_void_result['Field'] );
				ksort( $_void_result );
				
				ob_start();
				var_export( $_void_result );
				$_str_result = ob_get_contents();
				ob_end_clean();
				$_str_result = str_replace( "\n", "", $_str_result );
				$_str_columnSet .= "											'{$_str_columnName}' => {$_str_result},\n";
			}
			
			$_str_fileContent = "<?php\n";
			$_str_fileContent .= "	class {$this->str_tableName} extends Axion_orm_DataMap \n";
			$_str_fileContent .= "	{\n";
			$_str_fileContent .= "		function initSelf()\n";
			$_str_fileContent .= "		{\n";
			$_str_fileContent .= '			$this->str_primayKey = \'' . $this->str_keyName . "';\n";
			$_str_fileContent .= '			$this->arr_bill = array' . "(\n";
			$_str_fileContent .= "{$_str_columnSet}\n";
			$_str_fileContent .= "											);\n";
			$_str_fileContent .= "			parent::initSelf();\n";
			$_str_fileContent .= "		}//end function initSelf\n";
			$_str_fileContent .= "	}//class {$this->str_tableName}\n";
			$_str_fileContent .= "	\n\n ";
			$_str_fileContent .= "	/**\n";
			$_str_fileContent .= "	* Finish\n";
			$_str_fileContent .= "	 * o._.o\n";
			$_str_fileContent .= "	 */\n";
			$_str_fileContent .= "?>\n";
			
			file_put_contents( "{$str_filePath}{$this->str_tableName}.class.php", $_str_fileContent );
			return true;
		} //end function createFile
		
	
		/**
		 * 获取原型数据表结构信息
		 * @desc 使用SQL语句  desc `table_name`
		 *
		 * @return boolean
		 */
		function getColumnInfo()
		{
			$_str_SQLQuery = "DESC `{$this->str_tableName}`";
			$_void_result = $this->obj_MySQL->querySQL( $_str_SQLQuery );
			if( !$_void_result )
			{
				$this->obj_Axion_log->newMessage( '未获得指定的数据表，请检查数据库结构是否完整', Axion_log::ERROR );
				return false;
			}
			
			$this->arr_tableColumns = $_void_result;
			return true;
		} //end function getColumnInfo
		
	
		/**
		 * 将数据列结构信息根据需要进行补全
		 *
		 * @param array $arr_info			数据列信息
		 * @return array						补全后的数据列信息
		 */
		protected function formatColumn( $arr_info )
		{
			$_arr_typeInfo = explode( " ", $arr_info['Type'] );
			$_str_type = $_arr_typeInfo[0];
			$_arr_enum = array();
			
			if( preg_match( '/[a-z]+\((.+)\)/i', $_str_type, $_arr_result ) )
			{
				if( ( int ) $_arr_result[1] == $_arr_result[1] )
				{
					$arr_info['maxLength'] = $_arr_result[1] + 0;
					$_str_type = substr( $_arr_result[0], 0, - 1 * strlen( $_arr_result[1] ) - 2 );
				}
				else{
					$_arr_enum = explode( ',', $_arr_result[1] );
					foreach( $_arr_enum as $_int_key => $_void_value )
						$_arr_enum[$_int_key] = array( 'name' => '', 'value' => trim( $_void_value ) );
				}
			}
			
			switch( strtoupper( $_str_type ) ) {
				/**
				 * TINYINT类型
				 * 默认为BOOLEAN类型
				 */
				case 'TINYINT' :
					if( $arr_info['maxLength'] == 1 )
					{
						$arr_info = array_merge( $this->arr_billFormat[Axion_orm_DataMap::TYPE_BOOLEAN], $arr_info );
						$arr_info['type'] = Axion_orm_DataMap::TYPE_BOOLEAN;
					} 
					else
						$arr_info = array_merge( $this->arr_billFormat[Axion_orm_DataMap::TYPE_INT], $arr_info );
					break;
				
				/**
				 * 不同长度的INT类型
				 */
				case 'SMALLINT' :
				case 'MEDIUMINT' :
				case 'INT' :
				case 'BIGINT' :
					$arr_info = array_merge( $this->arr_billFormat[Axion_orm_DataMap::TYPE_INT], $arr_info );
					break;
				
				case 'TIMESTAMP' :
					$arr_info['action'] = 'n';
				case 'DATATIME' :
					$arr_info = array_merge( $this->arr_billFormat[Axion_orm_DataMap::TYPE_DATE], $arr_info );
					break;
				
				case 'DATE' :
					$arr_info = array_merge( $this->arr_billFormat[Axion_orm_DataMap::TYPE_STRING], $arr_info );
					$arr_info['maxLength'] = 10;
					$arr_info['minLength'] = 10;
					$arr_info['preg'] = REG_DATE;
					break;
				
				case 'CHAR' :
					$arr_info = array_merge( $this->arr_billFormat[Axion_orm_DataMap::TYPE_STRING], $arr_info );
					$arr_info['minLength'] = $arr_info['maxLength'];
					break;
				
				case 'TEXT' :
				case 'BLOB' :
					$arr_info = array_merge( $this->arr_billFormat[Axion_orm_DataMap::TYPE_STRING], $arr_info );
					$arr_info['minLength'] = $arr_info['maxLength'] = null;
					break;
				
				case 'ENUM' :
				case 'SET' :
					$arr_info = array_merge( $this->arr_billFormat[Axion_orm_DataMap::TYPE_STRING], $arr_info );
					$arr_info['enum'] = $_arr_enum;
					$arr_info['vType'] = 'select';
					break;
				case 'VARCHAR' :
					$arr_info = array_merge( $this->arr_billFormat[Axion_orm_DataMap::TYPE_STRING], $arr_info );
					break;
				
				default :
					$this->obj_Axion_log->newMessage( "未定义的字段类型{$_str_type}", Axion_log::WARNING );
					return false;
			}
			
			if( $arr_info['Extra'] == 'auto_increment' || $arr_info['Field'] == $this->str_keyName )
				$arr_info['action'] = 'n';
			
			$arr_info['name']			= '';
			$arr_info['unsigned']	= ( isset( $_arr_typeInfo[1] ) && $_arr_typeInfo[1] == 'unsigned' || $arr_info['Extra'] == 'auto_increment' ) ? true : false;
			$arr_info['isNull']		= $arr_info['Null'] == 'NO' ? false : true;
			$arr_info['defValue']	= $arr_info['Default']? $arr_info['Default']: '';
			
			switch( strtoupper( $arr_info['Key'] ) ) 
			{
				case 'UNI' :
					$arr_info['unique'] = true;
					break;
			}
			
			unset( $arr_info['Type'] );
			unset( $arr_info['Null'] );
			unset( $arr_info['Key'] );
			unset( $arr_info['Default'] );
			unset( $arr_info['Extra'] );
			
			return $arr_info;
		} //end function formatColumn
	}
	/**
	 * Finish
	 * o._.o
	 */
?>