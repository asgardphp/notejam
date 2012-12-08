<?php
namespace Coxis\Core\Tools;

class Flash {
	protected $messages = array('success' => array(), 'error' => array());

	function __construct() {
		if(\Coxis\Core\Inputs\Session::has('messages'))
			$this->messages = \Coxis\Core\Inputs\Session::get('messages');
	}

	protected function persist() {
		\Coxis\Core\Inputs\Session::set('messages', $this->messages);
	}

	public function addSuccess($message) {
		if(is_array($message))
			foreach($message as $one_message)
				$this->messages['success'][] = $one_message;
		else
			$this->messages['success'][] = $message;
			
		$this->persist();
		return true;
	}
	
	public function addError($message) {
		if(is_array($message))
			foreach($message as $one_message)
				$this->messages['error'][] = $one_message;
		else
			$this->messages['error'][] = $message;
			
		$this->persist();
		return true;
	}
	
	public function showAll() {
		$this->showSuccess();
		$this->showError();
	}
	
	public function showSuccess() {
		foreach(Tools::flateArray($this->messages['success']) as $msg)
			echo '<div class="message success"><p>'.$msg.'</p></div>'."\n";
		$this->messages['success'] = array();	
		$this->persist();
	}
	
	public function showError() {
		foreach(Tools::flateArray($this->messages['error']) as $msg)
			echo '<div class="message errormsg"><p>'.$msg.'</p></div>'."\n";
		$this->messages['error'] = array();	
		$this->persist();
	}
}