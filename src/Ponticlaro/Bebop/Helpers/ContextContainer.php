<?php

namespace Ponticlaro\Bebop\Helpers;

class ContextContainer {

	/**
	 * ID of this Context Container
	 * 
	 * @var string
	 */
	private $id;

	/**
	 * Function of this Context Container
	 * 
	 * @var string
	 */
	private $function;

	/**
	 * Instantiates a Context Container
	 * 
	 * @param string $id       ID
	 * @param string $function Function to execute
	 */
	public function __construct($id, $function)
	{
		if (!is_string($id))
			throw new \Exception('$id must be a string');
		
		if (!is_callable($function))
			throw new \Exception('$function must be callable');
		
		$this->id       = $id;
		$this->function = $function;
	}

	/**
	 * Returns ID
	 * 
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Returns function
	 * 
	 * @return string
	 */
	public function getFunction()
	{
		return $this->function;
	}

	/**
	 * Executes Context Container function,
	 * passing $wp_query as the first argument
	 *  
	 * @return string The current context key or null, if none was found
	 */
	public function run()
	{
		global $wp_query;

		return call_user_func_array($this->function, array($wp_query));
	}
}