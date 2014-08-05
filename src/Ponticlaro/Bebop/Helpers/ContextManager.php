<?php

namespace Ponticlaro\Bebop\Helpers;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\Helpers\ContextContainer;
use Ponticlaro\Bebop\Patterns\SingletonAbstract;

class ContextManager extends SingletonAbstract {

	/**
	 * Contains Context Manager instance
	 * 
	 * @var Ponticlaro\Bebop\Helpers\ContextManager
	 */
	private static $instance;

	/**
	 * Current context key
	 * 
	 * @var string
	 */
	protected static $current;

	/**
	 * Used to store the current context when
	 * manually defining the context key
	 * 
	 * @var string
	 */
	protected static $current_backup;

	/**
	 * List of Context Containers
	 *  
	 * @var Ponticlaro\Bebop\Common\Collection
	 */
	protected static $contexts;

	/**
	 * Instantiates Context Manager
	 */
	protected function __construct()
	{
		// Instantiate contexts collection
		self::$contexts = Bebop::Collection();

		// Add default context rules
		self::add('default', function($query) {

		    $key = null;

		    if (is_category()) { $key = 'tax/category'; }

		    elseif (is_tag()) { $key = 'tax/tag'; }

		    elseif (is_tax()) { $key = 'tax/'. $query->query_vars['taxonomy']; }

		    elseif (is_post_type_archive()) { $key = 'archive/'. $query->query_vars['post_type']; }
		    
		    elseif (is_date()) {
		        
		        if (is_year()) { $key = 'archive/date/year'; }

		        elseif (is_month()) { $key = 'archive/date/month'; }

		        elseif (is_day()) { $key = 'archive/date/day'; }
		    }

		    elseif (is_author()) { $key = 'archive/author'; }

		    elseif (is_singular()) {
		        
		        if (is_singular('post')) { $post_type = 'post'; } 

		        elseif (is_singular('page')) { $post_type = 'page'; }

		        else { $post_type = $query->query_vars['post_type']; }

		        $key = 'single/'. $post_type;
		    }

		    elseif (is_home()) { $key = 'home/posts'; }

		    elseif (is_front_page()) { $key = 'home/page'; }

		    elseif (is_search()) { $key = 'search'; }

		    elseif (is_404()) { $key = 'error/404'; }

		    return $key;
		});
		
		// Add action to define current context
		add_action('wp', array('Ponticlaro\Bebop\Helpers\ContextManager', 'defineCurrent'));
	}

	/**
	 * Defines current contexts by running all
	 * context containers until it finds a match
	 * 
	 * @return void
	 */
	public static function defineCurrent()
	{
		foreach (self::$contexts->getAll() as $context_container) {
			
			$key = $context_container->run();

			if ($key) {

				self::$current = $key;
				break;
			}
		}
	}

	/**
	 * Checks if the target $key is a partial match for the current context
	 * You can optionally pass a regular expression to find matches
	 * 
	 * @param  [type]  $key        Context key or regular expression
	 * @param  boolean $is_pattern True if the $key should be treated as a regular expression
	 * @return boolean             True if there is a match, false otherwise
	 */
	public static function is($key, $is_pattern = false)
	{
		$pattern = $is_pattern ? $key : '/^'. preg_quote($key, '/') .'*/';

		return preg_match($pattern, self::$current) ? true : false;	
	}

	/**
	 * Checks if the target $key is an exact match for the current context
	 * 
	 * @param  string  $key Context string to check
	 * @return boolean      True if it partially or fully matches, false otherwhise
	 */
	public static function equals($key)
	{
		return self::$current === $key ? true : false;
	}

	/**
	 * Returns current context key
	 * 
	 * @return string
	 */
	public static function getCurrent()
	{
		return self::$current;
	}

	/**
	 * Overrides current context with target $key,
	 * but also keeps a backup of the current key to later be restored
	 * 
	 * @param  string $key
	 * @return Ponticlaro\Bebop\Helpers\ContextManager Context Manager instance
	 */
	public static function overrideCurrent($key)
	{
		if (is_string($key)) {
			
			self::$current_backup = self::$current;
			self::$current        = $key;
		}

		return self::$instance;
	}

	/**
	 * Restores previois context key, if there is any
	 * 
	 * @return Ponticlaro\Bebop\Helpers\ContextManager Context Manager instance
	 */
	public static function restoreCurrent()
	{
		if (!is_null(self::$current_backup)) {
			
			self::$current        = self::$current_backup;
			self::$current_backup = null;
		}

		return self::$instance;
	}

	/**
	 * Returns single context container by ID
	 * 
	 * @param  string                                    $id ID of the target context container
	 * @return Ponticlaro\Bebop\Helpers\ContextContainer     Target context container
	 */
	public static function get($id)
	{
		return self::__getContextById($id);
	}

	/**
	 * Adds a single context container to the top of the list
	 * 
	 * @param string $id Context Container ID
	 * @param string $fn Context Container function
	 */
	public static function add($id, $fn)
	{
		self::prepend($id, $fn);
	}

	/**
	 * Adds a single context container to the top of the list
	 * 
	 * @param  string                                  $id Context Container ID
	 * @param  string                                  $fn Context Container function
	 * @return Ponticlaro\Bebop\Helpers\ContextManager     Context Manager instance
	 */
	public static function prepend($id, $fn)
	{
		if (is_string($id) && is_callable($fn)) {

			self::$contexts->unshift(new ContextContainer($id, $fn));
		}

		return self::$instance;
	}

	/**
	 * Adds a single context container to the bottom of the list
	 * 
	 * @param  string                                  $id Context Container ID
	 * @param  string                                  $fn Context Container function
	 * @return Ponticlaro\Bebop\Helpers\ContextManager     Context Manager instance
	 */
	public static function append($id, $fn)
	{	
		if (is_string($id) && is_callable($fn)) {

			self::$contexts->push(new ContextContainer($id, $fn));
		}

		return self::$instance;
	}

	/**
	 * Returns a single context container by ID
	 * 
	 * @param  string                                    $id ID of the target context container
	 * @return Ponticlaro\Bebop\Helpers\ContextContainer     Target context container
	 */
	private static function __getContextById($id)
	{
		$target_context = null;

		foreach (self::$contexts->getAll() as $context) {
			
			if ($context->getId() == $id) {
				
				$target_context = $context;
				break;
			}
		}

		return $target_context;
	}
}