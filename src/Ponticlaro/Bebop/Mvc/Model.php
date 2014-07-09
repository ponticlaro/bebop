<?php

namespace Ponticlaro\Bebop\Mvc;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\Db;
use Ponticlaro\Bebop\Db\Query;

abstract class Model {

	protected static $instance;

	/**
	 * Post type name
	 * 
	 * @var string
	 */
	public static $type = 'post';

	/**
	 * Function to execute for each model item
	 * 
	 * @var callable
	 */
	protected static $init_mods;

	/**
	 * Collection of functions for context modification
	 * 
	 * @var Ponticlaro\Bebop\Common\Collection
	 */
	protected static $context_mods;

	/**
	 * Collection of functions to load optional content or apply optional modifications
	 * 
	 * @var Ponticlaro\Bebop\Common\Collection
	 */
	protected static $loadables;

	/**
	 * Contains the current query instance
	 * 
	 * @var Ponticlaro\Bebop\Db\Query
	 */
	protected static $query;

	protected static $query_mode = false;

	/**
	 * Instantiates new model by inheriting all the $post properties
	 * 
	 * @param \WP_Post $post
	 */
	final public function __construct($post = null)
	{
		if ($post instanceof \WP_Post) {
			foreach ((array) $post as $key => $value) {
				$this->{$key} = $value;
			}
		}
	}

	/**
	 * Sets the post type name
	 * 
	 * @param string $type
	 */
	public static function setType($type)
	{
		if (is_string($type))
			static::$type = $type;
	}

    /**
     * Adds a single function to load optional content or apply optional modifications 
     * 
     * @param string   $id Loadable ID
     * @param callable $fn Loadable function
     */
	public static function addLoadable($id, $fn)
	{
		if (is_null(static::$loadables)) static::$loadables = Bebop::Collection();
		
		static::$loadables->set($id, $fn);
	}

	/**
	 * Executes loadables by loadable ID
	 * 
	 * @param array $ids List of loadables IDs
	 */
	public function load(array $ids = array())
	{
		if (!is_null(static::$loadables)) {

			foreach ($ids as $id) {
				
				if (static::$loadables->hasKey($id))		
					call_user_func_array(static::$loadables->get($id), array($this));
			}
		}

		return $this;
	}

	/**
	 * Sets a function to be executed for every single item,
	 * right after being fetched from the database
	 * 
	 * @param  callable $fn Function to be executed
	 */
	public static function onInit($fn)
	{
		if (is_callable($fn))
			static::$init_mods = $fn;

		return $this;
	}

    /**
     * Adds a function to execute when the target context key is active
     * 
     * @param string $context_key Target context key
     * @param string $fn          Function to execute
     */
    public static function onContext($context_keys, $fn)
    {	
        if (is_callable($fn)) {

        	if (is_null(static::$context_mods)) static::$context_mods = Bebop::Collection();

            if (is_string($context_keys)) {
               
                static::$context_mods->set($context_keys, $fn);
            }

            elseif (is_array($context_keys)) {
                
                foreach ($context_keys as $context_key) {
                   
                    static::$context_mods->set($context_key, $fn);
                }
            }
        }

        return $this;
    }

	/**
	 * Apply all post modifications 
	 * 
	 * @param  \WP_Post $post
	 * @return object         Instance of the current class
	 */
	private static function __applyModelMods(\WP_Post $post)
	{	
		$class = get_called_class();
		$item  = new $class($post);
		
		self::__applyInitMods($item);
		self::__applyContextMods($item);

		return $item;
	}

	/**
	 * Calls the function that applies initialization modifications
	 * 
	 * @param  object $item Object to be modified
	 * @return void
	 */
	private static function __applyInitMods(&$item)
	{
		if (!is_null(static::$init_mods))
			call_user_func_array(static::$init_mods, array($item));
	}

	/**
	 * 
     * Executes any function that exists for the current context
     * 
	 * @param class $item WP_Post instance converted into an instance of the current class
	 */
    protected function __applyContextMods(&$item)
    {
        // Get current environment
        $context_key = Bebop::Context()->getCurrent();

        // Execute current environment function
        if (!is_null(static::$context_mods)) {

        	// Exact match for the current environment
        	if (static::$context_mods->hasKey($context_key)) {
        		
            	call_user_func_array(static::$context_mods->get($context_key), array($item));
	        } 

	        // Check for partial matches
	        else {

	        	foreach (static::$context_mods->get() as $key => $fn) {
        		
	        		if (Bebop::Context()->is($key))
	        			call_user_func_array($fn, array($item));
	        	}
	        }
        }
    }

	/**
	 * Queries database for several items
	 * 
	 * @param  array $args    The same query args valid for Ponticlaro\Bebop\Db::queryPosts()
	 * @param  array $options List of options for Ponticlaro\Bebop\Db::queryPosts()
	 * @return array
	 */
	public static function query(array $args = array(), array $options = array())
	{	
		$args  = self::__mergeQueryArgs($args);
		$items = Db::queryPosts($args, $options);

		// Apply model modifications
		if ($items) {
			foreach ($items as $key => $item) {

				$items[$key] = static::__applyModelMods($item);
			}
		}

		return $items;
	}

	/**
	 * Finds a single entry by ID
	 * 
	 * @param  int    $id [description]
	 * @return object     Instance of the child class
	 */
	public static function find($id = null)
	{
		if ($id && intval($id) !== 0) {

			$post = get_post($id);
		}

		elseif(Bebop::Context()->is('single')) {

			global $post;
		}

		if (isset($post) && $post instanceof \WP_Post && self::__isValidType($post)) {

			return self::__applyModelMods($post);
		}

		return null;
	}

	public static function __callStatic($name, $args)
	{
		if (is_null(static::$instance)) {

			$class          = get_called_class();
			static::$instance = new $class;
		}

		if (is_null(static::$query)) {

			static::$query      = new Query;
			static::$query_mode = true;
		}

		call_user_method_array($name, static::$query, $args);

		return static::$instance;
	}

	public function __call($name, $args)
	{
		if (static::$query_mode)
			call_user_method_array($name, static::$query, $args);

		return static::$instance;
	}

	public function findAll()
	{	
		if (is_null(static::$query)) {

			static::$query      = new Query;
			static::$query_mode = true;
		}
		
		static::$query->type(static::$type);

		$items = static::$query->findAll();
		$meta  = static::$query->getMeta();

		static::$query      = null;
		static::$query_mode = false;

		// Apply model modifications
		if ($items) {
			foreach ($items as $key => $item) {

				$items[$key] = static::__applyModelMods($item);
			}
		}

		return $items;
	}

	/**
	 * Checks if a WP_Post have the correct post type
	 * 
	 * @param  \WP_Post $post Post to be checked
	 * @return boolean        True if it has the correct post type, false otherwise
	 */
	private static function __isValidType(\WP_Post $post)
	{
		return $post->post_type == static::$type ? true : false;
	} 

	/**
	 * Merges user args with unmodifiable post type
	 * 
	 * @param  array $args Query args
	 * @return array
	 */
	private static function __mergeQueryArgs(array $args = array())
	{
		return array_merge($args, array('post_type' => static::$type));
	}
}