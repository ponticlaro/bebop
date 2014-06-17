<?php

namespace Ponticlaro\Bebop;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\API\Client;
use Ponticlaro\Bebop\API\Router;
use Ponticlaro\Bebop\Http\Client as HttpClient;
use Ponticlaro\Bebop\Patterns\SingletonAbstract;

class API extends SingletonAbstract {

	/**
	 * API base URL
	 * 
	 */
	private static $base_url;

	/**
	 * API HTTP client
	 * @var Ponticlaro\Bebop\Http\Client
	 */
	private $client;

	/**
	 * Creates instance of the Bebop API
	 */
	protected function __construct()
	{
		// Set API base URL
		self::$base_url = '_bebop/api/';

		// Register stuff on the init hook
		add_action('init', array($this, 'initRegister'), 1);

		// Register custom rewrite rules
		add_action('rewrite_rules_array', array($this, 'rewriteRules'), 99);

		// Handle template includes
		add_action('template_redirect', array($this, 'templateRedirects'));

		// Instantiate HTTP Client for the Bebop API
		$url          = Bebop::getUrl('home') .'/'. self::$base_url;
		$this->client = new HttpClient($url);

		// Initialize Router
		$router = self::Router();

		// Set default routes after registering custom post types 
		add_action('init', array($router, 'setDefaultRoutes'), 2);
	}

	/**
	 * Returns Router instance
	 *
	 * @return  Ponticlaro\Bebop\API\Router API Router
	 */
	public static function Router()
	{
		return Router::getInstance();
	}

	/**
	 * Returns Routes Manager instance
	 *
	 * @return  Ponticlaro\Bebop\API\Routes API Routes Manager
	 */
	public static function Routes()
	{
		$router = Router::getInstance();

		return $router->Routes();
	}

	/**
	 * Register API stuff on the init hook
	 * 
	 * @return void
	 */
	public function initRegister()
	{
		add_rewrite_tag('%bebop_api%','([^&]+)');
	}

	/**
	 * Adds custom rewrite rules for API
	 * 
	 * @param  array $wp_rules Array of rewrite rules
	 * @return array           Modified array of rewrite rules
	 */
	public function rewriteRules($wp_rules) 
	{
		$bebop_rules = array(
			$this->base_url ."?(.*)?$" => 'index.php?bebop_api=1'
		);

		return array_merge($bebop_rules, $wp_rules);
	}

	/**
	 * Adds template redirections to run the router
	 * 
	 * @return void
	 */
	public function templateRedirects() 
	{
		global $wp_query;

		if($wp_query->get('bebop_api')) {
			
			$router = self::Router();
			$router->run();
			exit;
		}
	}

	/**
	 * Sets API URL prefix
	 * 
	 */
	public static function setBaseUrl($url)
	{
		if (is_string($url))
			self::$base_url = ltrim(rtrim($url ,'/'), '/') .'/';

		return $this;
	}

	/**
	 * Returns API URL prefix
	 * 
	 * @return string
	 */
	public static function getBaseUrl()
	{
		return Bebop::getUrl('home') .'/'. self::$base_url;
	}

	/**
	 * Maps undefined method calls to the HTTP client
	 * 
	 * @param  string $name Client method name
	 * @param  array  $args Client method arguments
	 * @return mixed        Client method returned value
	 */
	public function __call($method, $args)
	{
		$resource = isset($args[0]) ? $args[0] : '';

		return $this->client->$method($resource);
	}
}