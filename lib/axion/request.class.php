<?php
class AXION_REQUEST {
	/**
	 * 获取当前请求响应数据格式
	 *
	 * @return string
	 */
	public static function getResponseFormat() {
		$response = RENDER_HTML;
		
		if (isset ( $_SERVER ['X-AXION-REQUEST-FORMAT'] )) {
			$response = $_SERVER ['X-AXION-REQUEST-FORMAT'];
		}
		
		return $response;
	}
	
	/**
	 * 获取当前请求的HTTP方法
	 *
	 * @return mix
	 */
	public static function getRequestMethod() {
		static $_method = array ('GET', 'PUT', 'POST', 'DELETE' );
		
		if (isset ( $_POST ['_method'] )) {
			$postMethod = strtoupper ( $_POST ['_method'] );
		} else {
			$postMethod = '';
		}
		
		if (! IS_CLI) {
			$headMethod = strtoupper ( $_SERVER ['REQUEST_METHOD'] );
		}else{
			$headMethod = 'GET';//@todo 在CLI模式下是否需要使用一个独立的标志，而不是使用GET
		}
		
		if (! in_array ( $headMethod, $_method )) {
			return 'GET';
		}
		
		if ($postMethod == '') {
			return $headMethod;
		}
		
		if ($postMethod != 'GET' && $headMethod == 'GET') {
			return false;
		}
		
		if ($postMethod == 'POST' && $headMethod != 'GET') {
			return $headMethod;
		}
	}
}
?>