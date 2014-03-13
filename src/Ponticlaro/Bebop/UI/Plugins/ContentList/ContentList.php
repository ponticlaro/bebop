<?php

namespace Ponticlaro\Bebop\UI\Plugins;

use Ponticlaro\Bebop;

class ContentList extends \Ponticlaro\Bebop\UI\PluginAbstract {

	/**
	 * Identifier Key to call this plugin
	 * 
	 * @var string
	 */
	protected static $__key = 'List';

	protected static $__base_url;

	protected $__instances;

	protected $__current_instance_key;

	public function __construct()
	{
		self::$__base_url = Bebop::getPathUrl(__DIR__);

		$this->__instances = Bebop::Collection();
	}

	/**
	 * This function will register everything on the right hooks
	 * when the plugin is added to Bebop::UI
	 *  
	 * @return void
	 */
	public function load()
	{
		add_action('admin_enqueue_scripts', array($this, 'registerScripts'));
	}

	public function registerScripts()
	{
		wp_register_style('bebop-ui--list', self::$__base_url .'/assets/css/bebop-ui--list.css');

		wp_register_script('bebop-ui--listView', self::$__base_url .'/assets/js/views/List.js', array(), false, true);
		wp_register_script('bebop-ui--listItemView', self::$__base_url .'/assets/js/views/ListItemView.js', array(), false, true);
		wp_register_script('bebop-ui--listItemModel', self::$__base_url .'/assets/js/models/ListItemModel.js', array(), false, true);
		wp_register_script('bebop-ui--listCollection', self::$__base_url .'/assets/js/collections/ListCollection.js', array(), false, true);

		$app_dependencies = array(
			'jquery',
			'underscore',
			'backbone',
			'bebop-ui--listView',
			'bebop-ui--listItemView',
			'bebop-ui--listItemModel',
			'bebop-ui--listCollection'
		);		
		wp_register_script('bebop-ui--list', self::$__base_url .'/assets/js/bebop-ui--list.js', $app_dependencies, false, true);
	}

	private function __enqueueScripts()
	{
		global $wp_version;

		if (version_compare($wp_version, '3.5', '>=')) {
			
		} else {

			// Handle WordPress lower than 3.5
		}

		wp_enqueue_style('bebop-ui--list');
		wp_enqueue_script('bebop-ui--list');
	}

	public function single($key, array $config = array(), $fn)
	{	
		if (!is_callable($fn))
			throw new \Exception("Third parameter must be callable", 1);

		$this->__enqueueScripts();

		$label = $key;
		$key   = Bebop::util('slugify', $key);

		$default_config = array(
			'key'                    => $key,
			'field_name'             => $key,
			'fn'                     => $fn,
			'data'                   => array(),
			'form__show_fields'      => false,
			'add_button_text'        => 'Add item',
			'form__fields'           => array(),
			'list__is_draggable'     => true,
			'list__is_empty_message' => 'List have no items'
		);

		$instance           = new \stdClass;
		$instance->template = 'single';
		$config             = array_merge($default_config, $config);
		$instance->config   = Bebop::Collection($config);

		// Save reference to this instance
		$this->__current_instance_key = $key;
		$this->__instances->set($key, $instance);

		return $this;
	}

	private function __renderTemplate($template_name, $config)
	{
		include __DIR__ . '/templates/'. $template_name .'.php';
	}

	public function render()
	{
		$current_instance = $this->__instances->get($this->__current_instance_key);
		$template_name    = $current_instance->template;
		$config           = $current_instance->config;

		$this->__renderTemplate($template_name, $config);

		return $this;
	}
}