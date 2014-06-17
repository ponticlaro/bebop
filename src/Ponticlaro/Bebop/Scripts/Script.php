<?php

namespace Ponticlaro\Bebop\Scripts;

use Ponticlaro\Bebop;

class Script {

	/**
	 * Holds configuration parameters
	 * 
	 * @var \Ponticlaro\Bebop\Common\Collection
	 */
	protected $config;

	/**
	 * Holds environment specific configuration modifications
	 * 
	 * @var \Ponticlaro\Bebop\Common\Collection
	 */
	protected $env_configs;

	/**
	 * Flag that states if the script is already registered
	 * 
	 * @var boolean
	 */
	protected $is_registered = false;

	/**
	 * Instantiates a new script object 
	 * 
	 * @param string  $id           Script ID
	 * @param string  $file_path    Script file path
	 * @param array   $dependencies Script dependencies
	 * @param string  $version      Script version
	 * @param boolean $in_footer    If script should be loaded in the wp_footer hook
	 */
	public function __construct($id, $file_path, array $dependencies = array(), $version = null, $in_footer = true)
	{
		// Throw error if any of these fail
		if (!is_string($id) || !is_string($file_path) || !is_string($version) || !is_bool($in_footer)) {
			# code...
		}

		// Create config collection
		$this->config = Bebop::Collection(array(
			'id'           => $id,
			'file_path'    => ltrim($file_path, '/'),
			'in_footer'    => $in_footer,
			'dependencies' => $dependencies,
			'base_url'     => null
		));

		// Create environment configuration collection
		$this->env_configs = Bebop::Collection();
	}

	/**
	 * Sets script ID
	 * 
	 * @param string $id
	 */
	public function setId($id)
	{
		if (is_string($id))
			$this->config->set('id', $id);

		return $this;
	}

	/**
	 * Returns script ID
	 * 
	 * @return string
	 */
	public function getId()
	{
		return $this->config->get('id');
	}

	/**
	 * Sets file path
	 * 
	 * @param string $file_path Path relative to the theme location
	 */
	public function setFilePath($file_path)
	{
		if (is_string($file_path))
			$this->config->set('file_path', ltrim($file_path, '/'));

		return $this;
	}

	/**
	 * Returns file path
	 * 
	 * @return string
	 */
	public function getFilePath()
	{
		return $this->config->get('file_path');
	}

	/**
	 * Sets a base URL for this script
	 * 
	 * @param string $base_url 
	 */
	public function setBaseUrl($base_url)
	{
		if (is_string($base_url)) $this->config->set('base_url', rtrim($base_url, '/'));

		return $this;
	}

	/**
	 * Returns script base URL
	 * 
	 * @return string
	 */
	public function getBaseUrl()
	{
		return $this->config->get('base_url') ?: Bebop::getUrl('theme');
	}

	/**
	 * Returns script absolute URL
	 * 
	 * @return string
	 */
	public function getAbsoluteUrl()
	{
		return $this->getBaseUrl() .'/'. $this->getFilePath();
	}

	/**
	 * Sets if the script should load in the footer or not
	 * 
	 * @param bool $in_footer True if it should be loaded in the footer, false otherwise
	 */
	public function loadInFooter($in_footer)
	{
		if (is_bool($in_footer))
			$this->config->set('in_footer', $in_footer);

		return $this;
	}

	/**
	 * Returns ture if it should be loaded in the footer, false otherwise
	 * 
	 * @return bool
	 */
	public function getLoadInFooter()
	{
		return $this->config->get('in_footer');
	}

	/**
	 * Sets file version
	 * 
	 * @param string $version
	 */
	public function setVersion($version)
	{
		if (is_string($version))
			$this->config->set('version', $version);

		return $this;
	}

	/**
	 * Returns file version
	 * 
	 * @return string
	 */
	public function getVersion()
	{
		$version = $this->config->get('version');
		$path    = Bebop::getPath('theme') .'/'. $this->getFilePath();

		if (!$version && is_readable($path)) $version = filemtime($path);

		return $version;
	}

	/**
	 * Sets dependencies
	 * 
	 * @param array $dependencies
	 */
	public function setDependencies(array $dependencies = array())
	{
		$this->config->set('dependencies', $dependencies);

		return $this;
	}

	/**
	 * Returns dependencies
	 * 
	 * @return array
	 */
	public function getDependencies()
	{
		return $this->config->get('dependencies');
	}

	/**
	 * Adds a function to execute when the target '$env' is active
	 * 
	 * @param  string $env Target environment ID
	 * @param  string $fn  Function to execute
	 */
	public function onEnv($env, $fn)
	{
		if (is_string($env) && is_callable($fn)) $this->env_configs->set($env, $fn);

		return $this;
	}

	/**
	 * Registers script
	 * 
	 */
	public function register()
	{
		// Apply any environment specific modification
		$this->__applyEnvModifications();

		// Register script
		wp_register_script(
			$this->getId(),
			$this->getAbsoluteUrl(), 
			$this->getDependencies(), 
			$this->getVersion(), 
			$this->getLoadInFooter()
		);

		// Mark script as registered
		$this->is_registered = true;
	}

	/**
	 * Deregisters script
	 * 
	 */
	public function deregister()
	{
		wp_deregister_script($this->getId());

		return $this;
	}

	/**
	 * Enqueues script
	 * 
	 */
	public function enqueue()
	{
		// Register script if not already registered
		if (!$this->is_registered) $this->register();

		// Enqueue script
		wp_enqueue_script($this->getId());

		return $this;
	}

	/**
	 * Dequeues script
	 * 
	 */
	public function dequeue()
	{
		wp_dequeue_script($this->getId());

		return $this;
	}

	/**
	 * Executes any function that exists for the current environment
	 * 
	 */
	protected function __applyEnvModifications()
	{
		// Get current environment
		$current_env = Bebop::Env()->getCurrentKey();

		// Execute current environment function
		if ($this->env_configs->hasKey($current_env))
			call_user_func_array($this->env_configs->get($current_env), array($this));
	}
}