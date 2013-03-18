<?php
namespace Coxis\Form;

abstract class AbstractGroup extends \Coxis\Hook\Hookable implements \ArrayAccess, \Iterator {
	protected $groupName = null;
	protected $dad;
	public $data = array();
	public $files = array();
	protected $fields = array();
	public $errors = array();
	public $hasfile;

	public function render($render_callback, $field, $options=array()) {
		return $this->dad->render($render_callback, $field, $options);
	}

	// public function getRenderCallback($name) {
	// 	if(isset($this->render_callbacks[$name]))
	// 		return $this->render_callbacks[$name];
	// 	elseif($this->dad) 
	// 		return $this->dad->getRenderCallback($name);
	// 	else
	// 		return Form::getDefaultRanderCallback($name);
	// }

	public function setErrors($errors) {
		foreach($errors as $name=>$error)
			$this->fields[$name]->setErrors($error);
	}

	public function getFields() {
		return $this->fields;
	}
	
	public function has($field_name) {
		return isset($this->fields[$field_name]);
	}

	public function getParents() {
		$parents = array();
		
		if($this->groupName !== null)
			$parents[] = $this->groupName;
			
		if($this->dad)
			$parents = array_merge($this->dad->getParents(), $parents);
			
		return $parents;
	}
	
	public function getName() {
		return $this->groupName;
	}
	
	public function isSent() {
		//todo handle get form
		$method = strtolower(\Request::method());
		if($method != 'post')
			return false;
		else
			if($this->dad)
				return $this->dad->isSent();
			else
				if($this->groupName)
					return \POST::has($this->groupName);
				else
					return true;
	}

	public function parseFields($fields, $name) {
			if(is_array($fields)) {
				return new Group($fields, $this, $name, 
					(isset($this->data[$name]) ? $this->data[$name]:array()), 
					(isset($this->files[$name]) ? $this->files[$name]:array())
				);
			}
			elseif(is_object($fields) && is_subclass_of($fields, 'Coxis\Form\Fields\Field')) {
				#todo
				if(in_array($name, array('groupName', 'dad', 'data', 'fields', 'params', 'files'), true))
					throw new \Exception('Can\'t use keyword "'.$name.'" for form field');
				$field = $fields;
				$field->setName($name);
				$field->setDad($this);
				
				if(isset($this->data[$name]))
					$field->setValue($this->data[$name]);
				elseif(isset($this->files[$name]))
					$field->setValue($this->files[$name]);
				else {
					if($this->isSent()) {
						if(isset($field->params['multiple']) && $field->params['multiple'])
							$field->setValue(array());
						else
							$field->setValue('');
					}
				}
					
				return $field;
			}
			elseif($fields instanceof \Coxis\Form\AbstractGroup) {
			// 	d();
			// }
			// elseif(is_object($fields) && (is_subclass_of($fields, 'Coxis\Form\Form') || is_a($fields, 'Coxis\Form\Form'))) {
				$form = $fields;
				$form->setName($name);
				$form->setDad($this);
				// if(isset($this->data[$name]))
				// 	$form->setData($this->data[$name], (isset($this->files[$name]) ? $this->files[$name]:array()));
				$form->setData(
					(isset($this->data[$name]) ? $this->data[$name]:array()),
					(isset($this->files[$name]) ? $this->files[$name]:array())
				);
					
				return $form;
			}
	}

	public function size() {
		return sizeof($this->fields);
	}
	
	public function addFields($fields) {
		foreach($fields as $name=>$sub_fields)
			$this->fields[$name] = $this->parseFields($sub_fields, $name);
			
		//todo
		//~ reset data (fields values) after adding new fields
			
		return $this;
	}
	
	public function addField($field, $name=null) {
		// d($name, in_array($name, array('groupName', 'dad', 'data', 'fields', 'params'), true));
		if(in_array($name, array('groupName', 'dad', 'data', 'fields', 'params'), true))
			throw new \Exception('Can\'t use keyword "'.$name.'"" for form field');
		if($name !== null)
			$this->fields[$name] = $this->parseFields($field, $name);
		else
			$this->fields[] = $this->parseFields($field, sizeof($this->fields));
		
		return $this;
	}
	
	public function setDad($dad) {
		$this->dad = $dad;
	}
	
	public function setFields($fields) {
		$this->fields = array();
		$this->addFields($fields, $this);
	}
	
	public function setName($name) {
		$this->groupName = $name;
	}
	
	public function reset() {
		$this->setData(array(), array());
		
		return $this;
	}
	
	public function setData($data, $files) {
		$this->data = $data;
		$this->files = $files;
		
		$this->updateChilds();
		
		return $this;
	}
	
	public function getData() {
		$res = array();
		
		foreach($this->fields as $field)
			if($field instanceof \Coxis\Form\Fields\Field)
				$res[$field->name] = $field->getValue();
			elseif($field instanceof \Coxis\Form\Group)
				$res[$field->groupName] = $field->getData();
		
		return $res;
	}
	
	public function hasFile() {
		if($this->hasfile === true)
			return true;
		foreach($this->fields as $name=>$field) {
			if(is_subclass_of($field, 'Coxis\Form\AbstractGroup')) {
				if($field->hasFile())
					return true;
			}
			elseif($field instanceof \Coxis\Form\Fields\FileField)
				return true;
		}
		
		return false;
	}
	
	#todo what's this method for?
	protected function updateChilds() {
		foreach($this->fields as $name=>$field) {
			if($field instanceof \Coxis\Form\AbstractGroup) {
				$field->setData(
					(isset($this->data[$name]) ? $this->data[$name]:array()),
					(isset($this->files[$name]) ? $this->files[$name]:array())
				);
			}
			elseif($field instanceof \Coxis\Form\Fields\Field) {
				if($field instanceof \Coxis\Form\Fields\FileField) {
					if(isset($this->files[$name]))
						$field->setValue($this->files[$name]);
					// else
					// 	$field->setValue(null);
				}
				elseif(isset($this->data[$name]))
					$field->setValue($this->data[$name]);
				else {
					if($this->isSent()) {
						if(isset($field->params['multiple']) && $field->params['multiple'])
							$field->setValue(array());
						else
							$field->setValue('');
					}
					// else
					// 	$field->setValue(null);
				}
			}
		}
	}
	
	public function errors() {
		if(!$this->isSent())
			return array();
		
		$errors = array();
	
		foreach($this->fields as $name=>$field)
			if($field instanceof \Coxis\Form\AbstractGroup) {
				$errors[$name] = $field->errors();
				if(sizeof($errors[$name]) == 0)
					unset($errors[$name]);
			}

		// $this->errors = array_merge($errors, $this->my_errors());
		$this->errors = $errors + $this->my_errors();

		$this->setErrors($this->errors);

		#file in memory
		// $this->trigger('afterErrors', array($this));

		return $this->errors;
	}
	
	public function my_errors() {
		$validator = new Validator();
		$constrains = array();
		$messages = array();
		
		foreach($this->fields as $name=>$field)
			if(is_subclass_of($field, 'Coxis\Form\Fields\Field')) {
				if(isset($field->options['validation']))
					$constrains[$name] = $field->options['validation'];
				if(isset($field->options['messages']))
					$messages[$name] = $field->options['messages'];
				if(isset($field->options['choices']))
					if(is_string($field->options['choices']))
						d($field);
					// $constrains[$name]['in']	=	array_keys($field->options['choices']);
			}

		$validator->setConstrains($constrains);
		$validator->setMessages($messages);

		$data = $this->data + $this->files;

		return $validator->errors($data);
	}

	public function addErrors($errors) {
		$this->errors = array_merge($this->errors, $errors);
	}
	
	public function save() {
		if($errors = $this->errors()) {
			$e = new FormException();
			$e->errors = $errors;
			throw $e;
		}
	
		return $this->_save();
	}
	
	public function _save($group=null) {
		if(!$group)
			$group = $this;
			
		if($group instanceof \Coxis\Form\AbstractGroup)
			foreach($group->fields as $name=>$field)
				if($field instanceof \Coxis\Form\AbstractGroup)
					$field->_save($field);
	}
	
	public function isValid() {
		return !$this->errors();
	}
	
	public function remove($name) {
		unset($this->fields[$name]);
	}
	
	public function __unset($name) {
		$this->remove($name);
	}

	public function get($name) {
		return $this->fields[$name];
	}

	public function add($name, $field, $options=array()) {
		$fieldClass = $field.'Field';
		$this->__set($name, new $fieldClass($options));
	}
	
	public function __get($name) {
		return $this->get($name);
	}
	
	public function __set($k, $v) {
		$this->fields[$k] = $this->parseFields($v, $k);
		
		return $this;
	}

	public function __isset($name) {
		return isset($this->fields[$name]);
	}
	
	/* IMPLEMENTS */
	
    public function offsetSet($offset, $value) {
		if(is_null($offset))
			$this->fields[] = $this->parseFields($value, sizeof($this->fields));
		else
			$this->fields[$offset] = $this->parseFields($value, $offset);
    }
	
    public function offsetExists($offset) {
		return isset($this->fields[$offset]);
    }
	
    public function offsetUnset($offset) {
		unset($this->fields[$offset]);
    }
	
    public function offsetGet($offset) {
		return isset($this->fields[$offset]) ? $this->fields[$offset] : null;
    }
	
    public function rewind() {
		reset($this->fields);
    }
  
    public function current() {
		return current($this->fields);
    }
  
    public function key()  {
		return key($this->fields);
    }
  
    public function next()  {
		return next($this->fields);
    }
	
    public function valid() {
		$key = key($this->fields);
		$var = ($key !== NULL && $key !== FALSE);
		return $var;
    }

	public function trigger($name, $args=array(), $cb=null) {
		return parent::trigger($name, array_merge(array($this), $args), $cb);
	}
}