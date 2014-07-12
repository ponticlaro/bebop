<?php

namespace Ponticlaro\Bebop\Helpers;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\Patterns\SingletonAbstract;

class UrlManager extends SingletonAbstract {

	/**
	 * List of environments
	 * 
	 * @var Ponticlaro\Bebop\Common\Collection;
	 */
	private static $__urls;

	/**
	 * Instantiates Env Manager object
	 * 
	 */
	protected function __construct()
	{
		$uploads_data = wp_upload_dir();
		$template_url = get_bloginfo('template_url');

		// Instantiate paths collection object
		self::$__urls = Bebop::Collection(array(
			'bebop'   => Bebop::util('getPathUrl', __DIR__),
			'home'    => home_url(),
			'admin'   => admin_url(),
			'plugins' => plugins_url(),
			'content' => content_url(),
			'uploads' => $uploads_data['baseurl'],
			'themes'  => str_replace('/'. basename($template_url), '', $template_url),
			'theme'   => $template_url
		));
	}

	/**
	 * Used to store a single URL using a key
	 * 
	 * @param string $key Key 
	 * @param string $url URL
	 */
	public static function set($key, $url)
	{
		self::$__urls->set($key, rtrim($url, '/'));
	}

	/**
	 * Returns a single URL using a key
	 * with an optionally suffixed realtive URL
	 * 
	 * @param  string $key          Key for the target URL
	 * @param  string $relative_url Optional relative URL
	 * @return string               URL
	 */
	public static function get($key, $relative_url = null)
	{	
		// Get URL without trailing slash
		$url = self::$__urls->get($key);

		// Concatenate relative URL
		if ($relative_url) $url .= '/'. ltrim($relative_url, '/');

		return $url; 
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
		if (!method_exists(self::$__urls, $name))
			throw new \Exception("UrlManager->$name method do not exist", 1);

		return call_user_func_array(array(self::$__urls, $name), $args);
	}
}