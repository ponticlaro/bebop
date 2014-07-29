<?php 

namespace Ponticlaro\Bebop\Html\Elements;

class Textarea extends \Ponticlaro\Bebop\Html\ControlElement {

	public function __construct($name = null)
	{
		// Initialize fundamental objects
		$this->__init();

		// Set tag
		$this->setTag('textarea');

		// Set name attribute
		if (!is_null($name))
			$this->setName($name);
	}

	public function getHtml()
	{
		return $this->getOpeningTag() . $this->getValue() . $this->getClosingTag();
	}
}