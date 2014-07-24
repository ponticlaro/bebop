<?php 

namespace Ponticlaro\Bebop\Html\Elements;

class Input extends \Ponticlaro\Bebop\Html\ControlElement {

	public function __construct($name, array $options = array())
	{
		$this->__init();

		$this->tag = 'select';
		
		$this->attributes->set(array(
			'name' => $name
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