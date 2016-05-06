<?php
namespace Notejam\Controller;

class User extends \Asgard\Http\Controller {
	use \AuthTrait;
	public $fragments;

	/**
	 * @Route("signout")
	*/
	public function signoutAction($request) {
		$this->container['auth']->disconnect();
		return $this->response->redirect($this->url(['Notejam\Controller\PublicUser', 'signin']));
	}

	/**
	 * @Route("settings")
	*/
	public function settingsAction($request) {
		$this->container['html']->setTitle('Account Settings');

		$this->form = $this->container->make('form');
		$this->form['current'] = new \Asgard\Form\Field\TextField([
			'validation' => [
				'callback' => function($input) {
					return $this->user->password === sha1($this->container['config']['key'].$input);
				}
			],
			'messages' => [
				'callback' => 'Password is invalid'
			]
		]);
		$this->form['new'] = new \Asgard\Form\Field\TextField([
			'validation' => [
				'same' => 'confirm'
			],
			'messages' => [
				'same' => 'Password does not match the confirmation'
			]
		]);
		$this->form['confirm'] = new \Asgard\Form\Field\TextField;

		if($this->form->isValid()) {
			$this->user->save([
				'password' => $this->form['new']->value()
			]);
			$this->getFlash()->addSuccess('Your settings have been saved.');
		}
	}
}