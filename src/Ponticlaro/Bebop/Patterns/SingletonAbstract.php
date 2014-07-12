<?php

namespace Ponticlaro\Bebop\Patterns;

abstract class SingletonAbstract {	

	/**
	 * Class instances
	 * 
	 * @var object
	 */
	protected static $__instances = array();

	/**
	 * Gets single instance of called class
	 * 
	 * @return object
	 */
	public static function getInstance() 
	{
		$class = get_called_class();

		if (!isset(self::$__instances[$class])) self::$__instances[$class] = new $class();

        return self::$__instances[$class];
	}
}