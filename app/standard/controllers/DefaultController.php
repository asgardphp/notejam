<?php
namespace Coxis\App\Standard\Controllers;

class DefaultController extends \Coxis\Core\Controller {
	/**
	@Route('')
	*/
	public function indexAction($request) {
	}

	public function _404Action() {
	}
	
	public function layoutAction($content) {
		$this->content = $content;
	}
}