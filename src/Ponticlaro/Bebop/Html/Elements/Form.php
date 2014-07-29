<?php 

namespace Ponticlaro\Bebop\Html\Elements;

class Form extends \Ponticlaro\Bebop\Html\ElementAbstract {

	public function __construct()
	{
		$this->__init();

		$this->setTag('form');
		
		$this->setAttrs(array(
			'method' => 'post',
			'action' => ''
		));
	}
}