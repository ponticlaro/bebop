<?php 

namespace Ponticlaro\Bebop\Html\Elements;

class Radio extends \Ponticlaro\Bebop\Html\ControlElementWithOptions {

	const SELECTED_OPTION_ATTRIBUTE = 'checked';

	public function __construct($name = null, array $options = array())
	{
		// Initialize fundamental objects
		$this->__init();

		// Set tag
		$this->setTag('input');
		
		// Set default attributes
		$this->setAttr('type', 'radio');

		// Set default configuration
		$this->setConfigs([
			'self_closing'           => true,
			'allows_multiple_values' => false
		]);

		// Set name property
		if (!is_null($name))
			$this->setName($name);

		// Set options
		if (!is_null($options))
			$this->setOptions($options);
	}
}