<?php
	/**
	 * REQUEST处理结果缓存类
	 * @desc 用于将请求结果进行文件缓存
	 * @author[Alone]alonedistian@gmail.com〗
	 * @version 0.1
	 * @package PHPDoc
	 */
	class AXION_CACHE_APP extends AXION_CACHE_FILE
	{
		/**
		 * 单件模式对象，该对象为AXION_CACHE_APP本身
		 * @var AXION_CACHE_APP
		 */
		protected static $obj_self;

		/**
		 * 缓存超时时间
		 * @var integer
		 */
		protected $int_timeout;
		
		/**
		 * 构造函数用于设置CACHE的运行环境
		 *
		 * @return true
		 */
		public function __construct()
		{
			$this->config = AXION_CONFIG::get( 'axion.cache' );
			$this->config['path'] = 'd:/temp';
			$this->int_timeout	= 30000;												//设置缓存时间
			return true;
		}//end function __construct
		
		/**
		 * 单件模式实例化函数
		 * @return AXION_CACHE_APP
		 */
		public static function _init()
		{
			if( !AXION_CACHE_APP::$obj_self )
				AXION_CACHE_APP::$obj_self = new AXION_CACHE_APP();
				
			return AXION_CACHE_APP::$obj_self;
		}//end function _init
		
		/**
		 * 生成缓存文件
		 *
		 * @param string $str_key				缓存关键字
		 * @param array $arr_result			缓存内容
		 * @param integer $int_timeout		超时时间
		 * @return boolean
		 */
		public static function save( $str_key, $arr_result, $int_timeout = null )
		{
			$_obj_appCache = AXION_CACHE_APP::_init();
			$_int_timeout = $int_timeout ? $int_timeout : $_obj_appCache->int_timeout;
			if( !$_obj_appCache->set( $str_key, $arr_result, $_int_timeout ) )
				return false;
			return true;
		}//end function save
		
		/**
		 * 获取缓存内容
		 *
		 * @param string $str_key		缓存关键字
		 * @return array
		 */
		public static function load( $str_key )
		{
			$_obj_appCache = AXION_CACHE_APP::_init();
			return $_obj_appCache->get( $str_key );
		}//end function load
		
		public static function remove( $str_key, $str_path = null )
		{
			$_obj_appCache	= AXION_CACHE_APP::_init();
			$_str_savePath	= is_null( $str_path ) ? AXION_CONFIG::get( 'axion.cache' ) : $str_path;
			$_str_filePath	= $_obj_appCache->genFileName( $str_key );
			if( file_exists( $_str_filePath ) )
				unlink( $_str_filePath );
			return true;
		}//end function remove
		
		/**
		 * 设置缓存文件存储位置
		 *
		 * @param string $str_path
		 * @return boolean
		 */
		public static function setPath( $str_path )
		{
			if( !is_dir( $str_path ) )
			{
				Axion_log::_init()->newMessage( '错误的存储路径', Axion_log::EXCEPTION );
				return false;
			}
			$this->config['path'] = $str_path;
			return true;
		}//end function setPath
	}//AXION_CACHE_APP
?>