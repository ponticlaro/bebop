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