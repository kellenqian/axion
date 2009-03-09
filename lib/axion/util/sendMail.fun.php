<?php
	/**
	 * 邮件发送处理类定义。
	 * @param $str_smtp					SMTP服务器地址
	 * @param $int_port					STMP服务器端口
	 * @param $str_name					用户名
	 * @param $str_ps					密码
	 * @param $str_aimAddress			收件人地址
	 * @param $str_title				邮件标题
	 * @param $str_doc					邮件正文
	 * @param $str_cc					抄送地址
	 * 
	 * @author [Alone] alonedistian@gmail.com〗
	 * @package PHPDoc
	 */
	function sendMail( $str_smtp, $int_port, $str_name, $str_ps, $str_aimAddress, $str_title, $str_doc, $str_cc = '' )
	{
		$_str_result = '';							//邮件发送请求响应结果
		$_arr_mailContent = array();				//SMTP邮件发送内容
		$_arr_ccs = '';								//抄送地址
		
		//连接SMTP服务器
		$_obj_socket = fsockopen( $str_smtp, $int_port, $errno, $errstr, 60 );
		if( !$_obj_socket ) 
			exit( "stmp server connect error! errNo:{$errno};  errMsg:{$errstr}" );
		else 
			$_str_result .= 'stmp connect info:'.fgets( $_obj_socket, 512 )."<br/>";
		
		if( !empty( $str_cc ) )
			$_arr_ccs = explode( ',', $str_cc );
		
		/**
		 * 整理STMP请求串
		 */
		$_arr_mailContent[] = "HELO Alone\r\n";			//SMTP头
		$_arr_mailContent[] = "AUTH LOGIN\r\n";			//SMTP头
		$_arr_mailContent[] = base64_encode( $str_name )."\r\n";	//账号
		$_arr_mailContent[] = base64_encode( $str_ps )."\r\n";		//密码
		$_arr_mailContent[] = "MAIL FROM:<{$str_name}>\r\n";		//发送人
		$_arr_mailContent[] = "RCPT TO:<{$str_aimAddress}>\r\n";	//收件地址
		
		//抄送地址
		if( !empty( $_arr_ccs ) )
		{
			foreach ( $_arr_ccs as $_str_thisAimAddress )
			{
				$_str_thisAimAddress = trim( $_str_thisAimAddress );
				$_arr_mailContent[] = "RCPT TO:<{$_str_thisAimAddress}>\r\n";
			}//end foreach
		}//if
		
		//正文
		$_arr_mailContent[] = "DATA\r\n".
								"MIME-Version: 1.0\r\n".
								"Content-Type: text/html\r\n".
								"To: {$str_aimAddress}\r\n".
								( empty( $str_cc ) ? '' : "Cc:{$str_cc}\r\n" ).
								"From: {$str_name}\r\n".
								"Subject: {$str_title}\r\n".
								"X-Mailer:By   Redhat   (PHP/".phpversion().")\r\n".
								"Message-ID:   <".md5( time() )."{$str_aimAddress}>\r\n".
								"\r\n{$str_doc}\r\n";   //MAIL CONTENT
		$_arr_mailContent[] = ".\r\n";
		$_arr_mailContent[] = "QUIT\r\n";
		
		/**
		 * 逐条发送请求并记录响应结果
		 */
		foreach ( $_arr_mailContent as $_str_mailContent )
		{
			fputs( $_obj_socket, $_str_mailContent );
			$_str_result .=  "<b>{$_str_mailContent}</b>:";
			$_str_result .= fgets( $_obj_socket, 512 )."<br/>";
		}//end foreach
		
		//关闭服务器连接
		fclose( $_obj_socket );
		
		return $_str_result;
	}//end function sendMail
  
// echo sendMail( 'ssl://smtp.gmail.com', 465, 'alonedistian', 'gm3zyy2l1', 'alonedistian@gmail.com', 'testMail', '<h1>Hello MySelf</h1>', 'distian.alone@gmail.com, distian@eyou.com' );

	
	/**
	 * Finish
	 * o._.o
	 */
?>