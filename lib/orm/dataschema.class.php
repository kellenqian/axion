<?php
	/**
	 * 数据表结构校验
	 * @desc 该类从原有的TableSeed类中拆分后形成，将有关数据校验相关的操作部分拆分出来形成该类
	 * @author [Alone] alonedistian@gmail.com〗
	 * @version 0.1
	 * @package PHPDoc
	 */
	class Axion_orm_DataSchema
	{
		/**
		 * 用于生成FORM表单元素的信息列表
		 * @desc 该列表来自于 DataMap实例的 getBill方法的返回值
		 * @var array
		 */
		protected $arr_bill = array();
		
		/**
		 * 单件模式实例对象
		 * @var Axion_orm_DataSchema
		 */
		protected static $obj_this;
		
		protected $obj_processStatus;
		
		public function __construct()
		{
			$this->obj_processStatus = ProcessStatus::_init();
		}//end function __construct
		
		/**
		 * 单件模式创建对象
		 *
		 * @return Axion_orm_DataSchema
		 */
		static function _init()
		{
			 if( Axion_orm_DataSchema::$obj_this )
			 	return Axion_orm_DataSchema::$obj_this;
			 else 
			 	Axion_orm_DataSchema::$obj_this = new Axion_orm_DataSchema();
			 	
			 return Axion_orm_DataSchema::$obj_this;
		}//end function _init
		
		public static function check( $arr_paraInfo, $str_value = null )
		{
			$_obj_this = Axion_orm_DataSchema::_init();
			if( !is_null( $str_value ) )
				$arr_paraInfo['value'] = $str_value;
				
			return $_obj_this->checkParameter( $arr_paraInfo );
		}//end function check
		
		
		
		/**
		 * 根据参数的验证信息验证指定数据的合法性，并将相关的结果保存到obj_processStatus中
		 *
		 * @param array $arr_paraInfo
		 * @return boolean
		 */
		protected function checkParameter( $arr_paraInfo )
		{
			if( !$arr_paraInfo )
			{
				$this->obj_processStatus->newMessage( ProcessStatus::$INT_ERR_WARNING, "请指定一个要验证的信息" );
				return false;
			}
			
			
			//验证参数是否可以为空、格式及长度是否符合要求			
			if( $arr_paraInfo['isNull'] && empty( $arr_paraInfo['value'] ) )
				return true;
				
			switch ( $arr_paraInfo['type'] )
			{
				case DataMap::TYPE_INT :
					return $this->checkInt( $arr_paraInfo );
					
				case DataMap::TYPE_FLOAT :
					return $this->checkFloat( $arr_paraInfo );
					
				case DataMap::TYPE_DATE :
					return $this->checkDate( $arr_paraInfo );
					
				case DataMap::TYPE_MAIL :
					return $this->checkMail( $arr_paraInfo );
					
				default :
					return $this->checkString( $arr_paraInfo );
			}//switch
		}//end function checkParameter
		
		protected function checkInt( $arr_paraInfo )
		{
			if( !checkInt( $arr_paraInfo['value'], $arr_paraInfo['maxLength'], $arr_paraInfo['minLength']  ) )
			{
				$this->obj_processStatus->newMessage( ProcessStatus::$INT_ERR_WARNING, "'{$arr_paraInfo['name']}'格式错误。" );
				return false;
			}//if
			else if( $arr_paraInfo['unsigned'] && ( $arr_paraInfo['value'] != abs( $arr_paraInfo['value'] ) ) )
			{
				$this->obj_processStatus->newMessage( ProcessStatus::$INT_ERR_WARNING, "'{$arr_paraInfo['name']}'格式错误。" );
				return false;
			}//else if
			
			return true;
		}//end function checkInt
		
		protected function checkFloat( $arr_paraInfo )
		{
			if( !checkFloat( $arr_paraInfo['value'], $arr_paraInfo['maxLength'], $arr_paraInfo['minLength'] ) )
			{
				$this->obj_processStatus->newMessage( ProcessStatus::$INT_ERR_WARNING, "'{$arr_paraInfo['name']}'格式错误。" );
				return false;
			}//if
			else if( $arr_paraInfo['unsigned'] && ( $arr_paraInfo['value'] != abs( $arr_paraInfo['value'] ) ) )
			{
				$this->obj_processStatus->newMessage( ProcessStatus::$INT_ERR_WARNING, "'{$arr_paraInfo['name']}'格式错误。" );
				return false;
			}//else if
			
			return true;
		}//end function checkFloat
		
		protected function checkString( $arr_paraInfo )
		{
			if( empty( $arr_paraInfo['value'] ) )
			{
				if( empty( $arr_paraInfo['defValue'] ) )
				{
					$this->obj_processStatus->newMessage( ProcessStatus::$INT_ERR_WARNING, "'{$arr_paraInfo['name']}'不能为空。" );
					return false;
				}
				else
				{
					$this->obj_processStatus->newMessage( ProcessStatus::$INT_ERR_NOTICE, "'{$arr_paraInfo['name']}'启用了默认值。" );
					$arr_paraInfo['value'] = $arr_paraInfo['defValue'];
				}
			}//if
			elseif( !checkString( $arr_paraInfo['value'], $arr_paraInfo['maxLength'], $arr_paraInfo['minLength'], $arr_paraInfo['preg'] ) )
			{
				$this->obj_processStatus->newMessage( ProcessStatus::$INT_ERR_WARNING, "'{$arr_paraInfo['name']}'格式错误。" );
				return false;
			}//if
			
			return true;
		}//end function checkString
		
		
		protected function checkDate( $arr_paraInfo )
		{
			return $this->checkString( $arr_paraInfo );
		}//end function checkDate
		
		protected function checkMail( $arr_paraInfo )
		{
			if( empty( $arr_paraInfo['preg'] ) )
				$arr_paraInfo['preg'] = REG_MAIL;
				
			return $this->checkString( $arr_paraInfo );
		}//end function checkMail
	}//class Axion_orm_DataSchema
?>