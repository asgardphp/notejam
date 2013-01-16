<?php
class MultipleSelectField extends \Coxis\Form\Fields\Field {
	protected $default_render = 'checkboxes';

	public function getChoices() {
		if(isset($this->options['choices']))
			return $this->options['choices'];
		return array();
	}

	public function getCheckboxes($options=array()) {
		if(isset($options['choices']))
			$choices = $options['choices'];
		else
			$choices = $this->getChoices();

		$checkboxes = array();
		foreach($choices as $k=>$v) {
			$checkbox_options = $options;
			$checkbox_options['value'] = $k;
			$checkbox_options['widget_name'] = $v;
			$checkboxes[$k] = $this->getCheckbox($v, $checkbox_options);
		}
		return $checkboxes;
	}

	public function getCheckbox($name, $options=array()) {
		$choices = $this->getChoices();
		$default = $this->value;

		$value = isset($options['value']) ? $options['value']:null;
		if($value===null)
			foreach($choices as $k=>$v)
				if($v == $name) {
					$value = $k;
					break;
				}
		#todo no choice found

		if($value == $default)
			$options['attrs']['checked'] = 'checked';
		$options['label'] = $name;
		return HTMLWidget::checkbox($this->name, $value, $options);
	}
}