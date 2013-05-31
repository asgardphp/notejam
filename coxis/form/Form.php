<?php
namespace Coxis\Form;

class Form extends AbstractGroup {
	public $params = array(
		'method'	=>	'post',
		'action'	=>	'',
	);
	protected $callbacks = array();

	public $render_callbacks = array();

	function __construct($param1=null, $param2=array(), $param3=array()) {
		//$param1		=	form params
		//$param2	=	form fields
		//OR
		//$param1		=	form name
		//$param2	=	form params
		//$param3	=	form fields
		if(is_array($param1)) {
			$name = null;
			$params = $param1;
			$this->params = array_merge($this->params, $params);
			$this->fetch();
			$this->setFields($param2);
		}
		else {
			$name = $param1;
			$params = $param2;
			$this->params = array_merge($this->params, $params);
			$this->groupName = $name;
			$this->fetch();
			$this->setFields($param3);
		}

		$this->add('_csrf_token', '\Coxis\Form\Fields\CSRF');
	}
	
	public function setDad($dad) {
		$this->dad = $dad;
		$this->remove('_csrf_token');
	}

	public function render($render_callback, $field, $options=array()) {
		if($this->dad)
			return $this->dad->render($render_callback, $field, $options);

		if(is_function($render_callback))
			$cb = $render_callback;
		elseif(isset($this->render_callbacks[$render_callback]))
			$cb = $this->render_callbacks[$render_callback];
		else {
			$cb = function($field, $options=array()) use($render_callback) {
				return \Coxis\Form\Widgets\HTMLWidget::$render_callback($field->getName(), $field->getValue(), $options);
			};
		}

		if(!$cb)
			throw new \Exception('Render callback "'.$render_callback.'" does not exist.');

		$options = $field->options+$options;
		$options['field'] = $field;
		$options['id'] = $field->getID();

		if($this->hasHook('render'))
			$res = $this->trigger('render', array($field, $cb($field, $options), $options));
		else
			return $cb($field, $options);

		return $res;
	}

	public function uploadSuccess() {
		return \Server::get('CONTENT_LENGTH') <= (int)ini_get('post_max_size')*1024*1024;
	}

	public function setRenderCallback($name, $callback) {
		$this->render_callbacks[$name] = $callback;
	}
	
	public function __toString() {
		return $this->params['name'];
	}
	
	protected function convertTo($type, $files) {
		$res = array();
		foreach($files as $name=>$file)
			if(is_array($file))
				$res[$name] = $this->convertTo($type, $file);
			else
				$res[$name][$type] = $file;
				
		return $res;
	}
	
	protected function merge_all($name, $type, $tmp_name, $error, $size) {
		foreach($name as $k=>$v) {
			if(isset($v['name']) && !is_array($v['name']))
				$name[$k] = array_merge($v, $type[$k], $tmp_name[$k], $error[$k], $size[$k]);
			else 
				$name[$k] = $this->merge_all($name[$k], $type[$k], $tmp_name[$k], $error[$k], $size[$k]);
		}
		
		return $name;
	}

	protected function parseFiles($raw) {
		if(isset($raw['name']) && isset($raw['type']) && isset($raw['tmp_name']) && isset($raw['error']) && isset($raw['size'])) {
			if(is_array($raw['name'])) {
				$name = $this->convertTo('name', $raw['name']);
				$type = $this->convertTo('type', $raw['type']);
				$tmp_name = $this->convertTo('tmp_name', $raw['tmp_name']);
				$error = $this->convertTo('error', $raw['error']);
				$size = $this->convertTo('size', $raw['size']);
				
				$files = $this->merge_all($name, $type, $tmp_name, $error, $size);
				return $files;
			}
			else
				return $raw;
		}
		else {
			foreach($raw as $k=>$v) {
				if($v['error'] == 4)
					continue;
				else
					$raw[$k] = $this->parseFiles($v);
			}
			return $raw;
		}
	}
	
	public function fetch() {
		$raw = array();
		$files = array();
			
		if($this->groupName) {
			if(\File::get($this->groupName) !== null)
				$raw = \File::get($this->groupName);
			else
				$raw = array();
		}
		else
			$raw = \File::all();

		$files = $this->parseFiles($raw);

		$this->data = array();
		if($this->groupName) {
				$this->setData(
					\POST::get($this->groupName, array()),
					$files
				);
		}
		else
			$this->setData(\POST::all(), $files);

		return $this;
	}
	
	//todo should not pass this args here but when defining the form
	public function open($options=array()) {
		$options = array_merge($this->params, $options);
		$action = isset($options['action']) && $options['action'] ? $options['action']:\URL::full();
		$method = isset($options['method']) ? $options['method']:'post';
		$enctype = isset($options['enctype']) ? $options['enctype']:($this->hasFile() ? ' enctype="multipart/form-data"':'');
		$attrs = '';
		if(isset($options['attrs']))
			foreach($options['attrs'] as $k=>$v)
				$attrs .= ' '.$k.'="'.$v.'"';
		echo '<form action="'.$action.'" method="'.$method.'"'.$enctype.$attrs.'>'."\n";
		
		return $this;
	}
	
	public function close() {
		echo $this->_csrf_token->def();
		echo '</form>';
		
		return $this;
	}
	
	public function submit($value) {
		echo HTMLHelper::tag('input', array(
			'type'		=>	'submit',
			'value'	=>	$value,
		));
		
		return $this;
	}

	public function getGeneralErrors() {
		if(!$this->errors)
			return;
		$gen_errors = array();
		foreach($this->errors as $field_name=>$errors) {
			if(!$this->has($field_name) || is_subclass_of($this->$field_name, 'Coxis\Form\Fields\HiddenField'))
				$gen_errors[$field_name] = $errors;
		}
		return $gen_errors;
	}

	// public function showErrors() {
	// 	if(!$this->errors)
	// 		return;
	// 	$error_found = false;
	// 	foreach($this->errors as $field_name=>$errors) {
	// 		if(!$this->has($field_name) || is_subclass_of($this->$field_name, 'Coxis\Form\Fields\HiddenField')) {
	// 			if(!$error_found) {
	// 				echo '<ul>';
	// 				$error_found = true;
	// 			}
	// 			if(is_array($errors)) {
	// 				foreach(Tools::flateArray($errors) as $error)
	// 					echo '<li class="error">'.$error.'</li>';
	// 			}
	// 			else
	// 				echo '<li class="error">'.$errors.'</li>';
	// 		}
	// 	}
	// 	if($error_found)
	// 		echo '</ul>';
	// }

	public function isValid() {
		return !$this->errors() && $this->isSent();
	}
}
