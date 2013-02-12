<?php
namespace Coxis\Core;

class URL {
	public $request;
	public $server;
	public $root;
	public $url;

	function __construct($request, $server=null, $root=null, $url=null) {
		$this->request = $request;
		$this->server = $server;
		$this->root = $root;
		$this->url = $url;
	}

	public function get() {
		return $this->url;
	}
	
	public function setURL($url) {
		return $this->url = $url;
	}
	
	public function setServer($server) {
		return $this->server = $server;
	}
	
	public function setRoot($root) {
		return $this->root = $root;
	}
	
	public function current() {
		return $this->base().$this->get();
	}
	
	public function full() {
	if($this->request->get->size()) {
		$r = $this->current().'?';
		foreach($this->request->get->all() as $k=>$v)
			$r .= $k.'&'.$v;
		return $r;
	}
	else
		return $this->current();
	}
	
	public function base() {
		$res = $this->server().'/';
		if($this->root())
			$res .= $this->root().'/';
		return $res;
	}
	
	public function to($url) {
		return $this->base().$url;
	}
	
	public function root() {
		$result = $this->root;
		
		$result = str_replace('\\', '/', $result);
		$result = trim($result, '/');
		$result = str_replace('//', '/', $result);
		
		return $result;
	}
	
	public function server() {
		if($this->server !== null)
			return 'http://'.$this->server;
		else
			return '';
	}

	public function url_for($what, $params=array(), $relative=true) {
		#controller/action
		if(is_array($what)) {
			$controller = strtolower($what[0]);
			$action = strtolower($what[1]);
			foreach(\Router::getRoutes() as $route_params) {
				$route = $route_params['route'];
				if(strtolower($route_params['controller']) == $controller && strtolower($route_params['action']) == $action) {
					if($relative)
						return \Router::buildRoute($route, $params);
					else
						return $this->to(\Router::buildRoute($route, $params));
				}
			}
		}
		#route
		else {
			$what = strtolower($what);
			foreach(\Router::getRoutes() as $route_params) {
				$route = $route_params['route'];
				if($route_params['name'] != null && strtolower($route_params['name']) == $what)
					if($relative)
						return \Router::buildRoute($route, $params);
					else
						return $this->to(\Router::buildRoute($route, $params));
			}
		}
					
		throw new \Exception('Route not found.');
	}

	public function startsWith($what) {
		return preg_match('/^'.preg_quote($what, '/').'/', $this->get());
	}
}
