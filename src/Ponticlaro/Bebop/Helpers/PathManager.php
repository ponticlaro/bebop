<?php

namespace Ponticlaro\Bebop\Helpers;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\Patterns\SingletonAbstract;

class PathManager extends SingletonAbstract {

	/**
	 * List of environments
	 * 
	 * @var Ponticlaro\Bebop\Common\Collection;
	 */
	private static $__paths;

	/**
	 * Instantiates Env Manager object
	 * 
	 */
	protected function __construct()
	{
		$uploads_data = wp_upload_dir();
		$template_dir = get_template_directory();

		// Instantiate paths collection object
		self::$__paths = Bebop::Collection(array(
			'bebop'   => __DIR__,
			'root'    => ABSPATH,
			'admin'   => '',
			'plugins' => '',
			'content' => '',
			'uploads' => $uploads_data['basedir'],
			'themes'  => str_replace('/'. basename($template_dir), '', $template_dir),
			'theme'   => get_template_directory()
		));
	}

	/**
	 * Used to store a single path using a key
	 * 
	 * @param string $key  Key 
	 * @param string $path Path
	 */
	public static function set($key, $path)
	{
		self::$__paths->set($key, rtrim($path, '/'));
	}

	/**
	 * Returns a single path using a key
	 * with an optionally suffixed realtive path
	 * 
	 * @param  string $key           Key for the target path
	 * @param  string $relative_path Optional relative path
	 * @return string                path
	 */
	public static function get($key, $relative_path = null)
	{	
		// Get path without trailing slash
		$path = self::$__paths->get($key);

		// Concatenate relative URL
		if ($relative_path) $path .= '/'. ltrim($relative_path, '/');

		return $path; 
	}

	/**
	 * Sends all undefined method calls to the paths collection object
	 * 
	 * @param  string $name Method name
	 * @param  array  $args Method arguments
	 * @return mixed        Method returned value
	 */
	public function __call($name, $args)
	{
		if (!method_exists(self::$__paths, $name))
			throw new \Exception("UrlManager->$name method do not exist", 1);

		return call_user_func_array(array(self::$__paths, $name), $args);
	}
}