<?php
	/**
	 * 根据数据结构生成对应的HTML FORM表单元素
	 * @desc 该类使用DataMap类的验证格式生成可进行JS自动校验的DOM元素
	 * @author [Alone] alonedistian@gmail.com〗
	 * @version 0.1
	 * @package PHPDoc
	 */	
	class Axion_orm_HTMLDoc
	{
		/**
		 * 用于生成FORM表单元素的信息列表
		 * @desc 该列表来自于 DataMap实例的 getBill方法的返回值
		 * @var array
		 */
		protected $arr_bill = array();
		
		/**
		 * 单件模式实例对象
		 * @var Axion_orm_HTMLDoc
		 */
		protected static $obj_this;
		
		/**
		 * 单件模式创建对象
		 *
		 * @param Axion_orm_DataMap $obj_dataMap
		 * @return Axion_orm_HTMLDoc
		 */
		static function _init( Axion_orm_DataMap $obj_dataMap = null )
		{
			if( !Axion_orm_HTMLDoc::$obj_this )
				Axion_orm_HTMLDoc::$obj_this = new Axion_orm_HTMLDoc();
			
			if( !is_null( $obj_dataMap ) )
				Axion_orm_HTMLDoc::$obj_this->input( $obj_dataMap );
			
			return Axion_orm_HTMLDoc::$obj_this;
		} //end function _init
		
	
		/**
		 * 设置要生成FORM表单对象的Axion_orm_DataMap实例
		 *
		 * @param Axion_orm_DataMap $obj_dataMap
		 */
		public function input( Axion_orm_DataMap $obj_dataMap )
		{
			$this->arr_bill = $obj_dataMap->getBill();
			//创建FROM表单对象
			$this->createForm();
		} //end function input
		
	
		/**
		 * 获取生成select 或  input type='checkbox'元素所需的数组信息
		 * @desc 如果参数本身提供了自定义元素列表则使用该列表作为返回数据，否则从指定的外键关键字的主键表中获取数据列表。
		 * 返回数据格式为  array( array( 'name' => '...', 'value=> '...' ), array( 'name' => '...', 'value=> '...' ) ) 
		 * 默认情况下外键关键字名称与数据表名称的对应关系为   关键字  tableName_ID  对应数据表  t_tableName
		 * 其中  name 为选项的提示名称，   value 为选项值
		 *
		 * @param string $str_valueKey			关联外键数据库
		 * @param string $str_nameKey				显示列明名称
		 * @return array
		 */
		protected function getParaList( $str_valueKey, $arr_paraInfo )
		{		
			/**
			 * 从自定义内容中获取表单元素格式参考 $this->getParaList( array )函数返回值
			 */
			if( !empty( $arr_paraInfo['enum'] ) )
				return $arr_paraInfo['enum'];
			
			/**
			 * 从外键管理数据表中获取表单元素
			 */
			$_arr_result		= explode( '_', $str_valueKey );
			$str_tableName		= "t_{$_arr_result[0]}";
			$str_nameKey		= "{$_arr_result[0]}_Name";
			
			$_str_SQLQuery		= "SELECT `{$str_valueKey}` AS `value` , `{$str_nameKey}` AS `name` FROM `{$str_tableName}`";
			$_obj_MySQL			= Axion_db_MySQL::_init();
			$_void_result		= $_obj_MySQL->querySQL( $_str_SQLQuery );
			if( !$_void_result )
			{
				Axion_log::_init()->newMessage( Axion_log::$INT_ERR_ERROR, "从指定的数据表{$str_tableName}中获取{$str_valueKey}的HTML子元素失败，如果不存在该数据表请给出{$str_valueKey}的MENU元素内容！" );
				return false;
			}
			return $_void_result;
		} //end function getParaList
		
	
		/**
		 * 创建input 类型表单元素
		 * @desc 参数$arr_paraInfo为通过DataMap对象实例的getBill函数获取的数据映射结构中的一个元素，
		 * 该函数将在这个元素中追加一个使用HTML作为标识的内容来存储生成的FROM表单元素的HTML结构信息。
		 *
		 * @param string $str_paraKey			关键字
		 * @param array $arr_paraInfo			用于创建表单元素的参数  
		 * @return array
		 */
		protected function createInput( $str_paraKey, $arr_paraInfo )
		{
			$_str_jsChkString	= $this->getAttributeString( $arr_paraInfo );
			$_str_result		= "<input type='{$arr_paraInfo['vType']}' name='{$str_paraKey}' $_str_jsChkString />";
			return $_str_result;
		} //end function createInput
		
	
		/**
		 * 生成checkbox类型表单元素
		 *
		 * @param string $str_paraKey			关键字
		 * @param array $arr_paraInfo			用于创建表单元素的参数  
		 * @return string
		 */
		protected function createCheckBox( $str_paraKey, $arr_paraInfo )
		{
			$_str_jsChkString			= $this->getAttributeString( $arr_paraInfo );
			$_str_value					= is_null( $arr_paraInfo['value'] ) ? $arr_paraInfo['defValue']: $arr_paraInfo['value'];
			$_str_checked				= $_str_value ? "checked='checked'" : '';
			$arr_paraInfo['value']	= '1';
			$_str_result				= "<input type='checkbox' name='{$str_paraKey}' {$_str_checked} $_str_jsChkString />";
			return $_str_result;
		} //end function createCheckBox
		
	
		/**
		 * 生成input type='radio'类型表单元素
		 *
		 * @param string $str_paraKey			关键字
		 * @param array $arr_paraInfo			用于创建表单元素的参数  
		 * @return string
		 */
		protected function createRadio( $str_paraKey, $arr_paraInfo )
		{
			//获取表单元素
			$_void_result = $this->getParaList( $str_paraKey, $arr_paraInfo );
			if( !$_void_result )
				return false;
				
			//当前取值用于校验元素的选中状态
			$_str_currentValue = is_null( $arr_paraInfo['value'] ) ? $arr_paraInfo['defValue']: $arr_paraInfo['value'];
			//保存返回结果
			$_str_result = "";
			
			//生成input type='radio'元素的HTML代码
			foreach( $_void_result as $_arr_paraInfo )
			{
				$_str_attributes	= $this->getAttributeString( $_arr_paraInfo ); //获取属性列表				
				$_str_selected		= ( $_str_currentValue == $_arr_paraInfo['value'] ) ? "selected='selected'" : ''; //验证选中状态
				$_str_result		.= "<input type='radio' name='{$str_paraKey}' {$_str_attributes} {$_str_selected}/>{$_arr_paraInfo['name']}"; //生成HTML代码
			} //end foreach
			
	
			$_str_result .= "</select>";
			
			return $_str_result;
		} //end function createRadio
		
	
		/**
		 * 生成select类型表单元素
		 *
		 * @param string $str_paraKey			关键字
		 * @param array $arr_paraInfo			用于创建表单元素的参数  
		 * @return string
		 */
		protected function createSelect( $str_paraKey, $arr_paraInfo )
		{
			//获取表单元素
			$_void_result = $this->getParaList( $str_paraKey, $arr_paraInfo );
			if( !$_void_result )
				return false;
			
			$_str_currentValue	= is_null( $arr_paraInfo['value'] ) ? $arr_paraInfo['defValue']: $arr_paraInfo['value'];
			$_str_attributes		= $this->getAttributeString( $arr_paraInfo );
			$_str_result			= "<select name='{$str_paraKey}' {$_str_attributes} >";
			
			foreach( $_void_result as $_arr_paraInfo )
			{
				$_str_selected	= ( $_str_currentValue == $_arr_paraInfo['value'] ) ? " selected='selected' " : '';
				$_str_result	.= "<option value='{$_arr_paraInfo['value']}'{$_str_selected}>{$_arr_paraInfo['name']}</option>";
			} //end foreach
			
	
			$_str_result .= "</select>";
			
			return $_str_result;
		} //end function createSelect
		
	
		/**
		 * 创建textarea类型表单元素
		 * @desc 参数$arr_paraInfo为通过DataMap对象实例的getBill函数获取的数据映射结构中的一个元素，
		 * 该函数将在这个元素中追加一个使用HTML作为标识的内容来存储生成的FROM表单元素的HTML结构信息。
		 *
		 * @param string $str_paraKey			关键字
		 * @param array $arr_paraInfo			用于创建表单元素的参数  
		 * @return array
		 */
		protected function createTextarea( $str_paraKey, $arr_paraInfo )
		{
			$_str_result = "<textarea name='{$str_paraKey}' >";
			$_str_result .= is_null( $arr_paraInfo['value'] ) ? $arr_paraInfo['defValue']: $arr_paraInfo['value'];
			$_str_result .= '</textarea>';
			
			return $_str_result;
		} //end function createTextarea
		
	
		/**
		 * 创建用于元素标准属性及JS表单自动验证功能所需使用的自定义属性
		 *
		 * @param array $arr_paraInfo	用于创建属性的参数 
		 * @return string
		 */
		protected function getAttributeString( $arr_paraInfo )
		{
			/**
			 * JS验证属性
			 */
			//错误提示名称
			$_str_chkString = " v_name='{$arr_paraInfo['name']}' ";
			unset( $arr_paraInfo['name'] );
			
			//参数数据类型
			$_str_chkString .= " value_Type='{$arr_paraInfo['type']}' ";
			unset( $arr_paraInfo['type'] );
			
			//最大长度
			if( $arr_paraInfo['maxLength'] )
				$_str_chkString .= " max_Length='{$arr_paraInfo['maxLength']}' ";
			unset( $arr_paraInfo['maxLength'] );
			
			//最小长度
			if( $arr_paraInfo['minLength'] )
				$_str_chkString .= " min_Length='{$arr_paraInfo['minLength']}' ";
			unset( $arr_paraInfo['minLength'] );
			
			//是/否允许为空
			if( !$arr_paraInfo['isNull'] )
				$_str_chkString .= " no_Empty='1' ";
			unset( $arr_paraInfo['isNull'] );
			
			//正则表达式验证
			if( $arr_paraInfo['preg'] )
				$_str_chkString .= " preg_Exp='{$arr_paraInfo['preg']}' ";
			unset( $arr_paraInfo['preg'] );
			
			//是/否为无符号数值
			if( $arr_paraInfo['unique'] )
				$_str_chkString .= " unique='{$arr_paraInfo['unique']}' ";
			unset( $arr_paraInfo['unique'] );
			
			/**
			 * 其他属性
			 */
			if( !empty( $arr_paraInfo ) ) 
			{
				foreach( $arr_paraInfo as $_void_key => $_void_value )
					$_str_chkString .= " {$_void_key}='{$_void_value}' ";
			}
			
			return $_str_chkString;
		} //end function getAttributeString
		
	
		/**
		 * 创建表单元素操作入口
		 * @desc 该函数会根据元素的具体情况调用不同类型元素的创建函数来生成对应的HTML信息
		 *
		 * @return true
		 */
		protected function createForm() 
		{
			foreach( $this->arr_bill as $_void_key => $_arr_paraInfo ) 
			{
				switch( $_arr_paraInfo['vType'] )
				{
					case 'textarea' : //创建TEXTAREA
						$this->arr_bill[$_void_key]['html'] = $this->createTextarea( $_void_key, $_arr_paraInfo );
						break;
					case 'select' : //创建SELECT
						$this->arr_bill[$_void_key]['html'] = $this->createSelect( $_void_key, $_arr_paraInfo );
						break;
					case 'checkbox' : //创建CHECKBOX
						$this->arr_bill[$_void_key]['html'] = $this->createCheckBox( $_void_key, $_arr_paraInfo );
						break;
					case 'radio' : //创建	INPUT TYPE = 'RADIO'
						$this->arr_bill[$_void_key]['html'] = $this->createRadio( $_void_key, $_arr_paraInfo );
						break;
					default : //创建其他类型元素
						$this->arr_bill[$_void_key]['html'] = $this->createInput( $_void_key, $_arr_paraInfo );
						break;
				} //switch
			} //end foreach
			
	
			return true;
		} //end function createForm
		
	
		/**
		 * 获取生成后的标签列表
		 *
		 * @param string $str_key   关键字 如果指定了该值 则获取该关键字对应的标签信息
		 * @return array
		 */
		public function getForm( $str_key = null )
		{
			if( is_null( $str_key ) )
				return $this->arr_bill;
			else 
			{
				if( isset( $this->arr_bill[$str_key] ) )
					return $this->arr_bill[$str_key];
				
				$_obj_log = log::_init();
				$_obj_log->newMessage( log::$INT_ERR_WARNING, '未定义的标签数据' );
				return false;
			} //else
		} //end function getForm
	}
	/**
	 * Finish
	 * o._.o
	 */
?>