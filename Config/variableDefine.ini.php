<?php
	/**
	 * @desc 常量定义文件
	 * @version v0.01
	 * @author [Alone] [Nice Wang] 〖alonedistian@gmail.com〗
	 * Date 2007-11-27
	 */
	if( !defined( "INT_BOOL_TRUE" ) )
		/**
		 * 数字真值
		 */
		define( "INT_BOOL_TRUE" , 1 );
		
	if( !defined( "INT_BOOL_FALSE" ) ) 
		/**
		 * 数字假值
		 */
		define( "INT_BOOL_FALSE" , 0 );
		
	if( !defined( "INT_TAG_ZERO" ) ) 
		/**
		 * 数字空值
		 */
		define( "INT_TAG_ZERO" , 0 );
		
	if( !defined( 'STR_TAG_EMPTY' ) )
		/**
		 * 字符空值
		 */
		define( 'STR_TAG_EMPTY' , '' );
		
	if( !defined( 'STR_TAG_ENTER' ) )
		/**
		 * 换行符
		 */
		define( 'STR_TAG_ENTER' , "<br />\n" );
	
	if( !defined( 'REG_MAIL' ) )
		/**
		 * 正则表达式：邮件地址
		 */
		define( 'REG_MAIL' , "/\w+([-+.]\w+)*@\w+([-.]\w+)+/i" );
		
	if( !defined( 'REG_PASSWORD' ) )
		/**
		 * 正则表达式：站内密码
		 */
		define( 'REG_PASSWORD' , "/\w{32}/i" );
		
	if( !defined( 'REG_IP' ) )
		/**
		 * 正则表达式：IP地址
		 *
		 */
		define( 'REG_IP' , "/^[0-9]{1,3}(\.[0-9]{1,3}){3}$/" );
		
	if( !defined( 'REG_DOUBLECODE' ) )
		/**
		 * 正则表达式：非英文字符
		 */
		define( 'REG_DOUBLECODE' , "/[^\x00-\x7f]/i" );
		
	if( !defined( 'REG_BASICENSTRING' ) )
		/**
		 * 正则表达式：基本英文字串
		 */
		define( 'REG_BASICENSTRING' , "/[a-z][a-z0-9_\.]*/i" );
		
	if( !defined( 'REG_PRJPATHDESC' ) )
		/**
		 * 正则表达式：项目部署路径描述字串格式
		 */
		define( 'REG_PRJPATHDESC' , "/^(\([1-9][0-9]*\)\[[1-9][0-9]*\])(,\([1-9][0-9]*\)\[[1-9][0-9]*\])*$/i" );
	
	/**
	 * Finish
	 * o._.o
	 */
?>