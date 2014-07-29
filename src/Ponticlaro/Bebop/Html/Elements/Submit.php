<?php 

namespace Ponticlaro\Bebop\Html\Elements;

class Submit extends \Ponticlaro\Bebop\Html\ControlElement {

	public function __construct($value = null, $name = null)
	{
		$this->__init();

		$this->setTag('input');
		
		$this->setAttr('type', 'submit');

		$this->setConfig('self_closing', true);

		if (!is_null($name))
			$this->setName($name);

		if (is_string($value))
			$this->setValue($value);
	}
}