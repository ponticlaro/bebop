<?php

namespace Ponticlaro\Bebop;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\Helpers\MetaboxData;
use Ponticlaro\Bebop\Patterns\TrackableObjectAbstract;

class Metabox extends TrackableObjectAbstract
{
	protected $__type = 'metabox';

	protected $__config;

	protected $__post_types;

	protected $__meta_fields;

	protected $__fields;

	protected $__data;

	public function __construct($title, $post_types, array $meta_fields = array(), $fn = null, array $config = array())
	{
		// Take any necessary actions to make this object usable
		$this->__preInit();

		// Initialize class
		call_user_func_array(array($this, '__init'), func_get_args());
	}

	private function __preInit()
	{
		$default_config = array(
			'id'            => '',
			'title'         => '',
			'callback'      => '',
			'post_type'     => '',
			'context'       => 'normal',
			'priority'      => 'default',
			'callback_args' => null
 		);

		$this->__config      = Bebop::Collection($default_config);
		$this->__post_types  = Bebop::Collection();
		$this->__meta_fields = Bebop::Collection();
		$this->__fields      = Bebop::Collection();
		$this->__data        = new MetaboxData;
	}

	private function __init()
	{
		call_user_func_array(array($this, '__handleInit'), func_get_args());

		add_action("add_meta_boxes", array($this, 'register'));

		add_action('save_post', array($this, 'saveMeta'));

		return $this;
	}

	private function __handleInit($title, $post_types, array $meta_fields = array(), $fn = null,  array $config = array())
	{
		if (!isset($title) || !is_string($title)) 
			throw new ErrorException("You must set a title for the metabox and it must be a string");

		$this->__config->set('title', $title);

		// Set post_type id
		$this->__id = Bebop::util('slugify', $title);
		$this->__config->set('key', $this->__id);

		if (!isset($post_types)) 
			throw new ErrorException("You must define post types for this metabox");

		if (is_string($post_types)) {

			$this->__post_types->push($post_types);

		} elseif(is_object($post_types) && is_a($post_types, 'Ponticlaro\Bebop\PostType')) {

			$key = $post_types->getConfig('key');
			$this->__post_types->push($key);

		} elseif(is_array($post_types) && !empty($post_types)) {

			foreach ($post_types as $post_type) {
				
				if (is_string($post_type)) {

					$this->__post_types->push($post_type);

				} elseif(is_object($post_type) && is_a($post_type, 'Ponticlaro\Bebop\PostType')) {

					$key = $post_type->getConfig('key');
					$this->__post_types->push($key);
				}
			}
		}	

		if (isset($meta_fields) && is_array($meta_fields)) {

			$this->__meta_fields->set($meta_fields);
		}

		if ($fn) {

			if (is_callable($fn)) {

				$this->__config->set('callback', $fn);

			} elseif(is_array($fn)) {

				if (isset($fn[0]) && is_callable($fn)) {

					$this->__config->set('callback', $fn[0]);
				}

				if (isset($fn[1]) && is_array($fn[1])) {

					$this->__config->set('callback_args', $fn[1]);
				}
			}	

		} elseif (is_array($fn) && !$config) {
			
			$config = $fn;
		}

		if ($config) { 

			foreach ($config as $key => $value) {

				$this->__config->set($key, $value);
			}
		}

		$this->__validateConfig();
	}

	private function __validateConfig()
	{
		if (!$this->__config->get('id')) {

			$key = $this->__config->get('key');
			$this->__config->set('id', $key);
		}
	}

	public function __callbackWrapper($entry, $metabox)
	{	
		$id           = $this->__config->get('id');
		$callback     = $this->__config->get('callback');
		$meta_fields  = $this->__meta_fields->get();
		$fields       = $this->__fields->get();
		
		//wp_nonce_field( basename( __FILE__ ), $id);

		if($callback){

			if($meta_fields) {

				$meta = Bebop::PostMeta($entry->ID);

				foreach ($meta_fields as $meta_field) {

					$this->__data->set($meta_field, $meta->get($meta_field));
				}
			}

			$callback($this->__data, $entry, $metabox);

		} else {

			if($fields) {

				foreach ($fields as $field) {
					
					$field->render();
				}
			}
		}
	}

	public function register()
	{
		$post_types = $this->__post_types->get();

		foreach ($post_types as $post_type) {

			add_meta_box( 

				$this->__config->get('id'), 
				$this->__config->get('title'), 
				array($this, '__callbackWrapper'),
				$post_type, 
				$this->__config->get('context'),  
			    $this->__config->get('priority'), 
			    $this->__config->get('callback_args') 
			);
		}
	}

	public function metaValue($key) 
	{
		echo $this->__data->get($key);
	}

	public function saveMeta($post_id) 
	{
		global $wpdb;

		$post        = get_post($post_id);
		$post_type   = $post->post_type;
		$meta_fields = $this->__meta_fields->get();

		if( isset($_POST['post_type']) && $_POST['post_type'] == $post_type ) {

			if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;

			if (!current_user_can('edit_post', $post->ID)) return $post_id;

			// if our nonce isn't there, or we can't verify it, bail 
   			//if( !isset( $_POST['meta_box_nonce'] ) || !wp_verify_nonce( $_POST['meta_box_nonce'], 'my_meta_box_nonce' ) ) return; 

			foreach($meta_fields as $field) {

				$value = isset($_POST[$field]) ? $_POST[$field] : '';

				// Empty values
				if (!$value) {
					
					delete_post_meta($post_id, $field);
				}

				// Arrays
				elseif (is_array($value)) {
					
					// Delete all entries
					delete_post_meta($post_id, $field);

					foreach ($value as $v) {

						// Add single entry with same meta_key
						add_post_meta($post_id, $field, $v);
					}
				}

				// Strings, booleans, etc
				else {

					update_post_meta($post_id, $field, $value);
				}
			}
		}
	}

	private function __addField($field_type, $label, $config)
	{
		//$field = Field::factory($field_type, $label, $config);

		//$key = $field->getKey();

		//$this->__fields->set($key, $field);
		//$this->__meta_fields->set($key, $field);
	}

	public function __call($name, $args)
    {
	    //////////////////////////////
    	// Check for correct action //
	    //////////////////////////////
    	$is_add    = substr($name, 0, 3) == 'add' ? true : false;
		$is_get    = substr($name, 0, 3) == 'get' ? true : false;
		$is_set    = substr($name, 0, 3) == 'set' ? true : false;
		$is_remove = substr($name, 0, 6) == 'remove' ? true : false;
		//$is_add_field = in_array(strtolower($name), Bebop::getAvailableFields());

		/////////////////////
		// Add Form Fields //
		/////////////////////
   		// if($is_add_field){

			// if(!isset($args[0])) throw new ErrorException("You must set the label for this field");

			// $field_type = $name;
			// $label      = $args[0];
			// $config     = isset($post_types) ? $post_types : array();

			// $this->__addField($field_type, $label, $config);

  		 // }

		/////////
		// Add //
		/////////
		if ($is_add) {

			$is_addMetaField = substr($name, 3, strlen($name)) == 'MetaField' ? true : false;

			if ($is_addMetaField) {

				if (isset($args[0])) $this->__meta_fields->push($args[0]);
			}
		}

	    /////////
    	// Set //
	    /////////
    	if ($is_set) {

			$is_setConfig   = substr($name, 3, 6) == 'Config' ? true : false;
			$is_setPostType = substr($name, 3, 8) == 'PostType' ? true : false;

			if ($args[0]) {

				if (is_string($args[0])) {

					$key   = $args[0];
					$value = isset($post_types) ? $post_types : null;

					if($is_setConfig) $this->__config->set($key, $value);
					if($is_setPostType) $this->__post_types->set($key, $value);

				} elseif(is_array($args[0])) {

					if($is_setConfig) $this->__config->set($args[0]);
					if($is_setPostType) $this->__post_types->set($args[0]);
				}
			}
    	}

	    ///////////
    	// Unset //
	    ///////////
    	if ($is_remove) {

			$is_removeConfig   = substr($name, 6, 6) == 'Config' ? true : false;
			$is_removePostType = substr($name, 6, 8) == 'PostType' ? true : false;

			if ($args[0]) {

				if (is_string($args[0])) {

					$key   = $args[0];
					$value = isset($post_types) ? $post_types : null;

					if($is_removeConfig) $this->__config->remove($key, $value);
					if($is_removePostType) $this->__post_types->remove($key, $value);

				} elseif( is_array($args[0]) ){

					if($is_removeConfig) $this->__config->remove($args[0]);
					if($is_removePostType) $this->__post_types->remove($args[0]);
				}
			}
    	}

	    /////////
    	// Get //
	    /////////
    	if ($is_get) {

			$is_getConfig   = substr($name, 3, 6) == 'Config' ? true : false;
			$is_getPostType = substr($name, 3, 8) == 'PostType' ? true : false;

			$keys = isset($args[0]) ? $args[0] : null;

			if($is_getConfig) $results = $this->__config->get($keys);
			if($is_getPostType) $results = $this->__post_types->get($keys);

    		return $results ? $results : null;
    	}
    }
}