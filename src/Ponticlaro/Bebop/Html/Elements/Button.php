<?php 

namespace Ponticlaro\Bebop\Html\Elements;

class Button extends \Ponticlaro\Bebop\Html\ControlElement {

	public function __construct($text = null, $name = null, $value = null)
	{
		$this->__init();

		$this->tag = 'button';

		if (is_string($text))
			$this->append($text);

		if (is_string($name))
			$this->attributes->set('name', $name);

		if (is_string($value))
			$this->attributes->set('value', $value);
	}

	/**
	 * Making sure that tag cannot be changed
	 * 
	 * @param string $tag
	 */
	public function setTag($tag)
	{
		return $this;
	}
}