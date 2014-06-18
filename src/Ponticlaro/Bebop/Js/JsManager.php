<?php

namespace Ponticlaro\Bebop\Js;

use Ponticlaro\Bebop\Helpers\ScriptsManager;
use Ponticlaro\Bebop\Js\JsScriptsHook;
use Ponticlaro\Bebop\Patterns\SingletonAbstract;

class JsManager extends SingletonAbstract {

	/**
	 * ScriptsManager instance
	 * 
	 * @var \Ponticlaro\Bebop\Helpers\ScriptsManager
	 */
	protected static $manager;

    /**
     * Instantiates JS Manager
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
	public function addHook($id, $hook)
	{
		// Generate new CSS scripts hook
		$hook = new JsScriptsHook($id, $hook);

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