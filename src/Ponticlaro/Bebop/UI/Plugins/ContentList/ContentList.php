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

		$args = func_get_args();

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

	private function __createInstance($key, $data = array(), array $config = array())
	{	
		$this->__enqueueScripts();

		$label = $key;
		$key   = Bebop::util('slugify', $key);

		$default_config = array(
			'key'               => $key,
			'field_name'        => $key,
			'label__add_button' => 'Add Item',
			'form_before_list'  => true,
			'form_after_list'   => true,
			'data'              => is_array($data) ? $data : array(),
			'browse_view'       => '',
			'edit_view'         => '',
			'type'              => 'single',
			'mode'              => 'default'
		);

		$this->config = Bebop::Collection(array_merge($default_config, $config));

		return $this;
	}

	private function __renderTemplate($template_name, $config)
	{
		include __DIR__ . '/templates/'. $template_name .'.php';
	}

	public function setLabel($key, $value)
	{
		$this->config->set('label_'.$key, $value);

		return $this;
	}

	public function setMode($mode)
	{
		$this->config->set('mode', $mode);

		if ($mode == 'gallery') {
			$this->config->set('label__add_button', 'Add image');
		}

		return $this;
	}

	public function setItemView($view, $template)
	{
		if(!$view) return $this;

		if (is_callable($template)) {

			ob_start();
			call_user_func($template);
			$html = ob_get_contents();
			ob_clean();

		} elseif (is_file($template) && is_readable($template)) {

			$html = file_get_contents($template);

		} elseif (is_string($template)) {

			$html = $template;

		} else {

			$html = '';
		}

		$this->config->set($view .'_view', $html);

		return $this;
	}

	public function render()
	{
		$this->template = $this->config->get('type') .'/'. $this->config->get('mode');

		$this->__renderTemplate($this->template, $this->config);

		return $this;
	}
}