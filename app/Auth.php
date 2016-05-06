<?php
class Auth implements \Asgard\Auth\IAuth {
	protected $session;
	protected $cookies;
	protected $salt;
	protected $user;

	public function __construct($session, $cookies, $salt) {
		$this->session = $session;
		$this->cookies = $cookies;
		$this->salt = $salt;
	}

	public function isConnected() {
		return $this->session->has('user') || $this->attemptRemember();
	}

	public function isGuest() {
		return !$this->isConnected();
	}

	public function check() {
		if(!$this->isConnected())
			throw new \Asgard\Auth\NotAuthenticatedException;
	}

	public function attempt($email, $password) {
		$hash = $this->hash($password);
		$user = \Notejam\Entity\User::where([
			'email' => $email,
			'password' => $hash,
		])->first();

		if($user) {
			$this->connect($user->id);
			return $user;
		}
		else
			return false;
	}

	public function attemptRemember() {
		$remember = $this->cookies['user_remember'];
		if($remember) {
			$user = \Notejam\Entity\User::where(['SHA1(CONCAT(email, \'-\', password))' => $remember])->first();
			if($user) {
				$this->session['user'] = $user->id;
				return true;
			}
			else
				return false;
		}
	}

	public function remember($email, $password) {
		$this->cookies['user_remember'] = sha1($email.'-'.\Notejam\Entity\User::hash($password));
	}

	public function connect($id) {
		$this->session['user'] = $id;
	}

	public function disconnect() {
		unset($this->session['user']);
		unset($this->cookies['user_remember']);
	}

	public function user() {
		if($this->user !== null)
			return $this->user;
		try {
			$this->check();
		} catch(\Asgard\Auth\NotAuthenticatedException $e) {
			return null;
		}
		$this->user = \Notejam\Entity\User::load($this->session['user']);
		if($this->user === null) {
			$this->user = false;
			return null;
		}
		return $this->user;
	}

	public function setUser($user) {
		$this->user = $user;
	}

	protected function hash($password) {
		return sha1($this->salt.$password);
	}
}