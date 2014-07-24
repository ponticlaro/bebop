<?php 

namespace Ponticlaro\Bebop\Html;

class ControlElement extends ElementAbstract {

	public function setValue($value)
	{
		if (is_string($value) || is_integer($value) || is_bool($value)) {
			
			$this->attributes->set('value', $value);
		}

		return $this;
	}

	public function getValue()
	{
		return $this->attributes->get('value');
	}
}