<?php

namespace Ponticlaro\Bebop\Patterns;

use Ponticlaro\Bebop;

abstract class FactoryAbstract {

	/**
	 * List of manufacturable classes
	 * 
	 * @var array
	 */
	protected static $manufacturable = array();

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
		static::$manufacturable[$type] = $class;
	}

	/**
	 * Removes a new manufacturable class
	 * 
	 * @param string $type  Object type ID
	 */
	public static function remove($type)
	{
		if (isset(static::$manufacturable[$type])) unset(static::$manufacturable[$type]);
	}

    /**
     * Checks if there is a manufacturable with target key
     * 
     * @param  string  $key Target key
     * @return boolean      True if key exists, false otherwise
     */
    public static function canManufacture($key)
    {
        return is_string($key) && isset(static::$manufacturable[$key]) ? true : false;
    }

    /**
     * Returns the id to manufacture another instance of the passed object, if any
     * 
     * @param  object $instance Arg instance
     * @return string           Arg ID 
     */
    public static function getInstanceId($instance)
    {
        if (is_object($instance)) {

            $class = get_class($instance);
            $id    = array_search($class, static::$manufacturable);

            return $id ?: null;
        }

        return null;
    }

	/**
	 * Creates instance of target class
	 * 
	 * @param  string $type Class ID
	 * @param  array  $args Class arguments
	 * @return object       Class instance
	 */
	public static function create($type, array $args = array())
	{
		// Check if target is in the allowed list
		if (array_key_exists($type, static::$manufacturable)) {

			$class_name = static::$manufacturable[$type];

			// Get an instance of the target class
	        $obj = call_user_func_array(

	        	array(
					new \ReflectionClass($class_name), 
					'newInstance'
				), 
	            $args
	        );

	    	// Return object
	    	return $obj;
		}

		// Return null if target object is not manufacturable
		return null;
	}
}