<?php

namespace Ponticlaro\Bebop\API;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\Patterns\SingletonAbstract;

class Routes extends SingletonAbstract {

    protected static $routes;

    protected function __construct()
    {
        self::$routes = Bebop::Collection();
    }

    public function set($method, $path, $fn)
    {
        if (!is_string($method) || !is_string($path)) 
            throw new \UnexpectedValueException('Both $method and $path need to be a string');

        self::$routes->set($method .':'. $path, new Route($method, $path, $fn));

        return $this;
    }

    public static function get($method, $path)
    {   
        if (!is_string($method) || !is_string($path)) 
            throw new \UnexpectedValueException('Both $method and $path need to be a string');

        return self::$routes->get($method .':'. $path);
    }

    public static function getAll()
    {
        return self::$routes->getAll();
    }
}