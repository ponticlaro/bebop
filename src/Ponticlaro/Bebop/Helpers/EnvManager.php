<?php

namespace Ponticlaro\Bebop\Helpers;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\Patterns\SingletonAbstract;

class EnvManager extends SingletonAbstract {

	/**
	 * List of environments
	 * 
	 * @var Ponticlaro\Bebop\Common\Collection;
	 */
	private static $__environments;

	/**
	 * Instantiates Env Manager object
	 * 
	 */
	protected function __construct()
	{
		// Instantiate environments collection object
		self::$__environments = Bebop::Collection(array(
			'development' => new Env('development'),
			'staging'     => new Env('staging'),
			'production'  => new Env('production')
		));
	}

	/**
	 * Adds a new environment with target key,
	 * if we do not have that key already
	 * 
	 * @param string $key Key for the new environment
	 */
	public function add($key)
	{
		if (!is_string($key) || self::$__environments->hasKey($key)) return $this;

		self::$__environments->set($key, new Env($key));

		return $this;
	}

	/**
	 * Replaces an existing environment or adds a new one
	 * 
	 * @param string $key Key of the environment to replace or add
	 */
	public function replace($key)
	{
		if (!is_string($key)) return $this;

		self::$__environments->set($key, new Env($key));

		return $this;
	}

	/**
	 * Checks if the target environment exists
	 * 
	 * @param string $key Key of the environment to check
	 */
	public function exists($key)
	{
		if (!is_string($key)) return false;

		return self::$__environments->hasKey($key);
	}

	/**
	 * Returns the target environment
	 * 
	 * @param string $key Key of the environment to get
	 */
	public function get($key)
	{
		if (!is_string($key)) return $this;

		return self::$__environments->get($key);
	}

	/**
	 * Removes the target environment
	 * 
	 * @param string $key Key of the environment to remove
	 */
	public function remove($key)
	{
		if (!is_string($key)) return $this;

		self::$__environments->remove($key);

		return $this;
	}

	/**
	 * Checks if the target environment is the current one
	 * 
	 * @param  string  $key Key of the environment to check
	 * @return boolean      True if it is the current environment, false otherwise
	 */
	public function is($key)
	{
		if (!is_string($key) || !self::$__environments->hasKey($key)) return false;

		$env = self::$__environments->get($key);

		return $env->hasHost($_SERVER['SERVER_NAME']);
	}

	/**
	 * Returns the current environment
	 * 
	 * @return Ponticlaro\Bebop\Helpers\Env The current environment
	 */
	public function getCurrent()
	{
		$envs = self::$__environments->get();

		foreach ($envs as $key => $env) {
			
			if ($env->isCurrent()) return $env;
		}

		return null;
	}

	/**
	 * Returns the key of the current environment
	 * 
	 * @return string Key of the current environment
	 */
	public function getCurrentKey()
	{
		$current_env = $this->getCurrent();

		return $current_env instanceof \Ponticlaro\Bebop\Helpers\Env ? $current_env->getKey() : null;
	}
}