<?php

namespace Ponticlaro\Bebop\Helpers;

use Ponticlaro\Bebop;

class FeatureManager {

    private static $instance;

    protected static $features;

    protected function __construct()
    {
        static::$features = Bebop::Collection();
    }

    public static function getInstance()
    {
        if (is_null(static::$instance)) static::$instance = new static;
        
        return static::$instance;
    }

    public static function add($id, array $config = array())
    {
        if (!is_string($id))
            throw new \Exception("Feature ID must be a string");
            
        static::$features->set($id, new Feature($id, $config));

        return static::get($id);
    }

    public static function &get($id)
    {
        return static::$features->get($id);
    } 

    public static function exists($id)
    {
        return static::$features->hasKey($id);
    }
}