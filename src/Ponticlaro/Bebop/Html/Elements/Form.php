<?php 

namespace Ponticlaro\Bebop\Html\Elements;

class Form extends \Ponticlaro\Bebop\Html\ElementAbstract {

	public function __construct()
	{
		$this->__init();

		$this->tag = 'form';
		
		$this->attributes->set(array(
			'method' => 'post',
			'action' => ''
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