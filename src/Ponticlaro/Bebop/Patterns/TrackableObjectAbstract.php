<?php

namespace Ponticlaro\Bebop\Patterns;

class TrackableObjectAbstract implements TrackableObjectInterface {

	/**
	 * Object id 	 
	 * 
	 * @var string
	 */
	protected $__id;

	/**
	 * Object type
	 * 
	 * @var string
	 */
	protected $__type;

	/**
	 * Returns object ID
	 * 
	 * @return string Object ID
	 */
	public function getObjectID()
	{
		return $this->__id;
	}

	/**
	 * Returns object type
	 * 
	 * @return string Object type
	 */
	public function getObjectType()
	{
		return $this->__type;
	}
}