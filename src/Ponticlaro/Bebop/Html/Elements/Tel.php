<?php 

namespace Ponticlaro\Bebop\Html\Elements;

class Tel extends \Ponticlaro\Bebop\Html\ControlElement {

	public function __construct()
	{
		$this->__init();

		$this->tag = 'input';
		
		$this->attributes->set(array(
			'type'  => 'tel',
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