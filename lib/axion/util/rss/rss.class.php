<?php
	class AXION_UTIL_RSS_RSS extends AXION_UTIL_XML
	{
		/**
		 * 频道标题
		 * @desc  必须
		 * @var string
		 */
		protected $title;
		
		/**
		 * 频道链接地址
		 * @desc  必须
		 * @var string
		 */
		protected $link;
		
		/**
		 * 频道说明信息
		 * @desc  必须
		 * @var string
		 */
		protected $description;
		
		/**
		 * 语言
		 * @var string
		 */
		protected $language;
		
		/**
		 * 频道分类
		 * @var string
		 */
		protected $category;
		
		/**
		 * 版权声明
		 * @var string
		 */
		protected $copyright;
		
		/**
		 * 频道最后更新时间
		 * @var string
		 */
		protected $pubDate;
		
		/**
		 * 管理员电子邮件地址
		 * @var string
		 */
		protected $webMaster;
		
		/**
		 * 频道图片
		 * @var array
		 */
		protected $image;
		
		/**
		 * 频道内容列表
		 * @var array
		 */
		protected $arr_items;
		
		function __construct( $str_title, $str_link, $str_desc )
		{
			$this->title			= $str_title;
			$this->link				= $str_link;
			$this->description	= $str_desc;
		}//end function __construct
		
		function __set( $str_key, $void_value )
		{
			$this->$str_key = $void_value;
		}//end function __set
		
		public function newImg( $str_url, $str_title = '', $str_link = '' )
		{
			$_arr_image	= array( 'url' => $str_url );
			if( !empty( $str_title ) )
				$_arr_image['title']	= $str_title;
			if( !empty( $str_link ) )
				$_arr_image['link']	= $str_link;
				
			$this->arr_image = $_arr_image;
		}//end function newImg
		
		public function newItem( AXION_UTIL_RSS_ITEM $obj_item )
		{
			$_arr_paras = get_object_vars( $obj_item );
			foreach ( $_arr_paras as $_void_key => $_void_value ) 
			{
				if( empty( $_void_value ) )
					unset( $_arr_paras[$_void_key] );
			}//end foreach
			$this->arr_items[] = $_arr_paras;
		}//end function newItem
		
		/**
		 * 输出根据“数据结构数组”解析后的XML文档结构
		 *
		 * @param string $str_xsltPath 解析用XSLT文档路径
		 * @param string $str_aimFileName 如果输出模式为“FILE”则该参数指定存储文件地址，如果该文件已经存在则覆盖该文件。
		 * @return boolean
		 */
		function getRssDocument( $_str_version = '2.0' )
		{
			$this->setChildTag( 'item' );
			$this->arr_docs['channel']							= $this->arr_items;
			$this->arr_docs['channel']['title']				= $this->title;
			$this->arr_docs['channel']['link']				= $this->link;
			$this->arr_docs['channel']['description']		= $this->description;
			if( !empty( $this->language ) )
				$this->arr_docs['channel']['language']		= $this->language;
			if( !empty( $this->image ) )
				$this->arr_docs['channel']['image']			= $this->image;
			if( !empty( $this->category ) )
				$this->arr_docs['channel']['category']		= $this->category;
			if( !empty( $this->copyright ) )
				$this->arr_docs['channel']['copyright']	= $this->copyright;
			if( !empty( $this->pubDate ) )
				$this->arr_docs['channel']['pubDate']		= $this->pubDate;
			if( !empty( $this->webMaster ) )
				$this->arr_docs['channel']['webMaster']	= $this->webMaster;
			
			$_str_body = $this->parseElement( $this->arr_docs );
			//设置XML文档字符集
			$_str_xml = "<?xml version='1.0' encoding='utf-8' ?>";
			
			//获取XML内容
			$_str_xml .= "\n<rss version='{$_str_version}'>";
			$_str_xml .= str_replace( '&', '&amp;',  $this->parseElement( $this->arr_docs ) );
			$_str_xml .= "\n</rss>";
							
			//输出XML信息
			header( "Content-Type:text/xml; Charset='utf-8'" );
			echo $_str_xml;
			return true;
		}//end function getXMLDocument
	}//class AXION_UTIL_RSS_RSS
	
	
?>