<?php
	class AXION_UTIL_RSS_ITEM
	{
		/**
		 * 标题
		 * @desc 必须
		 * @var string
		 */
		public $title;
		
		/**
		 * 链接地址
		 * @desc 必须
		 * @var link
		 */
		public $link;
		
		/**
		 * 描述信息
		 * @desc 必须
		 * @var string
		 */
		public $description;
		
		/**
		 * 作者
		 * @var string
		 */
		public $author;
		
		/**
		 * 分类
		 * @var string
		 */
		public $category;
		
		/**
		 * 作者
		 * @var string
		 */
		public $comments;
		
		/**
		 * 允许项目连接到有关此项目的注释（文件）。
		 * @var array
		 */
		public $enclosure;
		
		/**
		 * 为项目定义一个唯一的标识符。
		 * @var string
		 */
		public $quid;
		
		/**
		 * 定义此项目的最后更新时间。
		 * @var string
		 */
		public $pubDate;
		
		/**
		 * 为此项目指定一个第三方来源。
		 * @var string
		 */
		public $source;
		
		public function __construct( $str_title, $str_link, $str_desc )
		{
			$this->title			= $str_title;
			$this->link				= $str_link;
			$this->description	= $str_desc;
		}//end function __construct
		
		public function __set( $str_key, $_void_value )
		{
			$this->$str_key = $_void_value;
			return true;
		}//end function __se
		
		public function setEnclosure( $str_url, $int_length, $str_type )
		{
			$this->enclosure = array( 'url' => $str_url );
			if( !empty( $int_length ) )
				$this->enclosure['length']	= $int_length;
			if( !empty( $str_type ) )
				$this->enclosure['type']	= $str_type;
				
			return true;
		}//end function setEnclosure
	}//end AXION_UTIL_RSS_ITEM
?>