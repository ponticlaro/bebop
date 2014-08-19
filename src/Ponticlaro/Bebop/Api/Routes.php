<?php

namespace Ponticlaro\Bebop\Api;

use Ponticlaro\Bebop;

class Routes {

    /**
     * Pre init hook cache for routes
     * 
     * @var \Ponticlaro\Bebop\Common\Collection
     */
    protected $pre_init_cache;

    /**
     * Routes list
     * 
     * @var \Ponticlaro\Bebop\Common\Collection
     */
    protected $routes;

    /**
     * Initializes a Routes instance
     * 
     */
    public function __construct()
    {
        // Pre initialization cache with the list of all routes
        $this->pre_init_cache = Bebop::Collection();

        // Routes list
        $this->routes = Bebop::Collection();

        // Register cached routes on the init hook, after having all custom post types registered
        add_action('init', array($this, '__setCachedRoutes'), 3);
    }

    /**
     * Adds a single route with GET as the method
     * 
     * @param  string                      $path     Route path
     * @param  string                      $callable Route function
     * @return Ponticlaro\Bebop\Api\Routes           This class instance
     */
    public function get($path, $callable)
    {
        $this->__addRoute('get', $path, $callable);

        return $this;
    }

    /**
     * Adds a single route with POST as the method
     * 
     * @param  string                      $path     Route path
     * @param  string                      $callable Route function
     * @return Ponticlaro\Bebop\Api\Routes           This class instance
     */
    public function post($path, $callable)
    {
        $this->__addRoute('post', $path, $callable);

        return $this;
    }

    /**
     * Adds a single route with PUT as the method
     * 
     * @param  string                      $path     Route path
     * @param  string                      $callable Route function
     * @return Ponticlaro\Bebop\Api\Routes           This class instance
     */
    public function put($path, $callable)
    {
        $this->__addRoute('put', $path, $callable);

        return $this;
    }

    /**
     * Adds a single route with PATCH as the method
     * 
     * @param  string                      $path     Route path
     * @param  string                      $callable Route function
     * @return Ponticlaro\Bebop\Api\Routes           This class instance
     */
    public function patch($path, $callable)
    {
        $this->__addRoute('patch', $path, $callable);

        return $this;
    }

    /**
     * Adds a single route with DELETE as the method
     * 
     * @param  string                      $path     Route path
     * @param  string                      $callable Route function
     * @return Ponticlaro\Bebop\Api\Routes           This class instance
     */
    public function delete($path, $callable)
    {
        $this->__addRoute('delete', $path, $callable);

        return $this;
    }

    /**
     * Adds a single route with OPTIONS as the method
     * 
     * @param  string                      $path     Route path
     * @param  string                      $callable Route function
     * @return Ponticlaro\Bebop\Api\Routes           This class instance
     */
    public function options($path, $callable)
    {
        $this->__addRoute('options', $path, $callable);

        return $this;
    }

    /**
     * Internal method to add a route
     * 
     * @param  string $method   Route method
     * @param  string $path     Route path
     * @param  string $callable Route function
     * @return void
     */
    public function __addRoute($method, $path, $callable)
    {
        if (!is_string($method))
            throw new \Exception("Routes: route method must be a string");

        if (!is_string($path))
            throw new \Exception("Routes: route path must be a string");
            
        if (!is_callable($callable))
            throw new \Exception("Routes: route callable must be callable");
        
        $this->__addRouteToCache(new Route($method, $path, $callable));
    }

    /**
     * Adds single route to pre init cache
     *
     * @param  \Ponticlaro\Bebop\Api\Route $route    Route to cache
     * @return void
     */
    protected function __addRouteToCache(\Ponticlaro\Bebop\Api\Route $route)
    {
        $this->pre_init_cache->push($route);
    }

    /**
     * Set cached routes
     * 
     * @return void
     */
    public function __setCachedRoutes()
    {
        foreach ($this->pre_init_cache->getAll() as $route) {
            
            self::__pushRoute($route);
        }
    }

    /**
     * Internal function to add route instance to routes list
     * 
     * @param  \Ponticlaro\Bebop\Api\Route $route Route instance
     * @return void
     */
    protected function __pushRoute(\Ponticlaro\Bebop\Api\Route $route)
    {
        $this->routes->push($route);
    }

    /**
     * Returns all routes
     * 
     * @return array Routes list
     */
    public function getAll()
    {
        return $this->routes->getAll();
    }
}