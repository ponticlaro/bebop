<?php 

namespace Ponticlaro\Bebop\Html\Elements;

class Label extends \Ponticlaro\Bebop\Html\ElementAbstract {

	public function __construct($text = null)
	{
		// Initialize fundamental objects
		$this->__init();

		// Set tag
		$this->setTag('label');

		// Set name property
		if (!is_null($text))
			$this->append($text);
	}
}