<?php

namespace Ponticlaro\Bebop\UI\Plugins;

use Ponticlaro\Bebop;

class Media extends \Ponticlaro\Bebop\UI\PluginAbstract {

	/**
	 * Identifier Key to call this plugin
	 * 
	 * @var string
	 */
	protected static $__key = 'Media';

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
		wp_register_style('bebop-ui--media', self::$__base_url .'/assets/css/bebop-ui--media.css', array('bebop-ui'));
		
		wp_register_script('bebop-ui--mediaView', self::$__base_url .'/assets/js/views/Media.js', array(), false, true);

		$app_dependencies = array(
			'jquery',
			'jquery-ui-sortable',
			'underscore',
			'backbone',
			'bebop-ui',
			'bebop-ui--mediaView'
		);		
		wp_register_script('bebop-ui--media', self::$__base_url .'/assets/js/bebop-ui--media.js', $app_dependencies, false, true);
	}

	private function __enqueueScripts()
	{
		global $wp_version;

		if (version_compare($wp_version, '3.5', '>=')) {
			
			wp_enqueue_media();
			
		} else {

			// Handle WordPress lower than 3.5
		}

		wp_enqueue_style('bebop-ui--media');
		wp_enqueue_script('bebop-ui--media');
	}

	public function single($key, $data, array $config = array())
	{	
		$this->__enqueueScripts();

		$label = $key;
		$key   = Bebop::util('slugify', $key);

		$default_config = array(
			'key'                  => $key,
			'field_name'           => $key,
			'select_button_class'  => '',
			'select_button_text'   => 'Select '. $label,
			'remove_button_class'  => '',
			'remove_button_text'   => 'Remove '. $label,
			'no_selection_message' => 'No selected item',  
			'modal_title'          => 'Upload or select existing resources',
			'modal_button_text'    => 'Select '. $label,
			'mime_types'           => array()
		);

		$instance           = new \stdClass;
		$instance->template = 'single';
		$config             = array_merge($default_config, $config);
		$instance->config   = Bebop::Collection($config);
		$instance->config->set('data', $data);

		// Save reference to this instance
		$this->__current_instance_key = $key;
		$this->__instances->set($key, $instance);

		return $this;
	}

	public function gallery($key, $data, array $config = array())
	{
		$this->__enqueueScripts();

		$label = $key;
		$key   = Bebop::util('slugify', $key);

		$default_config = array(
			'key'                  => $key,
			'label'                => $label,
			'container_id'         => 'bebop-media__single-'. $key . '-container',
			'display_label'        => true,
			'field_name'           => $key,
			'select_button_class'  => '',
			'select_button_text'   => 'Add image',
			'no_selection_message' => 'No selected images',  
			'modal_title'          => 'Upload or select existing resources',
			'modal_button_text'    => 'Add',
			'mime_types'           => array('image'),
			'multiple'             => true
		);

		$instance           = new \stdClass;
		$instance->template = 'gallery';
		$config             = array_merge($default_config, $config);
		$instance->config   = Bebop::Collection($config);
		$instance->config->set('data', $data);

		// Save reference to this instance
		$this->__current_instance_key = $key;
		$this->__instances->set($key, $instance);

		return $this;
	}

	private function __renderTemplate($template_name, $data)
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