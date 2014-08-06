<?php

namespace Ponticlaro\Bebop\Helpers;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\Patterns\SingletonAbstract;
use Ponticlaro\Bebop\Patterns\TrackableObjectAbstract;

class BebopFactory {

	/**
	 * List of manufacturable classes
	 * 
	 * @var array
	 */
	protected static $manufacturable = array(
		'AdminPage'  => '\Ponticlaro\Bebop\AdminPage',
		'Collection' => '\Ponticlaro\Bebop\Common\Collection',
		'Metabox'    => '\Ponticlaro\Bebop\Metabox',
		'Option'     => '\Ponticlaro\Bebop\Db\Option',
		'PostType'   => '\Ponticlaro\Bebop\PostType',
		'Taxonomy'   => '\Ponticlaro\Bebop\Taxonomy'
	);

	/**
	 * Making sure class cannot get instantiated
	 */
	protected function __construct() {}

	/**
	 * Making sure class cannot get instantiated
	 */
	protected function __clone() {}

	/**
	 * Adds a new manufacturable class
	 * 
	 * @param string $type  Object type ID
	 * @param string $class Full namespace for a class
	 */
	public static function set($type, $class)
	{
		self::$manufacturable[$type] = $class;
	}

	/**
	 * Removes a new manufacturable class
	 * 
	 * @param string $type  Object type ID
	 */
	public static function remove($type)
	{
		if (isset(self::$manufacturable[$type])) unset(self::$manufacturable[$type]);
	}

	/**
	 * Creates instance of target class
	 * 
	 * @param  string] $type Class ID
	 * @param  array   $args Class arguments
	 * @return object        Class instance
	 */
	public static function create($type, array $args = array())
	{
		// Check if target is in the allowed list
		if (array_key_exists($type, self::$manufacturable)) {

			$class_name = self::$manufacturable[$type];

			return call_user_func(array(__CLASS__, "__createInstance"), $class_name, $args);
		}

		// Return null if target object is not manufacturable
		return null;
	}

	/**
	 * Creates and instance of the target class
	 * 
	 * @param  string $class_name Target class
	 * @param  array  $args       Arguments to pass to target class
	 * @return mixed              Class instance or false
	 */
	private static function __createInstance($class_name, array $args = array())
	{
    	// Get an instance of the target class
        $obj = call_user_func_array(

        	array(
				new \ReflectionClass($class_name), 
				'newInstance'
			), 
            $args
        );
        	
    	// Track object if trackable
    	if ($obj instanceof TrackableObjectAbstract) Bebop::track($obj);

    	// Return object
    	return $obj;
	}
}