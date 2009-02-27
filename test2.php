<?php
	require( 'config/variableDefine.ini.php' );
	require( 'tool/default.fun.php' );
	require_once( 'lib/processtatus.class.php' );
	require_once( 'aquarius/datamap.class.php' );
	require_once( 'aquarius/dataschema.class.php' );
	require_once( 'lib/database/database.class.php' );
	require_once( 'lib/database/mysql.class.php' );
	
	class t_uchk extends DataMap 
	{
		function __construct()
		{
			$this->str_tableKey = 'uchk';
			
			parent::__construct();
		}//end function __construct
		
		function initSelf()
		{
			$this->arr_bill['uchk_ID'] = array( 'type' => DataMap::$str_typeString, 'name' => '创建人编号', 'vType' => 'hidden' );
			$this->arr_bill['uchk_Name'] = array( 'type' => DataMap::$str_typeString, 'name' => '登录名', 'length' => 96, 'unique' => true );
			$this->arr_bill['uchk_Mail'] = array( 'type' => DataMap::$str_typeMail, 'name' => '注册邮箱', 'length' => 96, 'unique' => true );
			$this->arr_bill['uchk_PS'] = array( 'type' => DataMap::$str_typeString, 'name' => '密码', 'length' => 32, 'vType' => 'password' );
			$this->arr_bill['uchk_Code'] = array( 'defValue' => '', 'type' => DataMap::$str_typeString, 'name' => '校验码' );
			$this->arr_bill['uchk_IP'] = array( 'defValue' => '', 'type' => DataMap::$str_typeString, 'name' => 'IP地址', 'length' => 15 , 'action' => 'i' );
			$this->arr_bill['uchk_Time'] = array( 'defValue' => 'CURRENT_TIMESTAMP', 'isExpression' => true, 'type' => DataMap::$str_typeString, 'name' => '注册时间', 'isNull' => true, 'action' => 'n' );
			$this->arr_bill['role_ID'] = array( 'defValue' => '1', 'defValue' => '1', 'type' => DataMap::$str_typeInt, 'name' => '身份标识', 'isNull' => false );
			$this->arr_bill['uchk_Disabled'] = array( 'value' => 0, 'type' => DataMap::$str_typeInt, 'name' => '禁用标识', 'vType' => 'checkbox' );
			
			parent::initSelf();
		}//end function initSelf
	}//class t_uchk
	
	class uchk extends DataSchema 
	{
		function __construct()
		{
			parent::__construct( 't_uchk' );
		}//end function __construct
	}
	
###############################INSERT############################
//	$_obj_class = new t_uchk();
//	$_obj_class->uchk_Name = 'test user 5';
//	$_obj_class->uchk_Mail = 'distian5@eyou.com';
//	$_obj_class->uchk_PS = 'haha';
//	$_obj_class->uchk_Code = 'alsdjflkasjdf';
//	$_obj_class->uchk_IP = '192.168.1.101';
//	$_obj_class->uchk_ID = 1;
//	$_void_result = $_obj_class->insertData();
//	if( !$_void_result )
//	{
//		$obj_processStatus = ProcessStatus::_init();	
//		print_r( $obj_processStatus->getAllData() );
//		exit;
//	}
//	$obj_MySQL = MySQL::_init();
//	echo $obj_MySQL->getAffectedRows();
//	$obj_MySQL->commitData();
//	
//	print_r( $_obj_class->getBill( ) );
###############################GET############################
//	$obj = new dataschema( 't_uchk' );
//   $obj->get1;
//   $obj->get1();	
//   $obj->getuchk_Name( 'Alone' );		
//   $obj->getuchk_Name( 'Alone', 10 );								
//   $obj->getuchk_Name( 'Alone', 10, " AND `uchk_Disabled` != '1' ORDER BY `uchk_Time` " );
//
//   $obj->top;								
//   $obj->top();								
//   $obj->top3();																		
//   $obj->top3( 'uchk_Time' );												
//  
//   $obj->end;
//   $obj->end();					
//   $obj->end3();				
//   $obj->end3( 'uchk_Time' );
   
   
###############################UPDATE############################
	
   $obj = new uchk();
   $_void_result = $obj->end;
   if( !$_void_result )
   {
   	$obj_processStatus = ProcessStatus::_init();
   	print_r( $obj_processStatus->getAllData() );
   }//if
   
   $_obj_uchk = $_void_result[0];
//   print_r( $_obj_uchk->getBill() );
   
   $_obj_uchk->uchk_Disabled = '10000000';
   if( !$_obj_uchk->updateData() )
   {
   	$obj_processStatus = ProcessStatus::_init();
   	print_r( $obj_processStatus->getAllData() );
   	
   	$_obj_MySQL = MySQL::_init();
   	$_obj_MySQL->commitData( false );
   }//if
   else 
   {
   	$_obj_MySQL = MySQL::_init();
   	$_obj_MySQL->commitData();
   	echo 'OK';
   }
   
   
   
?>