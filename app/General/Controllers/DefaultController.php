<?php
namespace General\Controllers;

class DefaultController extends \Asgard\Http\Controller {
	public $user;
	public $fragments;
	
	/**
	 * @Route("")
	 */
	public function indexAction(\Asgard\Http\Request $request) {
		if(!$this->user)
			return $this->response->redirect($this->url(['Notejam\Controllers\UserController', 'signin']));
	}

	public function _404Action() {
	}

	public function maintenanceAction() {
		$this->response->setCode(503);
	}

	public static function layout(\Asgard\Http\Controller $controller, $content) {
		$user = isset($controller->user) && $controller->user ? $controller->user:null;
		return \Asgard\Templating\PHPTemplate::renderFile(dirname(__DIR__).'/html/default/layout.php', [
			'controller' => $controller,
			'user' => $user,
			'content'=>$content
		]);
	}
}