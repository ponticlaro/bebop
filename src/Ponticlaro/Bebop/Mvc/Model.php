<?php

namespace Ponticlaro\Bebop\Mvc;

use Ponticlaro\Bebop;

abstract class Model {

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
	 * Instantiates new model by inheriting all the $post properties
	 * 
	 * @param \WP_Post $post
	 */
	final public function __construct(\WP_Post $post)
	{
		foreach ((array) $post as $key => $value) {
			$this->{$key} = $value;
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
	 * Queries database for several items
	 * 
	 * @param  array $args    The same query args valid for Ponticlaro\Bebop\Db::queryPosts()
	 * @param  array $options List of options for Ponticlaro\Bebop\Db::queryPosts()
	 * @return array
	 */
	public static function query(array $args = array(), array $options = array())
	{	
		$args  = self::__mergeQueryArgs($args);
		$items = \Ponticlaro\Bebop\Db::queryPosts($args, $options);

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
	public static function find($id)
	{
		if (intval($id) !== 0) {

			$post = get_post($id);

			if ($post instanceof \WP_Post && self::isValidType($post)) {

				return self::__applyModelMods($post);
			}
		}

		return null;
	}

	/**
	 * Checks if a WP_Post have the correct post type
	 * 
	 * @param  \WP_Post $post Post to be checked
	 * @return boolean        True if it has the correct post type, false otherwise
	 */
	public static function isValidType(\WP_Post $post)
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
}