<?php

namespace Ponticlaro\Bebop\Db\Query;

class Arg implements ArgInterface {
	
	protected $key;

	protected $value;

	protected $is_parent = false;

	protected $current_child;
	
	public function setKey($key)
	{
		if (is_string($key))
			$this->key = $key;
		
		return $this; 
	}

	public function getKey()
	{
		return $this->key;
	}

	public function setValue($value)
	{	
		$this->value = $value;
		
		return $this; 
	}

	public function getValue()
	{
		return $this->value;
	}

	public function isComplete()
	{
		return $this->key && $this->value ? true : false;
	}

	public function isParent()
	{
		return $this->is_parent;
	}

	public function addChild()
	{

	}

	public function getCurrentChild()
	{
		return $this->current_child;
	}
}