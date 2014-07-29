<?php 

namespace Ponticlaro\Bebop\Html\Elements;

class Button extends \Ponticlaro\Bebop\Html\ControlElement {

	public function __construct($text = null, $name = null, $value = null)
	{
		$this->__init();

		$this->setTag('button');

		if (is_string($text))
			$this->append($text);

		if (!is_null($name))
			$this->setName($name);

		if (is_string($value))
			$this->setValue($value);
	}
}