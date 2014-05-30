<?php

namespace Ponticlaro\Bebop;

use Ponticlaro\Bebop;

class UI {

	/**
	 * Class that plugins should be extending to get loaded
	 * 
	 */
	const PLUGIN_ABSTRACT_CLASS = 'Ponticlaro\Bebop\UI\PluginAbstract';

	/**
	 * Bebop UI instance
	 * 
	 * @var Ponticlaro\Bebop\UI
	 */
	private static $__instance;

	/**
	 * URL for current directory
	 * @var string
	 */
	private static $__base_url;

	/**
	 * List of plugins available
	 * 
	 * @var Ponticlaro\Bebop\Common\Collection;
	 */
	private $__plugins;

	/**
	 * Instantiates UI object
	 * 
	 */
	private function __construct()
	{
		// Get URL for current directory
		self::$__base_url = Bebop::getPathUrl(__DIR__);

		// Instantiate plugins collection object
		$this->__plugins = Bebop::Collection();

		// Add built-in plugins
		$this->addPlugins(array(
			'Ponticlaro\Bebop\UI\Plugins\Media',
			'Ponticlaro\Bebop\UI\Plugins\ContentList',
			'Ponticlaro\Bebop\UI\Plugins\MultiContentList'
		));

		$this->__instances = Bebop::Collection();

		// Register common UI scripts
		add_action('admin_enqueue_scripts', array($this, 'registerScripts'));
	}

	/**
	 * Gets single instance of Bebop UI
	 * 
	 * @return Ponticlaro\Bebop\UI Bebop UI class instance
	 */
	public static function getInstance() 
	{
		if(!self::$__instance || !is_a(self::$__instance, 'Ponticlaro\Bebop\UI')) {

			self::$__instance = new UI();
		}

		return self::$__instance;
	}

	/**
	 * Register common scripts for UI plugins
	 * 
	 * @return void
	 */
	public function registerScripts()
	{
		wp_register_style('bebop-ui', self::$__base_url .'/UI/assets/css/bebop-ui.css');
		wp_register_script('mustache', self::$__base_url .'/UI/assets/js/vendor/mustache.js', array(), '0.8.1', true);
		wp_register_script('jquery.debounce', self::$__base_url .'/UI/assets/js/vendor/jquery.ba-throttle-debounce.min.js', array('jquery'), '0.8.1', true);
		
		$dependencies = array(
			'jquery',
			'jquery.debounce',
			'jquery-ui-datepicker',
			'mustache'
		);
		
		wp_register_script('bebop-ui', self::$__base_url .'/UI/assets/js/bebop-ui.js', $dependencies, false, true);
	}

	/**
	 * Adds single plugin class
	 * 
	 * @param string $plugin Class containing a plugin
	 */
	public function addPlugin($plugin)
	{
		$this->__addPlugin($plugin);

		return $this;
	}

	/**
	 * Adds a batch of plugins
	 * 
	 * @param array $plugins Array with plugin classes
	 */
	public function addPlugins(array $plugins = array())
	{	
		foreach ($plugins as $plugin) {
			
			$this->__addPlugin($plugin);
		}

		return $this;
	}

	/**
	 * Internal function to handle addition of a single plugin
	 * 
	 * @param  string $plugin Plugin class
	 * @return void
	 */
	private function __addPlugin($plugin)
	{
		if (is_string($plugin) && class_exists($plugin)) {

			$class = new \ReflectionClass($plugin);

			if ($class->isSubclassOf(self::PLUGIN_ABSTRACT_CLASS)) {
				
				// Load plugin
				$instance = $class->newInstance();
				$instance->load();

				// Store referenc to plugin class
				$this->__plugins->set($plugin::getKey(), $plugin);
			}
		}
	}

	/**
	 * Calls the target plugin using its key
	 * 
	 * @param  string $name Key that identifies the target plugin
	 * @param  array  $args Arguments to pass to target plugin
	 * @return mixed        New instance of target plugin class
	 */
	public function __call($name, $args)
	{
		$className = $this->__plugins->get($name);

		if (class_exists($className)) {

	        return call_user_func_array(

	        	array(
					new \ReflectionClass($className), 
					'newInstance'
				), 
	            $args
	        );
	    }
	}
}