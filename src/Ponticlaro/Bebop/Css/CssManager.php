<?php

namespace Ponticlaro\Bebop\Css;

use Ponticlaro\Bebop\Helpers\ScriptsManager;
use Ponticlaro\Bebop\Css\CssScriptsHook;
use Ponticlaro\Bebop\Patterns\SingletonAbstract;

class CssManager extends SingletonAbstract {

	/**
	 * ScriptsManager instance
	 * 
	 * @var \Ponticlaro\Bebop\Helpers\ScriptsManager
	 */
	protected static $manager;

    /**
     * Instantiates CSS Manager
     * 
     */
	protected function __construct()
	{
		self::$manager = new ScriptsManager();

		// Add default hooks
		$this->addHook('front', 'wp_enqueue_scripts')
			 ->addHook('back', 'admin_enqueue_scripts')
			 ->addHook('login', 'login_enqueue_scripts');
	}

    /**
     * Adds a script registration hook
     * 
     * @param string $id   Registration hook ID
     * @param string $hook WordPress hook ID
     */
	public function addHook($id, $hook_id)
	{
		// Generate new CSS scripts hook
		$hook = new CssScriptsHook($id, $hook_id);

		// Add hook to scripts manager
		self::$manager->addHook($hook);
		
		return $this;
	}

    /**
     * Returns a single registration hook by ID
     * 
     * @param  string                           $id ID of the target registration hook
     * @return \Ponticlaro\Bebop\Scripts\Script
     */
	public function getHook($id)
	{
		return self::$manager->getHook($id);
	}
}