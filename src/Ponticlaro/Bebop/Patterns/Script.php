<?php

namespace Ponticlaro\Bebop\Patterns;

use Ponticlaro\Bebop;

abstract class Script implements ScriptInterface {

    /**
     * Holds configuration parameters
     * 
     * @var \Ponticlaro\Bebop\Common\Collection
     */
    protected $config;

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
     * @param string $file_path Path relative to the theme location
     */
    public function setFilePath($file_path)
    {
        if (is_string($file_path))
            $this->config->set('file_path', ltrim($file_path, '/'));

        return $this;
    }

    /**
     * Returns file path
     * 
     * @return string
     */
    public function getFilePath()
    {
        return $this->config->get('file_path');
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
        return $this->config->get('base_url') ?: Bebop::getUrl('theme');
    }

    /**
     * Returns script absolute URL
     * 
     * @return string
     */
    public function getAbsoluteUrl()
    {
        return $this->getBaseUrl() .'/'. $this->getFilePath();
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
        $path    = Bebop::getPath('theme') .'/'. $this->getFilePath();

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
        $this->config->set('dependencies', $dependencies);

        return $this;
    }

    /**
     * Returns dependencies
     * 
     * @return array
     */
    public function getDependencies()
    {
        return $this->config->get('dependencies');
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