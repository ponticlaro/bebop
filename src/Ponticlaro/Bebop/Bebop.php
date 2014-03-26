<?php

namespace Ponticlaro;

use Ponticlaro\Bebop\Common\Collection;
use Ponticlaro\Bebop\Utils;

class Bebop
{
	/**
	 * The WordPress version
	 * 
	 * @var string
	 */
	private static $__wp_version;

	/**
	 * Bebop instance
	 * 
	 * @var Ponticlaro\Bebop
	 */
	private static $__instance;

	/**
	 * Configuration parameters
	 * 
	 * @var Ponticlaro\Bebop\Common\Collection
	 */
	private static $__config;

	/**
	 * Tracker object
	 * 
	 * @var \stdClass
	 */
	private static $__tracker;

	/**
	 * Collection of paths
	 * 
	 * @var Ponticlaro\Bebop\Common\Collection
	 */
	private static $__paths;

	/**
	 * Collection of urls
	 * 
	 * @var Ponticlaro\Bebop\Common\Collection
	 */
	private static $__urls;

	/**
	 * UI class handler
	 * 
	 * @var \Ponticlaro\Bebop\UI
	 */
	private static $__ui;

	/**
	 * Checks environment and build defaults 
	 * 
	 */
	private function __construct()
	{	
		/////////////////////////////////////
		// Get installed WordPress version //
		/////////////////////////////////////
		global $wp_version;

		self::$__wp_version = $wp_version;

		///////////////////////////////
		// Set Default configuration //
		///////////////////////////////
		$default_config = array(
			// Not currently doing anything
		);

		///////////////////////////
		// Get base paths & Urls //
		///////////////////////////
		$uploads_data = wp_upload_dir();
		$template_dir = get_template_directory();
		$template_url = get_bloginfo('template_url');

		// Default paths
		$default_paths = array(
			'wptools' => __DIR__,
			'root'    => ABSPATH,
			'admin'   => '',
			'plugins' => '',
			'content' => '',
			'uploads' => $uploads_data['basedir'],
			'themes'  => str_replace('/'. basename($template_dir), '', $template_dir),
			'theme'   => get_template_directory()
		);

		// Default urls
		$default_urls = array(
			'wptools' => self::getPathUrl(__DIR__),
			'home'    => home_url(),
			'admin'   => admin_url(),
			'plugins' => plugins_url(),
			'content' => content_url(),
			'uploads' => $uploads_data['baseurl'],
			'themes'  => str_replace('/'. basename($template_url), '', $template_url),
			'theme'   => $template_url
		);

		// Save default configuration, paths and urls
		self::$__config = new Collection($default_config);
		self::$__paths  = new Collection($default_paths);
		self::$__urls   = new Collection($default_urls);

		//////////////////////////
		// Build Object tracker //
		//////////////////////////
		self::$__tracker             = new \stdClass;
		self::$__tracker->post_types = new Collection;
		self::$__tracker->taxonomies = new Collection;
		self::$__tracker->metaboxes  = new Collection;

		// Instantiate UI
		$this->UI();
		
		// Instantiate API
		$this->API();

		// Shortcode support for in editor use 
		add_shortcode('Bebop', array($this, 'shortcode'));

		// Register stuff on the init hook
		add_action('init', array($this, 'initRegister'), 1);

		// Register custom rewrite rules
		add_action('rewrite_rules_array', array($this, 'rewriteRules'), 99);

		// Handle template includes
		add_action('template_redirect', array($this, 'templateRedirects'));
	}

	/**
	 * Creates Bebop instance to check environment and build defaults 
	 * 
	 * @return \Ponticlaro\Bebop
	 */
	public static function boot()
	{
		if(!self::$__instance || !is_a(self::$__instance, 'Ponticlaro\Bebop')) {

			self::$__instance = new Bebop();
		}
	} 

	/**
	 * Register Bebop stuff on the init hook
	 * 
	 * @return void
	 */
	public function initRegister()
	{
		add_rewrite_tag('%bebop_api%','([^&]+)');
	}

	/**
	 * Adds custom rewrite rules
	 * 
	 * @param  array $wp_rules Array of rewrite rules
	 * @return array           Modified array of rewrite rules
	 */
	public function rewriteRules($wp_rules) 
	{
		$bebop_rules = array(
			"_bebop/api/?(.*)?$" => 'index.php?bebop_api=1'
		);

		return array_merge($bebop_rules, $wp_rules);
	}

	/**
	 * Addds template redirections
	 * 
	 * @return void
	 */
	public function templateRedirects() 
	{
		global $wp_query;

		if($wp_query->get('bebop_api')) {
			
			new \Ponticlaro\Bebop\API\Router;
			exit;
		}
	}

	/**
	 * Returns WordPress version
	 * 
	 * @return string
	 */
	public static function getVersion() 
	{
		return self::$__wp_version;
	}

	/**
	 * Creates single imnstance of target class
	 * 
	 * @return mixed Instance of target class or false
	 */
	public static function create()
	{	
		$args = func_get_args();

		if (!isset($args[0])) 
			throw new ErrorException("You must specify the type of object to create");

		$obj_name = $args[0];

		unset($args[0]);

		switch ($obj_name) {

			case 'Collection':

				$class = __CLASS__ ."\Common\Collection";
				break;

			case 'Option':

				$class = __CLASS__ ."\\Database\\" . $obj_name;
				break;

			default: 

				$class = __CLASS__ .'\\'. $obj_name;
				break;
		}

		return call_user_func(array(__CLASS__, "__createInstance"), $class, $args);
	}

	/**
	 * Creates and instance of the target class
	 * 
	 * @param  string $className Target class
	 * @param  array  $args      Arguments to pass to target class
	 * @return mixed             Class instance or false
	 */
	private static function __createInstance($className, array $args = array())
	{
	    if (class_exists($className)) {

	        return call_user_func_array(

	        	array(
					new \ReflectionClass($className), 
					'newInstance'
				), 
	            $args
	        );
	    }

	    return false;
	}

	/**
	 * Tracks objects created by Bebop
	 * 
	 * @param  mixed $object Object to be tracked
	 * @return void
	 */
	public static function track($object)
	{
		if (is_object($object)) {
			
			$class = get_class($object);
			$key   = $object->getConfig('key');

			switch ($class) {

				case __CLASS__ . '\PostType':

					$collection = 'post_types';
					break;

				case __CLASS__ . '\Taxonomy':

					$collection = 'taxonomies';
					break;

				case __CLASS__ . '\Metabox':

					$collection = 'metaboxes';
					break;
			}

			self::$__tracker->{$collection}->set($key, $object);
		}
	}

	/**
	 * Returns the UI class instance
	 */
	public static function UI()
	{
		return Bebop\UI::getInstance();
	}

	/**
	 * Returns the API class instance
	 * 
	 */
	public static function API()
	{
		return Bebop\API::getInstance();
	}

	/**
	 * Creates Bebop shortcode for usage inside content editor
	 * 
	 * @param  array   $attrs    Shortcode attributes
	 * @param  string  $content  Shortcode content
	 * @return void
	 */
	public function shortcode($attrs, $content = null) 
	{
		if ($attrs) {

			if (array_key_exists('url', $attrs)) 
				return Bebop::getUrl($attrs['url']);

			if (array_key_exists('path', $attrs)) 
				return Bebop::getPath($attrs['path']);
		}
	} 

	public static function getPathUrl($path, $relative = false)
	{
		if (!is_string($path)) return null;

		$content_base = basename(WP_CONTENT_URL);
		$path         = str_replace(ABSPATH, '', $path);
		$url          = '/'. preg_replace("/.*$content_base/", "$content_base", $path);
		
		return $relative ? $url : home_url() . $url; 
	}

	/**
	 * Calls utilities from common php library
	 * 
	 * @return mixed
	 */
	public static function util()
	{	
		$args = func_get_args();

		if( !isset($args[0])) throw new ErrorException("You need to define the utility name");
		
		$name = $args[0];

		unset($args[0]);

		return call_user_func_array(array('\Ponticlaro\Bebop\Utils', $name), $args);
	}

	/**
	 * Includes a path from the path collection
	 * 
	 * @param  string  $path_key Path key string
	 * @param  boolean $once     True if it should only be included once
	 * @return void          
	 */
	public static function inc($path_key, $once = false)
	{
		if (!is_string($path_key)) return;

		// Check for path in path collection
		$cached_path = self::getPath($path_key);

		if ($cached_path && is_readable($cached_path) && is_file($cached_path)) {
			
			if ($once) {

				include_once $cached_path;

			} else {

				include $cached_path;
			}
		}
	}

	/**
	 * Requires a path from the paths collection
	 * 
	 * @param  string  $path_key Path key string
	 * @param  boolean $once     True if it should only be required once
	 * @return void
	 */
	public static function req($path_key, $once = false)
	{
		if (!is_string($path_key)) return;

		// Check for path in path collection
		$cached_path = self::getPath($path_key);

		if ($cached_path && is_readable($cached_path) && is_file($cached_path)) {
			
			if ($once) {

				require_once $cached_path;

			} else {

				require $cached_path;
			}
		}
	}

	/**
	 * Handles any call to undefined static methods
	 * 
	 * @param  string $name Name of the target method
	 * @param  array  $args Arguments for the target method
	 * @return mixed      
	 */
	public static function __callStatic($name, $args) 
    {
	    //////////////////////////////
    	// Check for correct method //
	    //////////////////////////////

    	// Check for echo
		$is_echo_url  = $name == 'Url' ? true : false;
		$is_echo_path = $name == 'Path' ? true : false;

    	// Check for create
    	$create_methods = array(
    		'AdminForm',
    		'AdminPage',
			'Collection',
			'Metabox',
			'Option',
			'PostType',
			'Taxonomy',
    	);

    	if (substr($name, 0, 6) == 'create' || in_array($name, $create_methods) ) $is_create = true;
    	if (substr($name, 0, 6) == 'create') $name = substr($name, 6, strlen($name));

    	// Check for set
    	$is_get = substr($name, 0, 3) == 'get' ? true : false;
    	$is_set = substr($name, 0, 3) == 'set' ? true : false;

	    //////////
    	// Echo //
	    //////////
		if ($is_echo_url) {

			echo call_user_func_array(array(__CLASS__, 'getUrl'), $args);
			return;
		}

		if ($is_echo_path) {

			echo call_user_func_array(array(__CLASS__, 'getPath'), $args);
			return;
		} 

	    /////////
    	// Set //
	    /////////
    	if ($is_set) {

    		$set_action = substr($name, 3, strlen($name));

			$is_setPath = $set_action == 'Path' ? true : false;
			$is_setUrl  = $set_action == 'Url' ? true : false;

			if ( $is_setPath || $is_setUrl ){

				if (is_array($args[0])) {

					$obj = $is_setPath ? self::$__paths : self::$__urls;

    				call_user_func_array( array($obj, 'set'), $args);

				} elseif(is_string($args[0])) {

					$property_in_name = substr($name, 6);

	    			if($property_in_name){

						$key  = Bebop::util('camelcaseToUnderscore', $property_in_name);
						$path = isset($args[0]) ? $args[0] : null;

	    			}else{

						$key  = isset($args[0]) ? $args[0] : null;
						$path = isset($args[1]) ? $args[1] : null;
	    			}	

	    			if ($is_setPath) {

						self::$__paths->set($key, $path);
		    		}

		    		if ($is_setUrl) {

		    			self::$__urls->set($key, $path);
		    		}	
	    		}
			}
    	}

	    /////////
    	// Get //
	    /////////
    	elseif ($is_get) {

    		$get_action     = substr($name, 3, strlen($name));

			$is_getPath     = $get_action == 'Path' ? true : false;
			$is_getUrl      = $get_action == 'Url' ? true : false;
			
			$is_getPostType = $get_action == 'PostType' ? true : false;
			$is_getMetabox  = $get_action == 'Metabox' ? true : false;

    		if( $is_getPath || $is_getUrl ){

    			if (!$args) {

    				return $is_getPath ? self::$__paths->get() : self::$__urls->get();
    			}

    			$key           = isset($args[0]) ? $args[0] : null;
				$relative_path = isset($args[1]) ? $args[1] : null;

    			if ($is_getPath) {

					$key       = self::$__paths->hasKey($key) ? $key : 'home';
					$separator = DIRECTORY_SEPARATOR;
					$base      = self::$__paths->get($key);
	    		}

	    		if ($is_getUrl) {

					$key       = self::$__urls->hasKey($key) ? $key : 'home';
					$separator = "/";
					$base      = self::$__urls->get($key);
	    		}

	    		$separator = $relative_path == $separator ? null : $separator;

	    		return $relative_path ? $base . $separator . $relative_path : $base;
   

    		} elseif( $is_getPostType || $is_getMetabox ){

    			if (!isset($args[0])) return;

    			if (is_string($args[0])) {

    				$key = Bebop::util('slugify', $args[0]);
    				
    				if ($is_getPostType) 
    					return self::$__tracker->post_types->get($key);

    				if ($is_getMetabox) 
    					return self::$__tracker->metaboxes->get($key);
    			}
    		}
    	}

	    ////////////
    	// Create //
	    ////////////
    	elseif ($is_create) {

    		array_unshift($args, $name);

    		return call_user_func_array(array(__CLASS__, 'create'), $args);
    	}
    }
}