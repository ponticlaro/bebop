<?php

namespace Ponticlaro\Bebop\UI\Plugins;

use Ponticlaro\Bebop;

class MultiContentList extends \Ponticlaro\Bebop\UI\PluginAbstract {

	/**
	 * Identifier Key to call this plugin
	 * 
	 * @var string
	 */
	protected static $__key = 'MultiList';

	/**
	 * Contains the URL for the directory containing this file
	 * 
	 * @var String
	 */
	protected static $__base_url;

	/**
	 * Holds configuration values
	 * 
	 * @var Ponticlaro\Bebop\Common\Collection
	 */
	protected $__config;

	/**
	 * Holds all added lists
	 * 
	 * @var Ponticlaro\Bebop\Common\Collection
	 */
	protected $__lists;

	/**
	 * Loads plugin OR creates single instance of the MultiContentList plugin
	 * 
	 */
	public function __construct()
	{
		// Get URL for the directory containing this plugin
		self::$__base_url = Bebop::util('getPathUrl', __DIR__);

		// Instantiate configuration collections
		$this->__config = Bebop::Collection();
		$this->__lists  = Bebop::Collection();

		// Get function arguments
		$args = func_get_args();

		// Conditionally creates single instance of the MultiContentList plugin
		if ($args) call_user_func_array(array($this, '__createInstance'), $args);
	}

	/**
	 * This function will register everything on the right hooks
	 * when the plugin is added to Bebop::UI
	 *  
	 * @return void
	 */
	public function load()
	{
		// Register back-end scripts
		add_action('admin_enqueue_scripts', array($this, 'registerScripts'));
	}

	/**
	 * Register MultiContentList scripts
	 */
	public function registerScripts()
	{
		// Register CSS
		$css_path         = 'ui/multilist/css/bebop-ui--multilist.css';
		$css_version      = Bebop::util('getFileVersion', Bebop::getPath('_bebop/static', $css_path));
		$css_dependencies = array('bebop-ui');

		wp_register_style('bebop-ui--multilist', Bebop::getUrl('_bebop/static', $css_path), $css_dependencies, $css_version);

		// Register development JS
		if (Bebop::isDevEnvEnabled()) {
			
			wp_register_script('bebop-ui--multilistView', Bebop::getUrl('_bebop/static', 'ui/multilist/js/views/MultiList.js'), array(), false, true);

			$js_dependencies = array(
				'jquery',
				'jquery-ui-tabs',
				'bebop-ui--multilistView'
			);		

			wp_register_script('bebop-ui--multilist', Bebop::getUrl('_bebop/static', 'ui/multilist/js/bebop-ui--multilist.js'), $js_dependencies, false, true);
		}

		// Register optimized JS
		else {

			// The following dependencies should never be concatenated and minified
			// Some are use by other WordPress features and plugins
			// and other are register by Bebop UI
			$js_dependencies = array(
				'jquery',
				'jquery-ui-tabs'
			);

			$js_path    = 'ui/multilist/js/bebop-ui--multilist.min.js';
			$js_version = Bebop::util('getFileVersion', Bebop::getPath('_bebop/static', $js_path));

			wp_register_script('bebop-ui--multilist', Bebop::getUrl('_bebop/static', $js_path), $js_dependencies, $js_version, true);
		}
	}

	/**
	 * Enqueues scripts that MultiContentList needs
	 * 
	 */
	private function __enqueueScripts()
	{
		wp_enqueue_style('bebop-ui--multilist');
		wp_enqueue_script('bebop-ui--multilist');
	}

	/**
	 * Creates single instance of the MultiContentList plugin
	 * 
	 * @param  string $title  Instance Title. Also used to create a slugified key
	 * @param  array  $config Configuration array
	 * @return object         Ponticlaro\Bebop\UI\Plugins\MultiContentList
	 */
	private function __createInstance($title, array $config = array())
	{	
		// Enqueue all scripts that the MultiContentList needs
		$this->__enqueueScripts();

		// Create slugified $key from $title
		$key = Bebop::util('slugify', $title);

		// Set default configuration values
		$this->__config->set(array(
			'key'   => $key,
			'title' => $title,
			'mode'  => 'default'
		));

		// Set configuration values from input
		$this->__config->set($config);

		return $this;
	}

	/**
	 * Adds a single list
	 * 
	 * @param \Ponticlaro\Bebop\UI\Plugins\ContentList $list  ContentList instance
	 */
	public function addList(\Ponticlaro\Bebop\UI\Plugins\ContentList $list, array $data = array())
	{
		// Override list data
		if ($data) $list->setData($data);

		// Store list
		$this->__lists->push($list);

		return $this;
	}

	/**
	 * Calls the internal renders function
	 * 
	 * @return object Ponticlaro\Bebop\UI\Plugins\MultiContentList
	 */
	public function render()
	{
		$this->__renderTemplate($this->__lists);

		return $this;
	}

	/**
	 * Renders template and lists
	 * 
	 * @param  \Ponticlaro\Bebop\Common\Collection $lists Lists collection
	 * @return void
	 */
	private function __renderTemplate(\Ponticlaro\Bebop\Common\Collection $lists)
	{
		include __DIR__ . '/templates/views/default/default.php';
	}
}