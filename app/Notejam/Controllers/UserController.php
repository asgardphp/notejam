<?php
namespace Notejam\Controllers;

class UserController extends \Asgard\Http\Controller {
	public $user;
	public $fragments;

	/**
	 * @Route("signup")
	*/
	public function signupAction($request) {
		$this->container['html']->setTitle('Sign Up');

		$user = new \Notejam\Entities\User;
		$this->form = $this->container->make('entityform', [$user]);
		$this->form['password']->setOptions([
			'validation' => [
				'same' => 'confirm'
			],
			'messages' => [
				'same' => 'Password does not match the confirmation'
			]
		]);
		$this->form['confirm'] = new \Asgard\Form\Fields\TextField;

		if($this->form->isValid()) {
			$this->form->save();
			$this->container['session']->set('user', $user->id);
			return $this->response->redirect($this->url(['General\Controllers\DefaultController', 'index']));
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
		$this->form['email'] = new \Asgard\Form\Fields\TextField;
		$this->form['password'] = new \Asgard\Form\Fields\TextField;

		if($this->form->sent()) {
			$email = $this->form['email']->value();
			$password = $this->form['password']->value();
			$hash = sha1($this->container['config']['key'].$password);
			$user = \Notejam\Entities\User::where(['email' => $email, 'password' => $hash])->first();
			if($user) {
				$this->container['session']->set('user', $user->id);
				return $this->response->redirect($this->url(['General\Controllers\DefaultController', 'index']));
			}
			else {
				$this->getFlash()->addError('Wrong password or email');
				$this->response->setCode(400);
			}
		}
	}

	/**
	 * @Route("signout")
	*/
	public function signoutAction($request) {
		$this->container['session']->delete('user');
		return $this->response->redirect($this->url('signin'));
	}

	/**
	 * @Route("forgot-password")
	*/
	public function forgotPasswordAction($request) {
		$this->container['html']->setTitle('Forgot password?');

		$orm = \Notejam\Entities\User::orm();
		$this->form = $this->container->make('form');
		$this->form['email'] = new \Asgard\Form\Fields\TextField([
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
			$user = \Notejam\Entities\User::loadBy('email', $email);
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

	/**
	 * @Route("settings")
	*/
	public function settingsAction($request) {
		if(!$user = $this->user)
			return $this->response->redirect($this->url(['Notejam\Controllers\UserController', 'signin']));

		$this->container['html']->setTitle('Account Settings');

		$this->form = $this->container->make('form');
		$this->form['current'] = new \Asgard\Form\Fields\TextField([
			'validation' => [
				'callback' => function($input) use($user) {
					return $user->password === sha1($this->container['config']['key'].$input);
				}
			],
			'messages' => [
				'callback' => 'Password is invalid'
			]
		]);
		$this->form['new'] = new \Asgard\Form\Fields\TextField([
			'validation' => [
				'same' => 'confirm'
			],
			'messages' => [
				'same' => 'Password does not match the confirmation'
			]
		]);
		$this->form['confirm'] = new \Asgard\Form\Fields\TextField;

		if($this->form->isValid()) {
			$user->save([
				'password' => $this->form['new']->value()
			]);
			$this->getFlash()->addSuccess('Your settings have been saved.');
		}
	}
}