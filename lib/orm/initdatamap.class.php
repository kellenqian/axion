<?php
	class Axtion_orm_InitDataMap
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
		
		protected $str_tableName;
		
		protected $str_keyName;
		
		/**
		 * 初始化，但真正的初始化是由initSelf方法完成的。
		 *
		 */
		public function __construct( $str_tableName, $str_keyName = null )
		{
			$this->str_tableName = $str_tableName;
			
			if( is_null( $str_keyName ) )
				$str_keyName = substr( $this->str_tableName, 2 ) . '_ID';
			
			$this->obj_processStatus = ProcessStatus::_init();
			
			$this->arr_billFormat = array( 
				//数值
				DataMap::TYPE_INT		=> array( 'value'				=> null,
																 'defValue'			=> 0,
																 'isExpression'	=> true,
																 'maxLength'		=> null,
																 'minLength'		=> null,
																 'isNull'			=> false,
																 'preg'				=> '',
																 'unsigned'			=> true,
																 'unique'			=> false,
																 'vType'				=> 'text',
																 'action'			=> 'a',
																 ),
				//浮点
				DataMap::TYPE_FLOAT		=> array( 'value'				=> null,
																 'defValue'			=> 0.00,
																 'isExpression'	=> true,
																 'maxLength'		=> null,
																 'minLength'		=> null,
																 'isNull'			=> false,
																 'preg'				=> '',
																 'unsigned'			=> true,
																 'unique'			=> false,
																 'vType'				=> 'text',
																 'action'			=> 'a',
																 ),
				//字符串
				DataMap::TYPE_STRING			=> array( 'value'				=> null,
																 'defValue'			=> null,
																 'isExpression'	=> false,
																 'maxLength'		=> 64,
																 'minLength'		=> null,
																 'isNull'			=> false,
																 'preg'				=> '',
																 'unsigned'			=> true,
																 'unique'			=> false,
																 'vType' 			=> 'text',
																 'action'			=> 'a',
																 ),
				//邮件地址
				DataMap::TYPE_MAIL 			=> array( 'value'				=> null,
																 'defValue'			=> null,
																 'isExpression'	=> false,
																 'maxLength'		=> 64,
																 'minLength'		=> null,
																 'isNull'			=> false,
																 'preg'				=> REG_MAIL,
																 'unsigned'			=> true,
																 'unique'			=> false,
																 'vType'				=> 'text',
																 'action'			=> 'a',
																 ),
				//日期
				DataMap::TYPE_DATA 			=> array( 'value'				=> null,
																 'defValue'			=> 'CURRENT_TIMESTAMP',
																 'isExpression'	=> true,
																 'maxLength'		=> 19,
																 'minLength'		=> null,
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
//			foreach ( $this->arr_bill as $_void_key => $_arr_value ) 
//				$this->arr_bill[$_void_key] = $this->formatParameter( $_arr_value );
				
			return true;
		}//function __construct
	}

?>