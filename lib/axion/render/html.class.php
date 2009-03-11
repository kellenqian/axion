<?php
class AXION_RENDER_HTML implements AXION_INTERFACE_RENDER {
	private $ctrlInstance;
	private $templatePath;
	private $context;
	
	/**
	 * 模板引擎实例
	 *
	 * @var smarty
	 */
	private $templateInstance;
	
	public function __construct($controllerInstance) {
		$this->ctrlInstance = $controllerInstance;
		$this->context = $this->ctrlInstance->getContext ();
		$this->templateEngineInit ();
	}
	
	public function fetch() {
		$path = get_class ( $this->ctrlInstance );
		$file = $this->parseTemplatePath ( $path );
		
		if(!file_exists(APP_TEMPLATE_PATH . DS .$file)){
			throw new AXION_EXCEPTION('没有找到模板文件');
		}
		
		foreach ( $this->context as $k => $v ) {
			$this->templateInstance->assign ( $k, $v );
		}
		
		return $this->templateInstance->fetch ( $file );
	}
	
	private function templateEngineInit() {
		require_once 'html' . DS . 'smarty.class.php';
		$smarty = new Smarty ( );
		
		$smarty->compile_dir = VIEW_CACHE_PATH;
		$smarty->template_dir = APP_TEMPLATE_PATH;
		
		$this->templateInstance = $smarty;
	}
	
	private function parseTemplatePath($path) {
		$suffix = AXION_CONFIG::GET ( 'axion.view.templatesuffix' );
		$path = explode ( '_', $path );
		$file = array_pop ( $path );
		
		$path = implode ( DS, $path );
		
		$templatePath = strtolower ( $path . DS . $file . '.' . $suffix );
		
		return $templatePath;
	}
}
?>