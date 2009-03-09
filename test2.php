<?php
	require( 'config/variableDefine.ini.php' );
	require( 'lib/axion/util/default.fun.php' );
	require_once( 'lib/axion/processtatus.class.php' );
	require_once( 'lib/axion/orm/datamap.class.php' );
	require_once( 'lib/axion/orm/datasearch.class.php' );
	require_once( 'lib/axion/orm/dataschema.class.php' );
	require_once( 'lib/axion/orm/htmldoc.class.php' );
	require_once( 'lib/axion/db/database.class.php' );
	require_once( 'lib/axion/db/mysql.class.php' );
	
	class t_uchk extends Axion_orm_DataMap 
	{
		function initSelf()
		{
			$this->arr_bill['uchk_ID'] = array( 'type' => Axion_orm_DataMap::TYPE_STRING, 'name' => '创建人编号', 'vType' => 'hidden', 'action' => 'a' );
			$this->arr_bill['uchk_Name'] = array( 'type' => Axion_orm_DataMap::TYPE_STRING, 'name' => '登录名', 'maxLength' => 4, 'unique' => true, 'action' => 'a' );
			$this->arr_bill['uchk_Mail'] = array( 'type' => Axion_orm_DataMap::TYPE_MAIL, 'name' => '注册邮箱', 'length' => 96, 'unique' => true, 'action' => 'a' );
			$this->arr_bill['uchk_PS'] = array( 'type' => Axion_orm_DataMap::TYPE_STRING, 'name' => '密码', 'length' => 32, 'vType' => 'password', 'action' => 'a' );
			$this->arr_bill['uchk_Code'] = array( 'defValue' => '', 'type' => Axion_orm_DataMap::TYPE_STRING, 'name' => '校验码', 'action' => 'a' );
			$this->arr_bill['uchk_IP'] = array( 'defValue' => '', 'type' => Axion_orm_DataMap::TYPE_STRING, 'name' => 'IP地址', 'minLength' => 15, 'maxLength' => 15 , 'action' => 'i' );
			$this->arr_bill['uchk_Time'] = array( 'defValue' => 'CURRENT_TIMESTAMP', 'isExpression' => true, 'type' => Axion_orm_DataMap::TYPE_STRING, 'name' => '注册时间', 'isNull' => true, );
			$this->arr_bill['role_ID'] = array( 'defValue' => '1', 'defValue' => '1', 'type' => Axion_orm_DataMap::TYPE_INT, 'name' => '身份标识', 'isNull' => false, 'vType' => 'select', 'action' => 'a' );
			$this->arr_bill['uchk_Disabled'] = array( 'defValue' => 0, 'maxLength' => 10, 'type' => Axion_orm_DataMap::TYPE_INT, 'name' => '禁用标识', 'vType' => 'select', 'action' => 'a' );
			$this->arr_bill['uchk_Disabled']['enum'] = array( array( 'name' => '开启', 'value' => '1' ), array( 'name' => '关闭', 'value' => '0' ) );
			
			parent::initSelf();
		}//end function initSelf
	}//class t_uchk
	
	class uchk extends Axion_orm_DataSearch 
	{
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
//	$obj = new uchk();
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
   $_obj_uchk->setBill( 'uchk_Name', 'style', 'border:solid 1px blue' );
   $_obj_uchk->setBill( 'uchk_Name', 'class', 'test' );
//   $_arr_name = $_obj_uchk->getBill( 'uchk_Name' );
//   $_arr_name['style'] = 'border:solid 1px red;';
//   $_obj_uchk->uchk_Name = $_arr_name;
   
   $_obj_htmlDoc = Axion_orm_HTMLDoc::_init( $_obj_uchk );
   $_arr_reuslt = $_obj_htmlDoc->getForm();
   foreach ( $_arr_reuslt as $_arr_dom ) 
   {
   	echo $_arr_dom['name'] . ':'. $_arr_dom['html'] . '<br/>';
   }//end foreach
//   print_r( $_obj_htmlDoc->getForm() );

//	$obj = new uchk();
//	$obj_dataMap = $obj->top;
//	$obj_dataMap[0]->uchk_Name='HI';
//	$obj_dataMap[0]->uchk_IP = '123456789012345';
//	$obj_dataMap[0]->uchk_Mail = 'ls@ljs.dd';
//	$_str_sql = $obj_dataMap[0]->insertData();
//	if( !$_str_sql )
//	{
//		$obj_processStatus = ProcessStatus::_init();	
//		print_r( $obj_processStatus->getAllData() );
//		exit;
//	}
//	else 
//		echo $_str_sql;
//	$obj_MySQL = MySQL::_init();
//	print_r( $obj_MySQL->getTablesInfo() );
   
?>