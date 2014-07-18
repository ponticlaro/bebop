<?php

namespace Ponticlaro\Bebop;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\Patterns\TrackableObjectAbstract;

class Taxonomy extends TrackableObjectAbstract
{
	protected $__trackable_type = 'taxonomy';

	protected $__config;

	protected $__labels;

	protected $__post_types;

	public function __construct($name, $post_types, array $config = array())
	{
		// Take any necessary actions to make this object usable
		$this->__preInit();

		// Initialize class
		call_user_func_array(array($this, '__init'), func_get_args());
	}

	private function __preInit()
	{	
		$default_config = array(
			'hierarchical'      => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'query_var'         => true
		);

		$this->__config     = Bebop::Collection( $default_config );
		$this->__labels     = Bebop::Collection();
		$this->__post_types = Bebop::Collection();
	}

	private function __init()
	{
		call_user_func_array(array($this, '__handleInit'), func_get_args());

		add_action("init", array($this, 'register'));

		return $this;
	}

	private function __handleInit($name, $post_types, array $config = array())
	{   
		if( !isset($name) ) throw new ErrorException("You must set a name for the post type");

		if( is_string($name) ){
			$this->__config->set('singular_name', $name);

		} elseif( is_array($name) ){

			if( isset($name[0]) ){
				$this->__config->set('singular_name', $name[0]);
			}

			if( isset($name[1]) ){
				$this->__config->set('plural_name', $name[1]);
				$this->__config->set('label', $name[1]);
			}

		} 

		// Set post_type id
		$this->__trackable_id = Bebop::util('slugify', $this->__config->get('singular_name') );
		$this->__config->set('key', $this->__trackable_id);

		// Post types to be associated with
		if( isset($post_types) ) {	

			if( is_string($post_types)){

				$this->__post_types->push($post_types);


			} elseif( is_object($post_types) && is_a($post_types, 'Ponticlaro\Bebop\PostType') ){

				$key = $post_types->getConfig('key');
				$this->__post_types->push($key);


			} elseif( is_array($post_types) && !empty($post_types) ){

				foreach ($post_types as $post_type) {
					
					if( is_string($post_type)){
						$this->__post_types->push($post_type);

					} elseif( is_object($post_type) && is_a($post_type, 'Ponticlaro\Bebop\PostType') ){

						$key = $post_type->getConfig('key');
						$this->__post_types->push($key);

					}
					
				}

			}		
		}	

		// Handle configuration arguments
		if( isset($config) ) 
		{	
			if( is_array($config) ) {

				// Intercept labels
				if( isset($config['labels']) ){

					$this->__labels->set( $config['labels'] );
					unset($config['labels']);

				}

				$this->__config->set($config);

			} 
		}

		$this->__validateConfig();
		$this->__setLabels();
	}

	private function __validateConfig()
	{	
		if(!$this->__config->get('plural_name')){

			$plural = $this->__config->get('singular_name') .'s';
			$this->__config->set('plural_name', $plural);

		}

		if(!$this->__config->get('label')) {
			$this->__config->set('label', $this->__config->get('plural_name'));
		}

		if(!$this->__config->get('menu_name')){

			$this->__config->set('menu_name', $this->__config->get('plural_name'));

		}
	}

	private function __setLabels()
	{
		$singular = $this->__config->get('singular_name');
		$plural   = $this->__config->get('plural_name');

		$labels = array(
			'name'              => _x( $plural, 'taxonomy general name' ),
			'singular_name'     => _x( $singular, 'taxonomy singular name' ),
			'search_items'      => __( 'Search '. $plural ),
			'all_items'         => __( 'All '. $plural ),
			'parent_item'       => __( 'Parent '. $singular),
			'parent_item_colon' => __( 'Parent '. $singular .':'),
			'edit_item'         => __( 'Edit '. $singular),
			'update_item'       => __( 'Update '. $singular),
			'add_new_item'      => __( 'Add New '. $singular),
			'new_item_name'     => __( 'New '. $singular .' Name' ),
			'menu_name'         => __(  $singular ),
		);

		$labels = array_merge($labels, $this->__labels->get());

		$this->__labels->set($labels);
	}

	public function register()
	{
		$config           = $this->__config->get();
		$config['labels'] = $this->__labels->get();
		$post_types       = $this->__post_types->get();

		register_taxonomy($config['key'], $post_types, $config);
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
    	if($is_set){

			$is_setConfig   = substr($name, 3, 6) == 'Config' ? true : false;
			$is_setLabel    = substr($name, 3, 5) == 'Label' ? true : false;
			$is_setPostType = substr($name, 3, 8) == 'PostType' ? true : false;

			if($args[0]){

				if( is_string($args[0])){

					$key   = $args[0];
					$value = isset($args[1]) ? $args[1] : null;

					if($is_setConfig) $this->__config->set($key, $value);
					if($is_setLabel) $this->__labels->set($key, $value);
					if($is_setPostType) $this->__post_types->push($value);

				}elseif( is_array($args[0]) ){

					if($is_setConfig) $this->__config->set($args[0]);
					if($is_setLabel) $this->__labels->set($args[0]);
					if($is_setPostType) {

						foreach ($args[0] as $key => $value) {
						 	$this->__post_types->push($value);
						}

					}
				}

			}
    	}

    	/*---------------------------
    		Unset Stuff
    	 ---------------------------*/
    	 if($is_remove){

			$is_removeConfig   = substr($name, 6, 6) == 'Config' ? true : false;
			$is_removePostType = substr($name, 6, 8) == 'PostType' ? true : false;

			if($args[0]){

				if( is_string($args[0])){

					$key   = $args[0];
					$value = isset($args[1]) ? $args[1] : null;

					if($is_removeConfig) $this->__config->remove($key, $value);
					if($is_removePostType) $this->__post_types->pop($value);

				}elseif( is_array($args[0]) ){

					if($is_removeConfig) $this->__config->remove($args[0]);
					if($is_removePostType) {

						foreach ($args[0] as $key => $value) {
							if($is_setPostType) $this->__post_types->pop($value);
						}

					}
				}

			}
    	}

    	/*---------------------------
    		Get Stuff
    	 ---------------------------*/
    	if($is_get){

			$is_getConfig   = substr($name, 3, 6) == 'Config' ? true : false;
			$is_getLabel    = substr($name, 3, 5) == 'Label' ? true : false;
			$is_getPostType = substr($name, 3, 8) == 'PostType' ? true : false;

			$keys = isset($args[0]) ? $args[0] : null;

			if($is_getConfig) $results = $this->__config->get($keys);
  			if($is_getLabel) $results = $this->__labels->get($keys);
			if($is_getPostType) $results = $this->__post_types->get();

    		return $results ? $results : null;
    	}
    }

}