<?php
class AXION_RENDER_HTML implements AXION_INTERFACE_RENDER {
	private $templatePath;
	private $context;
	private $controller;
	/**
	 * 模板引擎实例
	 *
	 * @var smarty
	 */
	private $templateInstance;
	
	public function __construct() {
		$this->_templateEngineInit ();
	}
	
	public function addController($controller) {
		$this->controller = $controller;
	}
	
	public function render() {
		$this->_getContext ( $this->controller );
		
		$path = get_class ( $this->controller );
		$file = $this->_parseTemplatePath ( $path );
		
		if (! file_exists ( APP_TEMPLATE_PATH . DS . $file )) {
			throw new AXION_EXCEPTION ( '没有找到模板文件' );
		}
		
		foreach ( $this->context as $k => $v ) {
			$this->templateInstance->assign ( $k, $v );
		}
		
		$_str_resultHTML = $this->templateInstance->fetch ( $file );
		
		return $_str_resultHTML;
	}
	
	private function _getContext($controller) {
		$this->context = $controller->getContext ();
	}
	
	private function _templateEngineInit() {
		require_once 'html' . DS . 'smarty.class.php';
		$smarty = new Smarty ( );
		
		$smarty->compile_dir = VIEW_CACHE_PATH;
		$smarty->template_dir = APP_TEMPLATE_PATH;
		
		$this->templateInstance = $smarty;
	}
	
	private function _parseTemplatePath($path) {
		$suffix = AXION_CONFIG::GET ( 'axion.view.templatesuffix' );
		$path = explode ( '_', $path );
		$file = array_pop ( $path );
		
		$path = implode ( DS, $path );
		
		$templatePath = strtolower ( $path . DS . $file . '.' . $suffix );
		
		return $templatePath;
	}
}
?>