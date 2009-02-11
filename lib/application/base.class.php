<?php
abstract class Base {
	/**
	 * 框架版本号
	 *
	 * @var float
	 */
	protected $version = '0.01';
	
	/**
	 * 加载组件方法
	 *
	 * 如果要加载框架内部组件，使用"AXION开头"
	 * "_"用来连接目录，如CONTROLLER_EXAMPLE_FOLDER1_INDEX,
	 * 对应的目录为controller/example/folder1/index.class.php
	 * @param string $str_classFullPath 文件地址
	 * @param string $str_suffix 文件后缀
	 * @example load('axion_application_base')
	 * @example load('axion_controller');
	 * @example load('controller_index');
	 * @return bool
	 */
	protected function load($str_classFullPath,$str_suffix = '.class.php') {
		$_str_classNameTolower = strtolower ( $str_classFullPath );
		
		$_arr_classPath = explode ( '_', $_str_classNameTolower );
		
		if ($_arr_classPath [0] == 'axion') {
			$_str_source = 'AXION';
			array_shift ( $_arr_classPath );
		} else {
			$_str_source = 'APP';
		}

		$_str_Pathname = array_shift ( $_arr_classPath );
		$_str_fileName = !empty($_arr_classPath) ? array_pop($_arr_classPath).$str_suffix : $_str_Pathname.$str_suffix;
		$_str_classPath = implode ( DS, $_arr_classPath );
		
		$_str_loadFilePath = constant ( $_str_source . '_' . strtoupper ( $_str_Pathname ) . '_PATH' ) . DS . $_str_classPath . DS . $_str_fileName;
		
		$_str_loadFilePath = str_replace(DS.DS,DS,$_str_loadFilePath);
		
		if (file_exists ( $_str_loadFilePath )) {
			require_once $_str_loadFilePath;
			return true;
		} else {
			return false;
		}
	}
	
	protected function version() {
		return $this->version;
	}
}
?>