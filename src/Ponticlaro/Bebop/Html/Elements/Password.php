<?php 

namespace Ponticlaro\Bebop\Html\Elements;

class Password extends \Ponticlaro\Bebop\Html\ControlElement {

	public function __construct($name = null)
	{
		$this->__init();

		$this->setTag('input');
		
		$this->setAttr('type', 'password');

		if (!is_null($name))
			$this->setName($name);

		$this->setConfig('self_closing', true);
	}
}