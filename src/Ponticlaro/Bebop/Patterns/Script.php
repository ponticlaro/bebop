<?php

namespace Ponticlaro\Bebop\Patterns;

use Ponticlaro\Bebop;

abstract class Script implements ScriptInterface {

    /**
     * Holds configuration parameters, except dependencies
     * 
     * @var \Ponticlaro\Bebop\Common\Collection
     */
    protected $config;

    /**
     * Holds dependencies
     * 
     * @var \Ponticlaro\Bebop\Common\Collection
     */
    protected $dependencies;

    /**
     * Holds environment specific configuration modifications
     * 
     * @var \Ponticlaro\Bebop\Common\Collection
     */
    protected $env_configs;

    /**
     * Flag that states if the script is already registered
     * 
     * @var boolean
     */
    protected $is_registered = false;

    /**
     * Instantiates a new script object 
     * 
     */
    public function __construct()
    {
        // Create dependencies collection
        $this->dependencies = Bebop::Collection();

        // Create environment configuration collection
        $this->env_configs = Bebop::Collection();
    }

    /**
     * Sets script ID
     * 
     * @param string $id
     */
    public function setId($id)
    {
        if (is_string($id))
            $this->config->set('id', $id);

        return $this;
    }

    /**
     * Returns script ID
     * 
     * @return string
     */
    public function getId()
    {
        return $this->config->get('id');
    }

    /**
     * Sets file path
     * 
     * @param string $path Path relative to the theme location
     */
    public function setPath($path)
    {
        if (is_string($path))
            $this->config->set('path', ltrim($path, '/'));

        return $this;
    }

    /**
     * Returns file path
     * 
     * @return string
     */
    public function getPath()
    {
        return $this->config->get('path');
    }

    /**
     * Sets a base URL for this script
     * 
     * @param string $base_url 
     */
    public function setBaseUrl($base_url)
    {
        if (is_string($base_url)) $this->config->set('base_url', rtrim($base_url, '/'));

        return $this;
    }

    /**
     * Returns script base URL
     * 
     * @return string
     */
    public function getBaseUrl()
    {
        $path = $this->config->get('path');

        // Return empty string if path is an absolute URL
        if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0 || strpos($path, '//') === 0) {
            
            return '';
        }

        return $this->config->get('base_url') ? $this->config->get('base_url') .'/' : Bebop::getUrl('theme') .'/';
    }

    /**
     * Returns script absolute URL
     * 
     * @return string
     */
    public function getAbsoluteUrl()
    {
        return $this->getBaseUrl() . $this->getPath();
    }

    /**
     * Sets file version
     * 
     * @param string $version
     */
    public function setVersion($version)
    {
        if (is_string($version))
            $this->config->set('version', $version);

        return $this;
    }

    /**
     * Returns file version
     * 
     * @return string
     */
    public function getVersion()
    {
        $version = $this->config->get('version');
        $path    = Bebop::getPath('theme') .'/'. $this->getPath();

        if (!$version && is_readable($path)) $version = filemtime($path);

        return $version;
    }

    /**
     * Sets dependencies
     * 
     * @param array $dependencies
     */
    public function setDependencies(array $dependencies = array())
    {
        $this->dependencies->push($dependencies);

        return $this;
    }

    /**
     * Adds dependencies to existing set
     * 
     * @param array $dependencies
     */
    public function addDependencies(array $dependencies = array())
    {
        foreach ($dependencies as $dependency) {
            
            $this->dependencies->push($dependency);
        }

        return $this;
    }

    /**
     * Replaces all dependencies
     * 
     * @param array $dependencies
     */
    public function replaceDependencies(array $dependencies = array())
    {
        $this->dependencies->clear()->push($dependencies);

        return $this;
    }

    /**
     * Removes dependencies
     * 
     * @param array $dependencies
     */
    public function removeDependencies(array $dependencies = array())
    {
        foreach ($dependencies as $dependency) {
            
            $this->dependencies->pop($dependency);
        }

        return $this;
    }

    /**
     * Sets a single dependency
     * 
     * @param array $handle THe script handle
     */
    public function addDependency($handle)
    {
        if (is_string($handle))
            $this->dependencies->push($handle);

        return $this;
    }

    /**
     * Sets dependencies
     * 
     * @param array $dependencies
     */
    public function removeDependency($handle)
    {
        if (is_string($handle))
            $this->dependencies->pop($handle);

        return $this;
    }

    /**
     * Returns dependencies
     * 
     * @return array
     */
    public function getDependencies()
    {
        return $this->dependencies->getAll();
    }

    /**
     * Adds a function to execute when the target '$env' is active
     * 
     * @param  string $env Target environment ID
     * @param  string $fn  Function to execute
     */
    public function onEnv($env, $fn)
    {
        if (is_callable($fn)) {

            if (is_string($env)) {
               
                $this->env_configs->set($env, $fn);
            }

            elseif (is_array($env)) {
                
                foreach ($env as $env_key) {
                   
                    $this->env_configs->set($env_key, $fn);
                }
            }
        }
        
        return $this;
    }

    /**
     * Registers script
     * 
     */
    public function register() {}

    /**
     * Deregisters script
     * 
     */
    public function deregister() {}

    /**
     * Enqueues script
     * 
     */
    public function enqueue() {}

    /**
     * Dequeues script
     * 
     */
    public function dequeue() {}

    /**
     * Executes any function that exists for the current environment
     * 
     */
    protected function __applyEnvModifications()
    {
        // Get current environment
        $current_env = Bebop::Env()->getCurrentKey();

        // Execute current environment function
        if ($this->env_configs->hasKey($current_env))
            call_user_func_array($this->env_configs->get($current_env), array($this));
    }
}