<?php

namespace Ponticlaro\Bebop\Helpers;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\Helpers\FeatureManager;

class Feature {

    protected $id;

    protected $config;

    protected $enabled = false;

    public function __construct($id, array $config = array())
    {
        if (!is_string($id))
            throw new \Exception("Feature id must be a string");

        $this->id     = $id;
        $this->config = Bebop::Collection(array());

        if ($config) {

            foreach ($config as $key => $value) {
                
                $this->set($key, $value);
            }
        }
    }

    public function enable()
    {
        $this->enabled = true;

        return $this;
    }

    public function disable()
    {
        $this->enabled = false;

        return $this;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    public function getId()
    {
        return $this->id;
    }

    public function set($key, $value)
    {
        if (is_string($key))
            $this->config->set($key, $value);

        return $this;
    }

    public function get($key)
    {
        return is_string($key) ? $this->config->get($key) : null;
    }

    public function has($key)
    {
        return is_string($key) ? $this->config->hasKey($key) : false;
    }

    public function getAll()
    {
        return $this->config->getAll();
    }
}