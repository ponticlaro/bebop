<?php

namespace Ponticlaro\Bebop;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\Patterns\SingletonAbstract;

class UI extends SingletonAbstract {

	/**
	 * Class that plugins should be extending to get loaded
	 * 
	 */
	const PLUGIN_ABSTRACT_CLASS = 'Ponticlaro\Bebop\UI\PluginAbstract';

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
	protected function __construct()
	{
		// Get URL for current directory
		self::$__base_url = Bebop::util('getPathUrl', __DIR__);

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
	 * Register common scripts for UI plugins
	 * 
	 * @return void
	 */
	public function registerScripts()
	{
		// Register CSS
		$css_path    = '/UI/assets/css/bebop-ui.css';
		$css_url     = self::$__base_url . $css_path;
		$css_version = Bebop::util('getFileVersion', __DIR__ . $css_path);

		wp_register_style('bebop-ui', $css_url, array(), $css_version);

		// Register development JS
		if (Bebop::isDevEnvEnabled()) {
			
			wp_register_script('mustache', self::$__base_url .'/UI/assets/js/vendor/mustache.js', array(), '0.8.1', true);
			wp_register_script('jquery.debounce', self::$__base_url .'/UI/assets/js/vendor/jquery.ba-throttle-debounce.min.js', array('jquery'), '0.8.1', true);
			
			$dependencies = array(
				'jquery',
				'jquery-ui-datepicker',
				'jquery.debounce'
			);
			
			wp_register_script('bebop-ui', self::$__base_url .'/UI/assets/js/bebop-ui.js', $dependencies, false, true);
		}

		// Register optimized JS
		else {

			// Mustache is optimized separately 
			// so that other components can load it only if needed
			$mustache_path    = '/UI/assets/js/vendor/mustache.min.js';
			$mustache_url     = self::$__base_url . $mustache_path;
			$mustache_version = Bebop::util('getFileVersion', __DIR__ . $mustache_path); 
			
			wp_register_script('mustache', $mustache_url, array(), $mustache_version, true);

			// The following dependencies should never be concatenated and minified
			// These are used by other WordPress features and plugins
			$dependencies = array(
				'jquery',
				'jquery-ui-datepicker'
			);

			$bebop_ui_path    = '/UI/assets/js/bebop-ui.min.js';
			$bebop_ui_url     = self::$__base_url . $bebop_ui_path;
			$bebop_ui_version = Bebop::util('getFileVersion', __DIR__ . $bebop_ui_path); 

			wp_register_script('bebop-ui', $bebop_ui_url, $dependencies, $bebop_ui_version, true);
		}
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