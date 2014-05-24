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

	protected $__current_instance_key;

	public function __construct()
	{
		self::$__base_url = Bebop::getPathUrl(__DIR__);

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
		$app_css_dependencies = array(
			'bebop-ui'
		);

		wp_register_style('bebop-ui--list', self::$__base_url .'/assets/css/bebop-ui--list.css', $app_css_dependencies);

		wp_register_script('bebop-ui--listView', self::$__base_url .'/assets/js/views/List.js', array(), false, true);
		wp_register_script('bebop-ui--listItemView', self::$__base_url .'/assets/js/views/ListItemView.js', array(), false, true);
		wp_register_script('bebop-ui--childListView', self::$__base_url .'/assets/js/views/ChildListView.js', array(), false, true);
		wp_register_script('bebop-ui--listItemModel', self::$__base_url .'/assets/js/models/ListItemModel.js', array(), false, true);
		wp_register_script('bebop-ui--listCollection', self::$__base_url .'/assets/js/collections/ListCollection.js', array(), false, true);

		$app_dependencies = array(
			'jquery',
			'jquery-ui-sortable',
			'underscore',
			'backbone',
			'mustache',
			'bebop-ui--listView',
			'bebop-ui--listItemView',
			'bebop-ui--childListView',
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

		$title = $key;
		$key   = Bebop::util('slugify', $key);

		// Default main configuration
		$default_config = array(
			'key'              => $key,
			'title'            => $title,
			'description'      => '',
			'field_name'       => $key,
			'show_top_form'    => true,
			'show_bottom_form' => true,
			'type'             => 'single',
			'mode'             => 'default'
		);

		// Main configuration
		$this->config = Bebop::Collection(array_merge($default_config, $config));

		// Data
		$this->data = Bebop::Collection($data);

		// Views
		$this->views = Bebop::Collection(array(
			'browse'  => '',
			'reorder' => '',
			'edit'    => ''
		));

		// Labels
		$this->labels = Bebop::Collection(array(
			'add_button'  => 'Add Item',
			'sort_button' => 'Sort'
		));

		// Form elements
		$this->form_elements = Bebop::Collection(array(
			'add'  => __DIR__ .'/templates/partials/form/default/elements/add.php',
			'sort' => __DIR__ .'/templates/partials/form/default/elements/sort.php'
		));

		return $this;
	}

	public function setTitle($title)
	{
		if (is_string($title)) 
			$this->config->set('title', $title);

		return $this;
	}

	public function setDescription($description)
	{
		if (is_string($description)) 
			$this->config->set('description', $description);

		return $this;
	}

	public function setFieldName($name)
	{
		if (is_string($name)) 
			$this->config->set('field_name', $name);

		return $this;
	}

	public function setLabel($key, $value)
	{	
		if (is_string($key) && is_string($value)) 
			$this->labels->set($key, $value);

		return $this;
	}

	public function getLabel($key)
	{	
		if (!is_string($key)) return '';

		return $this->labels->get($key);
	}

	public function setMode($mode)
	{	
		if (is_string($mode)) {

			$this->config->set('mode', $mode);

			if ($mode == 'gallery') {
				$this->config->set('label__add_button', 'Add images');
			}
		}

		return $this;
	}

	public function setItemView($view, $template)
	{
		if(!is_string($view)) return $this;

		$this->views->set($view, $this->__getHtml($template));

		return $this;
	}

	public function getItemView($view)
	{
		if(!is_string($view)) return $this;

		return $this->views->get($view);
	}

	public function getAllItemViews()
	{
		return $this->views->get();
	}

	public function clearForm()
	{
		$this->form_elements->clear();

		return $this;
	}

	public function addFormEl($element_id, $template)
	{
		$this->appendFormEl($element_id, $template);

		return $this;
	}

	public function prependFormEl($element_id, $template)
	{
		$this->form_elements->unshift($element_id, $template);

		return $this;
	}

	public function appendFormEl($element_id, $template)
	{
		$this->form_elements->set($element_id, $template);

		return $this;
	}

	public function replaceFormEl($element_id, $template)
	{
		$this->form_elements->set($element_id, $template);

		return $this;
	}

	public function removeFormEl($element_id)
	{
		$this->form_elements->remove($element_id);

		return $this;
	}

	public function showForms($value)
	{
		$this->showTopForm($value);
		$this->showBottomForm($value);

		return $this;
	}

	public function showTopForm($value)
	{
		if (is_bool($value))
			$this->config->set('show_top_form', $value);

		return $this;
	}

	public function showBottomForm($value)
	{
		if (is_bool($value))
			$this->config->set('show_bottom_form', $value);

		return $this;
	}

	public function getForm()
	{
		$html     = '';
		$elements = $this->form_elements->get();

		if ($elements) {
			
			foreach ($elements as $element_id => $element_tpl) {
				
				$html .= "<div bebop-list--formElementId='$element_id' class='bebop-list--formField'>";
				$html .= $this->__getHtml($element_tpl);
				$html .= '</div>';
			}
		}

		return $html;
	}

	private function __getHtml($source) 
	{
		if (is_callable($source)) {

			ob_start();
			call_user_func($source);
			$html = ob_get_contents();
			ob_clean();

		} elseif (is_file($source) && is_readable($source)) {

			ob_start();
			$this->__renderTemplate($source, $this);
			$html = ob_get_contents();
			ob_clean();

		} elseif (is_string($source)) {

			$html = $source;

		} else {

			$html = '';
		}

		return $html;
	}

	public function render()
	{
		// Add default reorder view if in gallery mode
		if ($this->config->get('mode') == 'gallery') {
			
			$this->setLabel('add_button', 'Add Images');
			$this->setItemView('reorder', __DIR__ .'/templates/partials/items/gallery/reorder.mustache');
		}

		// Set path to template
		$this->template = 'views/'. $this->config->get('type') .'/'. $this->config->get('mode');

		// Render list
		$this->__renderTemplate($this->template, $this);

		return $this;
	}

	private function __renderTemplate($template_name, $instance)
	{
		// Absolute path templates
		if (is_file($template_name) && is_readable($template_name)) {
			
			include $template_name;
		}

		// Main View Templates
		else {

			include __DIR__ . '/templates/'. $template_name .'.php';
		}
	}
}