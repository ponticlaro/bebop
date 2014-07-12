<?php

namespace Ponticlaro\Bebop;

use Ponticlaro\Bebop;

class AdminPage
{
	/**
	 * Configuration parameters
	 * 
	 * @var Ponticlaro\Bebop\Common\Collection
	 */
	protected $__config;

	/**
	 * See http://codex.wordpress.org/Administration_Menus#Page_Hook_Suffix
	 * 
	 * @var string
	 */
	protected $__hook_suffix;

	/**
	 * Instantiates a new Admin 
	 * 
	 * @param string   $title  Admin page tile. Used to create its access slug
	 * @param callable $fn     Function to call that will contain all the page logic
	 * @param array    $config Administration Menus API configuration
	 */
	public function __construct($title, $fn, array $config = array())
	{
		// Take any necessary actions to make this object usable
		$this->__preInit();

		// Initialize class
		call_user_func_array(array($this, '__init'), func_get_args());
	}

	private function __preInit()
	{
		$default_config = array(
			'page_title'    => '',
			'menu_title'    => '',
			'capability'    => 'read',
			'menu_slug'     => '',
			'function'      => '',
			'icon_url'      => '',
			'position'      => null,
			'parent'        => '',
			'auto_register' => true
		);

		$this->__config = Bebop::Collection($default_config);
	}

	private function __init()
	{
		call_user_func_array(array($this, '__handleInit'), func_get_args());

		return $this;
	}

	private function __handleInit($title, $fn, array $config = array())
	{
		if( isset($title) ){

			if( is_array($title) ) {

				$this->__config->set($title);

			} elseif ( is_string( $title )) {

				$this->__config->set('page_title', $title);

			} else {

				throw new \ErrorException('First argument must be an associative array or string');

			}

		} 

		if( isset($fn) ) {
			
			if( is_string($fn) & !is_callable($fn) ){

				throw new \ErrorException('Second argument must be callable');

			}

			$this->__config->set('function', $fn);

		}

		// Set 
		if( isset($config) ) {

			if( !is_array($config) ) throw new \ErrorException('Third argument must be a config array');

			$this->__config->set($config);

		}

		// Set defaults, validate and fix config
		$this->__validateConfig();

		// Auto register admin page
		if( $this->__config->get('auto_register') ) {

			$this->register();

		}

	}

	private function __validateConfig()
	{
		$page_title = $this->__config->get('page_title');
		$menu_title = $this->__config->get('menu_title');
		$function   = $this->__config->get('function');

		// Throw error is neither page_title or menu_title was defined
		if( !$page_title && ! $menu_title ){
			throw new \ErrorException('You must set either the page_title or menu_title parameter');
		}

		// Throw error is no function was defined 
		if( !$function ){
			throw new \ErrorException("You must set the 'function' parameter");
		}

		// Auto fill page_title from menu_title
		if( !$this->__config->get('page_title') ) {
			$this->__config->set('page_title', $menu_title);
		}

		// Auto fill menu_title from page_title
		if( !$this->__config->get('menu_title') ) {
			$this->__config->set('menu_title', $page_title);
		}

		// Auto generate menu-_slug from page_title or menu_title
		if( !$this->__config->get('menu_slug') ) {
			$title = isset($page_title) ? $page_title : $menu_title; 
			$this->__config->set('menu_slug', str_replace(" ", "-", strtolower($title) ) );
		}

		// Set capability to manage_options by default
		if( !$this->__config->get('capability') ) {
			$this->__config->set('capability', 'manage_options');
		}

		// Set parent to null by default
		if( !$this->__config->get('position') ) {
			$this->__config->set('position', null);
		}

		// Set auto_register to true by default
		if( !$this->__config->get('auto_register') ) {
			$this->__config->set('auto_register', true);
		}
		
	}

	/**
	 * Registers this page with the add_action function on the "admin_menu" hook
	 * 
	 * @return void
	 */
	public function register()
	{
		if( $this->__config->get('function') ){
			add_action( 'admin_menu', array( $this, 'handleRegistration' ) );
		}
		
		return $this;
	}


	/**
	 * Defines which function should be used based on the settings provided
	 * See http://codex.wordpress.org/Administration_Menus
	 * 
	 * @return [type] [description]
	 */
	public function handleRegistration()
	{
		$parent = $this->__config->get('parent');

		if( $parent ){

			switch ( $parent ) {
				case 'dashboard':
					$fn = 'add_dashboard_page';
					break;

				case 'posts':
					$fn = 'add_posts_page';
					break;

				case 'pages':
					$fn = 'add_pages_page';
					break;

				case 'media':
					$fn = 'add_media_page';
					break;

				case 'links':
					$fn = 'add_links_page';
					break;

				case 'comments':
					$fn = 'add_comments_page';
					break;

				case 'theme':
					$fn = 'add_theme_page';
					break;

				case 'plugins':
					$fn = 'add_plugins_page';
					break;

				case 'users':
					$fn = 'add_users_page';
					break;

				case 'tools':
					$fn = 'add_management_page';
					break;

				case 'settings':
					$fn = 'add_options_page';
					break;
				
				default:
					$fn = 'add_submenu_page';
					break;
			}

			if($fn == 'add_submenu_page'){

				$hook_suffix = $fn(
					$this->__config->get('parent'),
					$this->__config->get('page_title'), 
					$this->__config->get('menu_title'), 
					$this->__config->get('capability'), 
					$this->__config->get('menu_slug'), 
					array($this, 'baseHtml')
				);

			}else{

				$hook_suffix = $fn(
					$this->__config->get('page_title'), 
					$this->__config->get('menu_title'), 
					$this->__config->get('capability'), 
					$this->__config->get('menu_slug'), 
					array($this, 'baseHtml')
				);

			}


		}else{

			$hook_suffix = add_menu_page(
				$this->__config->get('page_title'), 
				$this->__config->get('menu_title'), 
				$this->__config->get('capability'), 
				$this->__config->get('menu_slug'), 
				array($this, 'baseHtml'), 
				$this->__config->get('icon_url'), 
				$this->__config->get('position') 
			);

		}		

		//$this->__hook_suffix = $hook_suffix;
	}

	/**
	 * Still not working
	 * See http://codex.wordpress.org/Administration_Menus#Page_Hook_Suffix
	 * 
	 * @return string Returns the hook in which we can attach actions to, when this page is loaded
	 */
	public function getHook(){
		//return 'load-'. $this->__config->get(hook_suffix;
	}

	/**
	 * Base Html for any administration page
	 * Executes the function passed in the "function" parameter
	 * 
	 * @return void
	 */
	public function baseHtml(){ 

		$this->checkPermissions(); ?>

		<div class="wrap">

			<div id="icon-plugins" class="icon32"></div>
			<h2><?php echo $this->__config->get('page_title'); ?></h2>
			
			<?php settings_errors(); ?>
			
			<?php
				$fn = $this->__config->get('function');
				call_user_func($fn);
			?>
			
		</div><!-- /.wrap -->
		
	<?php }


	/**
	 * Checks if the user have permission to access this page
	 * 
	 * @return void
	 */
	protected function checkPermissions()
	{
		if ( !current_user_can( $this->__config->get('capability') ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		} 
	}

	/**
	 * Removes this admin page
	 * 
	 * @return void
	 */
	public function destroy()
	{
		remove_menu_page( $this->__config->get('menu_slug') );
	}

}