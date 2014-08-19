<?php

namespace Ponticlaro\Bebop\Api;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\Api\Router;
use Ponticlaro\Bebop\Http\Client as HttpClient;

class WpApi {

	/**
	 * Api configuration
	 * 
	 * @var Ponticlaro\Bebop\Common\Collection
	 */
	protected $config;

	/**
	 * Api router
	 * 
	 * @var Ponticlaro\Bebop\Api\Router
	 */
	protected $router;

	/**
	 * Instantiates new Api
	 * 
	 * @param string $rewrite_tag Rewrite Tag. Must not match any WordPress built-in query vars
	 * @param string $base_url    Base URL for all Api routes
	 */
	public function __construct(array $config)
	{
		$this->config = Bebop::Collection($config);

		if (!$this->config->get('rewrite_tag'))
			throw new \Exception("WpApi: rewrite_tag must be a string");

		if (!$this->config->get('base_url'))
			throw new \Exception("WpApi: base_url must be a string");

		// Register stuff on the init hook
		add_action('init', array($this, '__initRegister'), 1);

		// Register custom rewrite rules
		add_action('rewrite_rules_array', array($this, '__rewriteRules'), 99);

		// Handle template includes
		add_action('template_redirect', array($this, '__templateRedirects'), 1);

		// Initialize Router
		$this->router = new Router($this->config->get('rewrite_tag'), $this->config->get('base_url'));
	}

	/**
	 * Sets API URL prefix
	 * 
	 */
	public function setBaseUrl($url)
	{
		if (is_string($url))
			$this->config->set('base_url', ltrim(rtrim($url ,'/'), '/') .'/');

		return $this;
	}

	/**
	 * Returns API URL prefix
	 * 
	 * @return string
	 */
	public function getBaseUrl()
	{
		return ltrim(rtrim($this->config->get('base_url') ,'/'), '/') .'/';
	}

	/**
	 * Returns Router instance
	 *
	 * @return Ponticlaro\Bebop\Api\Router Api Router
	 */
	public function router()
	{
		return $this->router;
	}

	/**
	 * Returns Routes Manager instance
	 *
	 * @return Ponticlaro\Bebop\Api\Routes Api Routes Manager
	 */
	public function routes()
	{
		return $this->router->routes();
	}

	/**
	 * Register Api stuff on the init hook
	 * 
	 * @return void
	 */
	public function __initRegister()
	{
		add_rewrite_tag('%'. $this->config->get('rewrite_tag') .'%','([^&]+)');
	}

	/**
	 * Adds custom rewrite rules for API
	 * 
	 * @param  array $wp_rules Array of rewrite rules
	 * @return array           Modified array of rewrite rules
	 */
	public function __rewriteRules($rules) 
	{
		return array_merge(
			array(
				$this->getBaseUrl() ."?(.*)?$" => 'index.php?'. $this->config->get('rewrite_tag') .'=1'
			), 
			$rules
		);
	}

	/**
	 * Adds template redirections to run the router
	 * 
	 * @return void
	 */
	public function __templateRedirects() 
	{
		global $wp_query;

		if ($wp_query->get($this->config->get('rewrite_tag'))) {

			$this->router->run();
			exit;
		}
	}
}