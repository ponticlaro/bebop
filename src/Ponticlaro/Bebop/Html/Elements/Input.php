<?php 

namespace Ponticlaro\Bebop\Html\Elements;

class Input extends \Ponticlaro\Bebop\Html\ControlElement {

	public function __construct($name = null)
	{
		$this->__init();

		$this->tag = 'input';
		
		$this->attributes->set(array(
			'type'  => 'text',
			'name'  => !is_null($name) && is_string($name) ? $name : '',
			'value' => ''
		));
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