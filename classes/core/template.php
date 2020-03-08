<?php
namespace core;

class template {

	private $html 			    = "";
	private $filePath 		  = "";
	public $restUrl	 	      = array();
	public static $route	 	= array();
	public static $urlPath	= "";
	public static $page	    = array();
	public static $fullURL  = "";


  public static function route($index = null){
    return ($index != null) ? self::$route['routes'][$index] : self::$route['routes'];
  }

  public static function getCurrentUri(){
      self::$fullURL = $_SERVER['REQUEST_URI'];
      $basepath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
      $uri = substr($_SERVER['REQUEST_URI'], strlen($basepath));
      if (strstr($uri, '?')) $uri = substr($uri, 0, strpos($uri, '?'));
      $uri = trim($uri, '/');
      return $uri;
  }

	public static function load() {
    $uri = self::getCurrentUri();
    $routes = array();
    $routes = explode('/', $uri);
    foreach($routes as $route)
    {
        if(trim($route) != '' && !in_array($route, $routes)) {
            array_push($routes, $route);
        }
    }

    $return['routes'] = $routes;
    $return['baseurl'] = $uri;
    self::$urlPath = "/". str_replace("/tibiamate","", $uri);
    self::$route = $return;
  return $return;
	}

	public function loadOLD() {
		$rurl = $_SERVER['REQUEST_URI'];
		$rurl = str_replace(strstr($rurl,"?"),"",$rurl);
		$rurlAs = [];
		$rurlArray = explode("/",$rurl);
		if(!empty($rurlArray)) {
			foreach($rurlArray as $rurlA) {
				if(!empty($rurlA)) {
					$rurlAs[] = $rurlA;
				}
			}
		}

	return $rurlAs;
	}

	public function clearHtml() {
		$this->html = preg_replace('/^[ \t]*[\r\n]+/m', '', $this->html);
	}

	public function html() {
		return $this->html;
	}

	public function getFilePath() {
		return $this->filePath;
	}

	public function filePath($path) {
		$this->filePath = $path;
	}

	public function url_friendly($string) {
		$url = str_replace("ö","o",strtolower($string));
		$url = str_replace("Ö","o",$url);
		$url = str_replace("å","a",$url);
		$url = str_replace("Å","a",$url);
		$url = str_replace("ä","a",$url);
		$url = str_replace("Ä","a",$url);
		$url = preg_replace('/[^a-zA-Z0-9- ]/','',$url);
		$url = str_replace("  "," ",$url);
		$url = str_replace(" ","-",$url);
	return $url;
	}

}
