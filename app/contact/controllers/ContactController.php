<?php
namespace App\Contact\Controllers;

class ContactController extends \Coxis\Core\Controller {
	/**
	@Route('contact')
	*/
	public function indexAction($request) {
		$this->form = new Form;
		$this->form->name = new TextField(array('validation'=>'required'));
		$this->form->email = new TextField(array('validation'=>array('required', 'email')));
		$this->form->message = new TextField(array('validation'=>'required'));
		$this->form->captcha = new CaptchaField;

		if($this->form->isSent()) {
			if($this->form->isValid()) {
				$txt = '';
				foreach($this->form as $field) {
					if($field->name == '_csrf_token')
						continue;
					$txt .= $field->label().': '.$field->getValue()."\n";
				}
				Email::create(Value::val('email'), $this->form->email->getValue(), 'Contact', $txt)->send();

				$this->form->reset();
				\Flash::addSuccess('Merci pour votre message.');
			}
			else {
				\Flash::addError(Tools::flateArray($this->form->errors()));
			}
		}
	}
}