<?php

namespace Ponticlaro\Bebop\Css;

use Ponticlaro\Bebop;

class CssScript extends \Ponticlaro\Bebop\Patterns\Script {

    /**
     * Instantiates a new script object 
     * 
     * @param string  $id           Script ID
     * @param string  $file_path    Script file path
     * @param array   $dependencies Script dependencies
     * @param string  $version      Script version
     * @param string  $media        String specifying the media for which this stylesheet has been defined
     */
    public function __construct($id, $file_path, array $dependencies = array(), $version = null, $media = 'all')
    {
        parent::__construct();

        // Create config collection
        $this->config = Bebop::Collection(array(
            'id'           => $id,
            'file_path'    => ltrim($file_path, '/'),
            'media'        => $media,
            'dependencies' => $dependencies,
            'base_url'     => null
        ));
    }

    /**
     * Sets if the script should load in the footer or not
     * 
     * @param bool $in_footer True if it should be loaded in the footer, false otherwise
     */
    public function setMedia($media)
    {
        if (is_string($media))
            $this->config->set('media', $media);

        return $this;
    }

    /**
     * Returns media for this stylesheet
     * 
     * @return bool
     */
    public function getMedia()
    {
        return $this->config->get('media');
    }

    /**
     * Registers style
     * 
     */
    public function register()
    {
        // Apply any environment specific modification
        $this->__applyEnvModifications();

        // Register script
        wp_register_style(
            $this->getId(),
            $this->getAbsoluteUrl(), 
            $this->getDependencies(), 
            $this->getVersion(), 
            $this->getMedia()
        );

        // Mark style as registered
        $this->is_registered = true;
    }

    /**
     * Deregisters style
     * 
     */
    public function deregister()
    {
        wp_deregister_style($this->getId());

        return $this;
    }

    /**
     * Enqueues style
     * 
     */
    public function enqueue()
    {
        // Register style if not already registered
        if (!$this->is_registered) $this->register();

        // Enqueue style
        wp_enqueue_style($this->getId());

        return $this;
    }

    /**
     * Dequeues style
     * 
     */
    public function dequeue()
    {
        wp_dequeue_style($this->getId());

        return $this;
    }
}