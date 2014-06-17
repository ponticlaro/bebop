<?php

namespace Ponticlaro\Bebop;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\Patterns\SingletonAbstract;
use Ponticlaro\Bebop\Scripts\ScriptsHook;

class Scripts extends SingletonAbstract {

    /**
     * Holds hooks for scripts registration
     * 
     * @var \Ponticlaro\Bebop\Common\Collection
     */
    protected $hooks;

    /**
     * Instantiates the Scripts manager
     * 
     */
    protected function __construct()
    {   
        // Register default hooks
        $this->hooks = Bebop::Collection(array(
            'front' => new ScriptsHook('front', 'wp_enqueue_scripts'),
            'back'  => new ScriptsHook('back', 'admin_enqueue_scripts'),
            'login' => new ScriptsHook('login', 'login_enqueue_scripts')
        ));

        // Create environment configuration collection
        $this->env_configs = Bebop::Collection();
    }

    /**
     * Adds a script registration hook
     * 
     * @param string $id   Registration hook ID
     * @param string $hook WordPress hook ID
     */
    public function addHook($id, $hook)
    {
        if (is_string($id) && is_string($hook)) {
            
            $this->hooks->set($id, new ScriptsHook($id, $hook));
        }

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
        if (!is_string($id)) return null;

        return $this->hooks->get($id);
    }
}