<?php

namespace Ponticlaro\Bebop;

use Ponticlaro\Bebop;

class PostType
{
	private $__config;

	private $__supports;

	private $__labels;

	private $__taxonomies;

	private $__metaboxes;

	public function __construct($name, array $config = array())
	{
		// Take any necessary actions to make this object usable
		$this->__preInit();

		// Initialize class
		call_user_func_array(array($this, '__init'), func_get_args());
	}

	private function __preInit()
	{
		$default_config = array(
			'auto_register'      => true,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true, 
			'query_var'          => true,
		);

		$default_support = array( 
			'title',
			'editor'
		);

		$this->__config   = Bebop::Collection($default_config);
		$this->__supports = Bebop::Collection($default_support); 
		$this->__labels   = Bebop::Collection();
	}

	private function __init()
	{
		call_user_func_array(array($this, '__handleInit'), func_get_args());

		if ($this->__config->get('auto_register')) {

			$this->register();
		}

		if(class_exists('\Ponticlaro\Bebop')){

			// Keep track of this object on Bebop
			Bebop::track($this);
			$key = $this->getConfig('key');
			unset($this);

			return Bebop::getPostType($key);

		} else {

			return $this;
		}
	}

	private function __handleInit($name, array $config = array())
	{
		if (!isset($name)) 
			throw new ErrorException("You must set a name for the post type");

		if (is_string($name)) {

			$this->__config->set('singular_name', $name);

		} elseif(is_array($name)) {

			if (isset($name[0])) {

				$this->__config->set('singular_name', $name[0]);
			}

			if (isset($name[1])) {

				$this->__config->set('plural_name', $name[1]);
			}
		} 

		// Set post_type key
		$key = Bebop::util('slugify', $this->__config->get('singular_name') );
		$this->__config->set('key', $key);

		// Handle configuration arguments
		if ($config) {	

			// Intercept labels
			if (isset($config['labels']) && is_array($config['labels'])) {

				$this->__labels->set($config['labels']);
				unset($config['labels']);
			}

			// Intercept supports
			if (isset($config['supports']) && is_array($config['supports'])) {

				foreach ($config['supports'] as $support) {

					if (!$this->__supports->hasValue($support)) {
						
						$this->__supports->push($support);
					}
				}

				unset($config['supports']);
			}

			$this->__config->set($config);
		}	

		$this->__validateConfig();
		$this->__setLabels();
	}

	private function __validateConfig()
	{
		if (!$this->__config->get('plural_name')) {

			$plural = $this->__config->get('singular_name') .'s';
			$this->__config->set('plural_name', $plural);
		}

		if (!$this->__config->get('menu_name')) {

			$this->__config->set('menu_name', $this->__config->get('plural_name'));
		}
	}

	private function __setLabels()
	{
		$singular = $this->__config->get('singular_name');
		$plural   = $this->__config->get('plural_name');

		$labels = array(
			'name'               => __($plural),
			'singular_name'      => __($singular),
			'menu_name'          => __($plural),
			'all_items'          => __($plural),
			'add_new'            => __('Add '. $singular),
			'add_new_item'       => __('Add new '. $singular), 
			'edit_item'          => __('Edit '. $singular), 
			'new_item'           => __('New '. $singular),
			'view_item'          => __('View '. $singular),
			'search_items'       => __('Search '. $plural),
			'not_found'          => __('There are no '. $plural),
			'not_found_in_trash' => __('There are no '. $plural .' in trash'), 
			'parent_item_colon'  => __('Parent '. $singular . ':') 
		);

		$labels = array_merge($labels, $this->__labels->get());

		$this->__labels->set($labels);
	}

	public function register()
	{
		add_action("init", array($this, '__innerRegister'), 9999999);

		return $this;
	}

	public function __innerRegister()
	{
		$config             = $this->__config->get();
		$config['labels']   = $this->__labels->get();
		$config['supports'] = $this->__supports->get();

		register_post_type($config['key'], $config);

		return $this;
	}

	public function __call($name, $args)
    {
    	/*----------------------------------
    		Check for correct main method
    	 ----------------------------------*/
		$is_get    = substr($name, 0, 3) == 'get' ? true : false;
		$is_set    = substr($name, 0, 3) == 'set' ? true : false;
		$is_remove = substr($name, 0, 6) == 'remove' ? true : false;

    	/*---------------------------
    		Set Stuff
    	 ---------------------------*/
    	if ($is_set) {

			$is_setConfig  = substr($name, 3, 6) == 'Config' ? true : false;
			$is_setLabel   = substr($name, 3, 5) == 'Label' ? true : false;
			$is_setSupport = substr($name, 3, 7) == 'Support' ? true : false;

			if ($args[0]) {

				if (is_string($args[0])) {

					$key   = $args[0];
					$value = isset($args[1]) ? $args[1] : null;

					if($is_setConfig) $this->__config->set($key, $value);
					if($is_setLabel) $this->__labels->set($key, $value);
					if($is_setSupport) $this->__supports->set($key, $value);

				} elseif(is_array($args[0])) {

					if($is_setConfig) $this->__config->set($args[0]);
					if($is_setLabel) $this->__labels->set($args[0]);
					if($is_setSupport) $this->__supports->set($args[0]);
				}
			}

			return $this;
    	}

    	/*---------------------------
    		Unset Stuff
    	 ---------------------------*/
    	 if ($is_remove) {

    	 	$is_removeConfig  = substr($name, 6, 6) == 'Config' ? true : false;
			$is_removeSupport = substr($name, 6, 7) == 'Support' ? true : false;

			if ($args[0]) {

				if(is_string($args[0])) {

					$key   = $args[0];
					$value = isset($args[1]) ? $args[1] : null;

					if($is_removeConfig) $this->__config->remove($key, $value);
					if($is_removeSupport) $this->__supports->pop($value);

				} elseif(is_array($args[0])) {

					if($is_removeConfig) $this->__config->remove($args[0]);
					if($is_removeSupport) $this->__supports->remove($args[0]);
				}
			}

			return $this;
    	}

    	/*---------------------------
    		Get Stuff
    	 ---------------------------*/
    	if ($is_get) {

    		$is_getConfig  = substr($name, 3, 6) == 'Config' ? true : false;
			$is_getLabel   = substr($name, 3, 5) == 'Label' ? true : false;
			$is_getSupport = substr($name, 3, 7) == 'Support' ? true : false;

			$keys = isset($args[0]) ? $args[0] : null;

			if($is_getConfig) $results = $this->__config->get($keys);
  			if($is_getLabel) $results = $this->__labels->get($keys);
			if($is_getSupport) $results = $this->__supports->get($keys);

    		return $results ? $results : null;
    	}
    }
}