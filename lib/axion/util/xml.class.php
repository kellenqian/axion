<?php
	/**
	 * 简单XML处理类
	 * @desc 
	 * @author [Alone] alonedistian@gmail.com〗& Nick Wang [ interlover0@gmail.com ]
	 * @package PHPDoc
	 */
	
	/*********************************************
	☆		  				更新说明				☆
	**********************************************
	☆	v1.20									☆
	☆	增加数据列说明	 						☆
	☆		name 字段中文名称		value 值			☆
	☆		isExpression 表达式	type 字段类型		☆
	☆		length 字段长度		isNull 空标识	☆
	☆		preg 正则表达式		unique 符号位	☆
	☆	取消getBill方法							☆
	☆	增加自动验证								☆
	☆	区分INSERT与UPDATE的验证方式				☆
	☆	增加SELECT语句快速实现函数					☆
	☆	重新定义函数deleteData使其允许级联删除相关表	☆
	☆	删除函数getBill的定义						☆
	☆	删除函数getDeleteSQL的定义				☆
	☆	将insertData函数的返回值更改为getLastInsertID☆
	☆											☆
	☆	v1.12：									☆
	☆		去除标识 int_extLv	及相关的操作		☆
	☆											☆
	☆	v2.0:									☆
	☆		去除原有用于调试的信息记录操作			☆
	☆		将原有的输出模式INT_OUTMODULE_XML变更为	☆
	☆		直接返回XML结构字符串且不输出到浏览器	☆
	***********************************************/ 	
	
	class Axion_util_xml
	{
		/**
		 * 输出模式定义：该模式输出带有XSLT解析标志的XML文档到浏览器
		 * @var integer
		 */
		public static $INT_OUTMODULE_HTML = 1;
		
		/**
		 * 输出模式定义：该模式返回XML结构字符串
		 * @var integer
		 */
		public static $INT_OUTMODULE_XML = 2;
				
		/**
		 * 输出模式定义：该模式将XML内容输出到指定的文件地址
		 * @var integer
		 */
		public static $INT_OUTMODULE_FILE = 3;
		
		/**
		 * 数据结构数组 能够与“XML数据结构”进行相互转换
		 * @var array
		 */
		private $arr_docs;
		
		/**
		 * 类输出模式
		 * @var integer
		 */
		private $int_outModule;
		
		/**
		 * 定义输出的XML结构文档根标签名称
		 * @var string
		 */
		private $str_rootTag;
		
		/**
		 * 如果“数据结构数组”中未指明下标值，则使用该值进行XML解析
		 * @var string
		 */
		private $str_childTag;
		
		/**
		 * XML数据结构 能够与“数据结构数组”进行相互转换
		 * @var string
		 */
		private $str_xmlDoc;
		
		/**
		 * XML数据字符集
		 * @var string
		 */
		private $str_charset;
		
		/**
		 * 构造函数
		 *
		 * @param object $obj_log
		 * @return true
		 */
		function __construct( )
		{
			$this->arr_docs = array();
			$this->int_outModule = XMLDocument::$INT_OUTMODULE_HTML;
			$this->str_childTag = 'childs';
			$this->str_rootTag = 'XMLDocument';
			$this->str_xmlDoc = '';
			$this->str_charset = 'utf-8';
			return true;
		}//end function __construct
		
		/**
		 * 设置XML处理(输入/输出)使用字符集
		 * 
		 * @param string $str_charset 字符集名称
		 * @return boolean
		 */
		function setCharset( $str_charset = '' )
		{
			if( empty( $str_charset ) )
				$this->str_charset = 'utf-8';
			else
				$this->str_charset = $str_charset;
			return true;
		}//end function setCharset
		
		/**
		 * 设置XML结构的根节点名称
		 *
		 * @param string $str_tagName 节点名称
		 * @return true
		 */
		function setRootTag( $str_tagName = '' )
		{
			if( empty( $str_tagName ) )
				$this->str_rootTag = 'XMLDocument';
			else
				$this->str_rootTag = $str_tagName;
			return true;
		}//end function setRootTag
		
		/**
		 * 设置XML使用的子节点通用名称(如果节点标识为数字则使用该名称命名)
		 * @desc 
		 * 			如子节点通用名称为 childs
		 * 			则array( 'my name', 'my age' )将被转换为
		 * 			<childs>my name</childs><childs>my age</childs>
		 *
		 * @param string $str_tagName 节点名称
		 * @return true
		 */
		function setChildTag( $str_tagName = '' )
		{
			if( empty( $str_tagName ) )
				$this->str_childTag = 'childs';
			else
				$this->str_childTag = $str_tagName;
			return true;
		}//end function setChildTag
		
		/**
		 * 设置输出模式
		 *
		 * @param integer $int_module 输出模式标识 HTML | XML | FILE
		 * @return true
		 */
		function setOutModule( $int_module )
		{
			$this->int_outModule = $int_module;
			return true;
		}//end function setOutModule

		/**
		 * 验证XML标签名称是否符合标准
		 *
		 * @param string $str_tagName 要验证的标签名称
		 * @return boolean
		 */
		private function checkTagName( $str_tagName )
		{
			if( !checkString( $str_tagName, null, null, '/^[a-z0-9_\-\.]*$/i' ) )
				return false;
				
			return true;
		}//end function checkTagName
		
		/**
		 * 将“数据结构数组”解析为相应的XML文档结构
		 *
		 * @param array $arr_element 要解析的“数据结构数组”
		 * @return string
		 */
		private function parseElement( $arr_element )
		{
			//XML文档内容
			$_str_xmlDoc = '';
			
			foreach ( $arr_element as $_str_key => $_void_value ) 
			{ 
				if( !is_array( $_void_value ) )
				{
					if( is_numeric( $_str_key ) )
						$_str_xmlDoc .= "\n<{$this->str_childTag}>{$_void_value}</{$this->str_childTag}>";
					else 
					{
						if( !$this->checkTagName( $_str_key ) )
							continue;
						$_str_xmlDoc .= "\n<{$_str_key}>{$_void_value}</{$_str_key}>";
					}//else
				}//if
				else 
				{
					if( is_numeric( $_str_key ) )
						$_str_xmlDoc .= "\n<{$this->str_childTag}>".$this->parseElement( $_void_value )."</{$this->str_childTag}>";
					else
					{
						if( !$this->checkTagName( $_str_key ) )
							continue;
						$_str_xmlDoc .= "\n<{$_str_key}>".$this->parseElement( $_void_value )."</{$_str_key}>";
					}//else
				}//else
			}//end foreach
			
			$this->str_xmlDoc = $_str_xmlDoc;
			return $this->str_xmlDoc;
		}//end function parseElement
		
		/**
		 * 输出根据“数据结构数组”解析后的XML文档结构
		 *
		 * @param string $str_xsltPath 解析用XSLT文档路径
		 * @param string $str_aimFileName 如果输出模式为“FILE”则该参数指定存储文件地址，如果该文件已经存在则覆盖该文件。
		 * @return boolean
		 */
		function getXMLDocument( $str_xsltPath = null, $str_aimFileName = null )
		{
			//设置XML文档字符集
			$_str_xml = "<?xml version='1.0' encoding='".$this->str_charset."' ?>";
			//设置要应用XSLT文件地址
			if( $this->int_outModule != XMLDocument::$INT_OUTMODULE_XML )
				$_str_xml .= ( is_null( $str_xsltPath ) ? '' : "\n<?xml-stylesheet type='text/xsl' href='{$str_xsltPath}' ?>" );
			//获取XML内容
			$_str_xml .= "\n<{$this->str_rootTag}>";
			$_str_xml .= $this->parseElement( $this->arr_docs );
			$_str_xml .= "\n</{$this->str_rootTag}>";
			
			/**
			 * 以文件方式存储XML信息
			 */
			if( $this->int_outModule == XMLDocument::$INT_OUTMODULE_FILE )
			{
				$_hl_aimFile = fopen( $str_aimFileName, 'w' );
				if( !$_hl_aimFile )
					return false;
					
				if( !fwrite( $_hl_aimFile, $_str_xml ) )
					return false;
					
				return true;
			}//if
			else if( $this->int_outModule == XMLDocument::$INT_OUTMODULE_XML )
				return $_str_xml;
				
			//输出XML信息
			header( "Content-Type:text/xml; Charset='".$this->str_charset."'" );
			echo $_str_xml;
			return true;
		}//end function getXMLDocument
		
		/**
		 * 向给定的节点路径添加或修改数据结构
		 * @desc 节点路径使用“/”进行分隔
		 *
		 * @param string $str_path 要添加/修改的节点路径
		 * @param viod $void_element 修改内容
		 * @return true
		 */
		function appendElement( $str_path, $void_element, $bool_isOverWrite = false )
		{
			//定义要修改的节点引用
			$_arr_point = &$this->arr_docs;
			//将节点路径拆字符串分为响应数组
			$_arr_paths = explode( '/', $str_path );
			if( empty( $_arr_paths ) )
				return false;
			
			//获取要修改的节点引用
			foreach ( $_arr_paths as $_str_path ) 
			{
				if( empty( $_arr_point ) )
					$_arr_point[$_str_path] = array();
				$_arr_point = &$_arr_point[$_str_path];
			}//end foreach
			
			//修改节点内容
			if( $bool_isOverWrite || empty( $_arr_point ) )
				$_arr_point = $void_element;
			else 
			{
				if( !is_array( $_arr_point ) )
				{
					$_arr_temp = $_arr_point;
					$_arr_point = array();
					$_arr_point[] = $_arr_temp;
					$_arr_point[] = $void_element;
				}//end if
				else 
				{
					$_bool_haveChild = true;
					foreach ( $_arr_point as $_void_key => $_void_value ) 
					{
						if( !is_int( $_void_key ) )
							$_bool_haveChild = false;
					}//end foreach
					
					if( $_bool_haveChild )
						$_arr_point[] = $void_element;
					else 
					{
						$_arr_temp = $_arr_point;
						$_arr_point = array();
						$_arr_point[] = $_arr_temp;
						$_arr_point[] = $void_element;
					}//end else
				}//end else
			}//end else
			return true;
		}//end function appendElement
		
		/**
		 * 获取当前“数据结构数组”
		 *
		 * @return array
		 */
		function getElements()
		{
			return $this->arr_docs;
		}//end function getElements
		
		/**
		 * 将XML文档信息解析为对应的数据结构数组
		 *
		 * @param string $str_xml
		 * @return boolean
		 */
		function paresXML( $str_xml, $str_rootTag = null, $str_childTag = null )
		{
			if( is_null( $str_rootTag ) )
				$str_rootTag = $this->str_rootTag;
			if( is_null( $str_childTag ) )
				$str_childTag = $this->str_childTag;
				
			$_obj_XMLPares = xml_parser_create( $this->str_charset );				//创建XML解析器
			xml_parser_set_option( $_obj_XMLPares, XML_OPTION_CASE_FOLDING, 0 );	//设置XML解析器区分标签大小写
			$_arr_values = array();		//保存XML内容
			$_arr_index = array();		//保存XML结构
			$_arr_xmlStruct = xml_parse_into_struct( $_obj_XMLPares, $str_xml, $_arr_values, $_arr_index );	//将XML解析到数组中
			
			$_arr_result = array();													//解析结果数组
			$_arr_optionLink = &$_arr_result;
			foreach ( $_arr_values as $_void_key => $_arr_value ) 
			{
				switch( $_arr_value['type'] )
				{
					case 'open' :
						$_h_thisLink = &$_arr_optionLink;
						if( $_arr_value['tag'] == $str_childTag )
						{
							$_arr_optionLink[count($_arr_optionLink)] = array( 'optionParentLink' => &$_h_thisLink );
							$_arr_optionLink = &$_arr_optionLink[count($_arr_optionLink)-1];
						}
						else
						{
							$_arr_optionLink[$_arr_value['tag']] = array( 'optionParentLink' => &$_h_thisLink );
							$_arr_optionLink = &$_arr_optionLink[$_arr_value['tag']];
						}
						break;
					case 'close' :
						$_h_thisLink = &$_arr_optionLink['optionParentLink'];
						unset( $_arr_optionLink['optionParentLink'] );
						$_arr_optionLink = &$_h_thisLink;
						break;
					case 'complete' :
						if( $_arr_value['tag'] == $str_childTag )
							$_arr_optionLink[count($_arr_optionLink)-1] = $_arr_value['value'];
						else
							$_arr_optionLink[$_arr_value['tag']] = $_arr_value['value'];
						break;
					default:
						continue;
				}//switch
			}//end foreach
			
			$this->arr_docs = $_arr_result[$this->str_rootTag];
			
			return true;
		}//end function paresXML
	}//class Axion_util_xml
?>