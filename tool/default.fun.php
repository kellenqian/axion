<?
	/**
	 * 通用函数定义文件
	 * @version v1.00
	 * @author [Alone] 〖alonedistian@gmail.com〗
	 * Date 2007-11-30
	 */
	/*********************************************
	☆		  				更新说明							☆
	**********************************************
	☆	v1.00:												☆
	☆	Desc:													☆
	☆		1.更新getGet()函数为获取页面提交的所有		☆
	☆			GET变量；										☆
	☆		2.更新getPost()函数为获取页面提交的所有		☆
	☆			POST变量；									☆
	☆		3.更新getRequest()函数为获取页面提交的所有	☆
	☆			GET与POST变量；								☆
	☆		4.增加函数trimArray()。						☆
	☆	v1.10:												☆
	☆		1.更新函数checkInt();							☆
	☆		2.增加函数checkFloat()。						☆
	☆															☆
	☆	v1.20													☆
	☆		增加函数postRequest()用于通过PHP使用POST	☆
	☆		的方式向目标地址提交数据并获取返回结果			☆
	☆															☆
	☆	v1.21													☆
	☆		增加函数clearPlace()用于清理					☆
	☆		当前GLOBALS变量中的对象实例。					☆
	☆															☆
	***********************************************/ 
	
	/***************************************************
	☆		  				IncludeFunction						☆
	 ***************************************************
	☆	1.encyptCode			自定义MD5加密算法；				☆
	☆	2.checkString			测试变量的值是否为字符串，		☆
	☆								及字符串是否符合标准；			☆
	☆	3.checkInt				检测变量的值是否为数值型变量，	☆
	☆								及数值是否符合要求;				☆
	☆	4.checkFolat			检测变量的值是否为浮点型变量，	☆
	☆								及数值是否符合要求;				☆
	☆	5.getDataModifyType	获取数据修改类型的对应数字描述;	☆
	☆	6.trimArray				去掉指定数组中所有值的前后空白;	☆			
	☆	7.getPost				获取页面提交的所有POST变量，	☆
	☆								并去掉所有值的前后空白;			☆
	☆	8.getGet					获取页面提交的所有GET变量，		☆
	☆								并去掉所有值的前后空白;			☆
	☆	9.getRequest			获取所有页面提交的变量			☆
	☆								（GET & POST）					☆
	☆																	☆
	***************************************************/ 
	
	
	/**
	 * 自定义MD5加密算法
	 *
	 * @param string $str_para	参数字符串
	 * @return string
	 */
	function encyptCode( $str_para )
	{
		//将要加密的字符串转换为数组
		$_arr_paraCode = str_split( strval( $str_para ) );
		//运算种子
		$_arr_seed = array( 65, 108, 111, 110, 101 );
		//系统MD5函数加密运算前的密码明文
		$_str_result = '';
		foreach (  $_arr_paraCode as $_int_key => $_str_code ) 
		{
			$_int_codeAscii = ord( $_str_code );
			if( isset( $_arr_seed[$_int_key ] ) )
				$_int_codeAscii = $_int_codeAscii ^ $_arr_seed[$_int_key%5];
			$_str_result .= sprintf( '%c', $_int_codeAscii );
		}//end foreach
		
		return md5( $_str_result );
	}//end function encyptCode
	
	/**
	 * 测试变量的值是否为字符串，及字符串是否符合标准
	 *
	 * @param string $str_para : 要测试的变量
	 * @param integer $int_maxLength : 限制字符串的最大长度
	 * @param integer $int_minLength ：限制字符串的最小长度
	 * @param string $str_preg ： 限制字符串必须符合的正则表达式
	 * @return boolean 标识给定字符串是否符合要求
	 */
	function checkString( $str_para, $int_maxLength = null, $int_minLength = null, $str_preg = "" )
	{
		if( empty( $str_para ) && $int_minLength != -1 )
			return false;
		
		//测试参数变量类型
		if( !is_string( $str_para ) )
			return false;
		//测试参数的最大长度是否符合要求
		if( !is_null( $int_maxLength ) && strlen( $str_para ) > $int_maxLength )
			return false;
				
		//测试参数的最小长度是否符合要求
		if( !is_null( $int_minLength ) && strlen( $str_para ) < $int_minLength )
			return false;
				
		//测试参数是否符合正则表达式的要求
		if( !empty( $str_preg ) && !preg_match( $str_preg, $str_para ) )
			return false;
		
		return true;
	}//end static function checkString

	/**
	 * 检测变量的值是否为数值型变量，及数值是否符合要求（如 1.0|2.0 等 均被视为整数）
	 *
	 * @param integer $int_para ： 要测试的变量
	 * @param integer $int_max ： 限制数值的最大值
	 * @param integer $int_min ： 限制数值的最小值
	 * @return boolean ： 标识给定数值是否符合要求
	 */
	function checkInt( $int_para, $int_max = null, $int_min = null )
	{
		//测试变量类型是否符合要求			
		if( strlen( $int_para ) != strlen( intval( $int_para ) ) )
			return false;
		
		if( floor( $int_para ) != $int_para )
			return false;
			
		//测试变量大小是否符合要求
		if( !is_null( $int_max ) && $int_para > $int_max )
			return false;
		
		if( !is_null( $int_min ) && $int_para < $int_min )
				return false;
		
		return true;
	}//end static function checkInt	

	function checkFloat( $int_para, $int_max = null, $int_min = null )
	{
		//测试变量类型是否符合要求			
		if( strlen( $int_para ) != strlen( sprintf( '%.2f', $int_para ) ) )
		{
			return false;
		}
		
		//测试变量大小是否符合要求
		if( !is_null( $int_max ) && $int_para > $int_max )
			return false;
		
		if( !is_null( $int_min ) && $int_para < $int_min )
				return false;
		
		return true;
	}//end function checkFloat

	/**
	 * 去掉指定数组中所有值的前后空白，支持字符串及多级数组
	 *
	 * @param string|array $arr_paras
	 * @param boolean $bool_htmlConver
	 * @return array
	 */
	function trimArray( $arr_paras, $bool_htmlConver = true )
	{
		if( !is_array( $arr_paras ) )
			return trim( $arr_paras );
			
		foreach (  $arr_paras as $_str_key => $_void_value ) 
		{
			if( is_array( $_void_value ) )
				$arr_paras[$_str_key] = trimArray( $_void_value );
			else
			{
				$arr_paras[$_str_key] = trim( $_void_value );
				if( $bool_htmlConver )
					$arr_paras[$_str_key] = htmlspecialchars( $arr_paras[$_str_key] );
			}
		}//end foreach
		
		return $arr_paras;
	}//function trimArray()
	
	/**
	 * 获取页面提交的所有POST变量，并去掉所有值的前后空白
	 *
	 * @return array
	 */
	function getPost()
	{
		$_arr_postInfo = $_POST;
		return trimArray( $_arr_postInfo );
	}//end function getPost
	
	/**
	 * 获取页面提交的所有GET变量，并去掉所有值的前后空白
	 *
	 * @return unknown
	 */
	function getGet()
	{
		$_arr_getInfo = $_GET;
		return trimArray( $_arr_getInfo );
	}//end function getGet
	
	/**
	 * 获取所有页面提交的变量（GET & POST），如果GET方式与POST方式发送的变量重复，则以POST方式发送的值为准
	 *
	 * @return array
	 */
	function getRequest()
	{
		$_arr_requestInfo = getPost();
		return  array_merge( getGet(), $_arr_requestInfo );
	}//end function getRequest

	/**
	 * 通过POST方式发送数据到指定的位置并接受返回值
	 *
	 * @param string $str_aimAddress : 目标地址
	 * @param array $arr_paras : POST参数键值对
	 * @return string
	 */
	function postReq( $str_aimAddress, $arr_paras )
	{
		$_str_para = '';
		$_str_header = '';
		$_str_result = '';
		$_arr_aimAddress = parse_url( $str_aimAddress );
		
		if( !empty( $arr_paras ) )
		{
			foreach ( $arr_paras as $_str_key => $_str_value ) 
				$_str_para .= "{$_str_key}=".urlencode( $_str_value )."&";
			$_str_para = substr( $_str_para, 0, -1 );
		}//if
		
		$_str_header .= "POST {$_arr_aimAddress['path']} HTTP/1.1\r\n"; 
		$_str_header .= "Host:{$_arr_aimAddress['host']}\r\n";
		$_str_header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$_str_header .= "Content-Length:".strlen( $_str_para )."\r\n";
		$_str_header .= "Connection: Close\r\n\r\n";
		$_str_header .= "{$_str_para}\r\n";

		unset( $_str_para );
		
		$_hl_socket = fsockopen( "{$str_aimAddress}", $_arr_aimAddress['port'], $_str_errorNo, $_str_errorStr );
		if( !$_hl_socket )
			return false;
		
		fputs( $_hl_socket, $_str_header );
		unset( $_str_header );
		unset( $_arr_aimAddress );
		
		$_bool_inheader = 0;
		 while ( !feof( $_hl_socket ) ) 
		 {
	          $_str_line = fgets( $_hl_socket ); //去除请求包的头只显示页面的返回数据 
	          if ( !$_bool_inheader && ( $_str_line == "\n" || $_str_line == "\r\n" ) ) 
	          {
	              $_bool_inheader = 1; 
	              continue;
	          }//if
	          if( $_bool_inheader )
	          	$_str_result .= $_str_line;
	     }//while
		fclose( $_hl_socket );
		
		return $_str_result;
	}//end function sendReq
	
	/**
	 * Finish
	 * o._.o
	 */
	
	/*********************************************************************************************
	echo postReq( '192.168.1.102:8080/bbs/test/test2.php', array('var2'=>'你好吗') );
	*********************************************************************************************/
?>