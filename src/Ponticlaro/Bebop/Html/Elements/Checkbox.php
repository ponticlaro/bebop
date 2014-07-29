<?php 

namespace Ponticlaro\Bebop\Html\Elements;

class Checkbox extends \Ponticlaro\Bebop\Html\ControlElementWithOptions {

	const SELECTED_OPTION_ATTRIBUTE = 'checked';

	public function __construct($name = null, array $options = array())
	{
		// Initialize fundamental objects
		$this->__init();

		// Set tag
		$this->setTag('input');
		
		// Set default attributes
		$this->setAttr('type', 'checkbox');

		// Set default configuration
		$this->setConfig('self_closing', true);

		// Set name property
		if (!is_null($name))
			$this->setName($name);

		// Set options
		if (!is_null($options))
			$this->setOptions($options);
	}
}