<?php

namespace Ponticlaro\Bebop\Js;

use Ponticlaro\Bebop;

class JsScript extends \Ponticlaro\Bebop\Patterns\Script {

    /**
     * Instantiates a new script object 
     * 
     * @param string  $id           Script ID
     * @param string  $path         Script path
     * @param array   $dependencies Script dependencies
     * @param string  $version      Script version
     * @param boolean $in_footer    If script should be loaded in the wp_footer hook
     */
    public function __construct($id, $path, array $dependencies = array(), $version = null, $in_footer = true)
    {
        parent::__construct();

        // Create config collection
        $this->config = Bebop::Collection(array(
            'id'           => $id,
            'path'         => ltrim($path, '/'),
            'in_footer'    => $in_footer,
            'base_url'     => null
        ));

        $this->dependencies->push($dependencies);
    }

    /**
     * Sets if the script should load in the footer or not
     * 
     * @param bool $in_footer True if it should be loaded in the footer, false otherwise
     */
    public function loadInFooter($in_footer)
    {
        if (is_bool($in_footer))
            $this->config->set('in_footer', $in_footer);

        return $this;
    }

    /**
     * Returns ture if it should be loaded in the footer, false otherwise
     * 
     * @return bool
     */
    public function getLoadInFooter()
    {
        return $this->config->get('in_footer');
    }

    /**
     * Registers script
     * 
     */
    public function register()
    {
        // Apply any environment specific modification
        $this->__applyEnvModifications();

        // Register script
        wp_register_script(
            $this->getId(),
            $this->getAbsoluteUrl(), 
            $this->getDependencies(), 
            $this->getVersion(), 
            $this->getLoadInFooter()
        );

        // Mark script as registered
        $this->is_registered = true;
    }

    /**
     * Deregisters script
     * 
     */
    public function deregister()
    {
        wp_deregister_script($this->getId());

        return $this;
    }

    /**
     * Enqueues script
     * 
     */
    public function enqueue()
    {
        // Register script if not already registered
        if (!$this->is_registered) $this->register();

        // Enqueue script
        wp_enqueue_script($this->getId());

        return $this;
    }

    /**
     * Dequeues script
     * 
     */
    public function dequeue()
    {
        wp_dequeue_script($this->getId());

        return $this;
    }
}