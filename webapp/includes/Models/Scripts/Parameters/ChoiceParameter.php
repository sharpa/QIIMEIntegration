<?php

namespace Models\Scripts\Parameters;

class ChoiceParameter extends DefaultParameter {
	private $options = array();
	
	public function __construct($name, $defaultValue, array $options) {
		$this->name = $name;
		$this->value = $defaultValue;
		$this->options = $options;
	}

	public function renderForForm() {
		$output = "<label for=\"{$this->name}\">{$this->name}<select name=\"{$this->name}\">\n";
		foreach ($this->options as $option) {
			$selected = ($this->value == $option) ? " selected" : "";
			$output .= "<option value=\"{$option}\"{$selected}>{$option}</option>\n";
		}
		$output .= "</select></label>\n";
		return $output;
	}
	
	public function isValueValid($value) {
		if (!$value) {
			return true;
		}
		return in_array($value, $this->options);
	}
}