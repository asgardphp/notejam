<?php
namespace Coxis\Core\Inputs;

abstract class InputsBag implements \ArrayAccess {
	protected $inputs;

	function __construct($inputs=array()) {
		$this->inputs = $inputs;
	}

	public function offsetSet($offset, $value) {
		if (is_null($offset))
			$this->inputs[] = $value;
		else
			$this->inputs[$offset] = $value;
	}
	public function offsetExists($offset) {
		return isset($this->inputs[$offset]);
	}
	public function offsetUnset($offset) {
		unset($this->inputs[$offset]);
	}
	public function offsetGet($offset) {
		return isset($this->inputs[$offset]) ? $this->inputs[$offset] : null;
	}

	public function get($name, $default=null) {
		if($this->has($name))
			return Tools::array_get($this->inputs, $name, $default);
		else
			return $default;
	}

	public function set($name, $value=null) {
		if(is_array($name) && array_values($name) !== $name) {
			foreach($name as $k=>$v)
				static::set($k, $v);
		}
		else
			Tools::array_set($this->inputs, $name, $value);
		return $this;
	}

	public function has($name) {
		return Tools::array_isset($this->inputs, $name);
	}

	public function remove($name) {
		Tools::array_unset($this->inputs, $name);
		return $this;
	}

	public function all() {
		return $this->inputs;
	}

	public function clear() {
		$this->inputs = array();
		return $this;
	}

	public function setAll($all) {
		return $this->clear()->set($all);
	}

	public function size() {
		return sizeof($this->inputs);
	}
}