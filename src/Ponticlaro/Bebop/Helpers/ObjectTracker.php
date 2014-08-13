<?php

namespace Ponticlaro\Bebop\Helpers;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\Patterns\SingletonAbstract;
use Ponticlaro\Bebop\Patterns\TrackableObjectAbstract;

class ObjectTracker extends SingletonAbstract {

	/**
	 * List with all the tracked object types lists
	 * @var array
	 */
	protected static $lists = array();

	/**
	 * Tracks Bebop objects
	 * 
	 * @param  mixed $object Object to be tracked
	 * @return void
	 */
	public function track(TrackableObjectAbstract $object)
	{	
		// Get object details
		$id   = $object->getObjectID();
		$type = $object->getObjectType();

		// Add object type collection if not already present
		if ($type && !isset(self::$lists[$type])) 
			self::$lists[$type] = Bebop::Collection()->disableDottedNotation();

		// Add object to its type collection
		if($type && $id) 
			self::$lists[$type]->set($id, $object);

		return $this;
	}

	/**
	 * Returns a single object by "type" and "id"
	 * 
	 * @param  string $type Type of the target object
	 * @param  string $id   ID of the target object
	 * @return object       Object that was stored in memory
	 */
	public function get($type, $id)
	{
		return isset(self::$lists[$type]) ? self::$lists[$type]->get($id) : null;
	}
}