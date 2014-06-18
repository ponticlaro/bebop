<?php

namespace Ponticlaro\Bebop\Helpers;

use Ponticlaro\Bebop;

class ScriptsManager {

    /**
     * Holds hooks for scripts registration
     * 
     * @var \Ponticlaro\Bebop\Common\Collection
     */
    protected $hooks;

    /**
     * Instantiates a Scripts Manager
     * 
     */
    public function __construct()
    {   
        $this->hooks = Bebop::Collection();
    }

    /**
     * Adds a script registration hook
     * 
     * @param \Ponticlaro\Bebop\Patterns\ScriptsHook $hook
     */
    public function addHook(\Ponticlaro\Bebop\Patterns\ScriptsHook $hook)
    {
        $this->hooks->set($hook->getId(), $hook);

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