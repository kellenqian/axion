<?php
/**
 * AXION工具类-获取并分析useragent
 * 
 * @package AXION
 * @author kellenqian
 * @copyright techua.com
 *
 */

class AXION_UTIL_USERAGENT {
	private $origUserAgent;
	
	private $userAgent;
	
	private $platform;
	
	private $browser;
	
	private $is_spider;
	
	private $platformFlags;
	
	private $browserFlags;
	
	private $botFlags;
	
	public function __construct($userAgent = false) {
		if ($userAgent) {
			$this->userAgent = $userAgent;
		} else {
			$this->userAgent = $_SERVER ['HTTP_USER_AGENT'];
		}
		
		$this->origUserAgent = $this->userAgent;
		
		$this->userAgent = strtolower ( $this->userAgent );
		
		$this->init_data ();
	}
	
	public function analyze() {
		preg_match ( '/\((.+)\)/U', $this->userAgent, $match );
		
		if ($match) {
			$flag = $match [1];
			foreach ( $this->platformFlags as $k => $v ) {
				if (strstr ( $flag, $v )) {
					$platform = $k;
				}
			}
		} else {
			$platform = NULL;
		}
		
		$browser = NULL;
		foreach ( $this->browserFlags as $k => $v ) {
			if (strstr ( $this->userAgent, $v )) {
				$browser = $k;
			}
		}
		
		//ugly patch for safari || chrome
		if ($browser == 'safari') {
			if (strstr ( $this->userAgent, 'chrome' )) {
				$browser = $this->browserFlags ['chrome'];
			}
		}
		//patch end
		
		
		//bots filter start
		
		if (! $platform) {
			$platform = 'unknow';
		}
		
		if (! $browser) {
			$browser = 'unknow';
		}
		
		$this->browser = $browser;
		$this->platform = $platform;
	}
	
	private function init_data() {
		$platforms ['windows'] = 'windows nt';
		$platforms ['macos'] = 'macintosh';
		$platforms ['linux'] = 'linux';
		$platforms ['iphone'] = 'iphone;';
		$platforms ['ipad'] = 'ipad;';
		$platforms ['android'] = 'android';
		
		$browsers ['chrome'] = 'chrome';
		$browsers ['safari'] = 'safari';
		$browsers ['firefox'] = 'firefox';
		$browsers ['opera'] = 'opera';
		$browsers ['ie6'] = 'msie 6';
		$browsers ['ie7'] = 'msie 7';
		$browsers ['ie8'] = 'msie 8';
		$browsers ['ie9'] = 'msie 9';
		
		$bots ['google'] = 'googlebot';
		$bots ['baidu'] = 'baiduspider';
		$bots ['yahoo'] = 'yahoo! slurp';
		$bots ['iask'] = 'iaskspider'; //新浪爱问
		$bots ['sogou'] = 'sogou';
		$bots ['163'] = 'yodaobot'; //网易
		

		$this->platformFlags = $platforms;
		$this->browserFlags = $browsers;
		$this->botFlags = $bots;
	}
}
?>