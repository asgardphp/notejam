<?php
namespace Notejam\Controller;

class PublicUser extends \Asgard\Http\Controller {
	public $fragments;

	/**
	 * @Route("signup")
	*/
	public function signupAction($request) {
		$this->container['html']->setTitle('Sign Up');

		$user = new \Notejam\Entity\User;
		$this->form = $this->container->make('entityform', [$user]);
		$this->form['password']->setOptions([
			'validation' => [
				'same' => 'confirm'
			],
			'messages' => [
				'same' => 'Password does not match the confirmation'
			]
		]);
		$this->form['confirm'] = new \Asgard\Form\Field\TextField;

		if($this->form->isValid()) {
			$this->form->save();
			$this->container['auth']->connect($user->id);
			return $this->response->redirect($this->url(['General\Controller\DefaultController', 'index']));
		}
		else
			$this->response->setCode(400);
	}

	/**
	 * @Route("signin")
	*/
	public function signinAction($request) {
		$this->container['html']->setTitle('Sign In');

		$this->form = $this->container->make('form');
		$this->form['email'] = new \Asgard\Form\Field\TextField;
		$this->form['password'] = new \Asgard\Form\Field\TextField;

		if($this->form->sent()) {
			$email = $this->form['email']->value();
			$password = $this->form['password']->value();
			if($user = $this->container['auth']->attempt($email, $password))
				return $this->response->redirect($this->url(['General\Controller\DefaultController', 'index']));
			else {
				$this->getFlash()->addError('Wrong password or email');
				$this->response->setCode(400);
			}
		}
	}

	/**
	 * @Route("forgot-password")
	*/
	public function forgotPasswordAction($request) {
		$this->container['html']->setTitle('Forgot password?');

		$orm = \Notejam\Entity\User::orm();
		$this->form = $this->container->make('form');
		$this->form['email'] = new \Asgard\Form\Field\TextField([
			'validation' => [
				'callback' => function($input) use($orm) {
					return $orm->where('email', $input)->count() > 0;
				}
			],
			'messages' => [
				'callback' => 'Email does not exist'
			]
		]);

		if($this->form->isValid()) {
			$email = $this->form['email']->value();
			$user = \Notejam\Entity\User::loadBy('email', $email);
			$password = \Asgard\Common\Tools::randstr(10);
			$user->save([
				'password' => $password
			]);
			$this->container['email']->send(function($msg) use($email, $password) {
				$msg->subject('Notejam - New password');
				$msg->from('notejam@notejam.com');
				$msg->to($email);
				$msg->text('Your new password: '.$password);
			});
			$this->getFlash()->addSuccess('Your new password was sent to your email address.');
		}
	}
}