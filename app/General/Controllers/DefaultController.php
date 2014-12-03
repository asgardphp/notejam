<?php
namespace General\Controllers;

class DefaultController extends \Asgard\Http\Controller {
	/**
	 * @Route("")
	 */
	public function indexAction(\Asgard\Http\Request $request) {
		$this->container['html']->setTitle('');

		if(!($this->user = $request->session->get('user')))
			return $this->response->redirect($this->container['resolver']->url(['Notejam\Controllers\UserController', 'signin']));
	}

	public function _404Action() {
	}

	public function maintenanceAction() {
	}
	
	public static function layout(\Asgard\Http\Controller $controller, $content) {
		$user = $controller->request->session->get('user');
		return \Asgard\Templating\PHPTemplate::renderFile(dirname(__DIR__).'/html/default/layout.php', [
			'controller' => $controller,
			'user' => $user,
			'content'=>$content
		]);
	}
}