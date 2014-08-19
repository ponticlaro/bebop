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
     * Adds single route to the top of routes list.
     * 
     * @param string $id     ID of the route
     * @param string $method HTTP method of the route
     * @param string $path   Relative URL of the route
     * @param string $fn     Function to execute
     */
    public function add($id, $method, $path, $fn)
    {
        $this->prepend($id, $method, $path, $fn);
    }

    /**
     * Adds single route to the top of routes list.
     * 
     * @param string $id     ID of the route
     * @param string $method HTTP method of the route
     * @param string $path   Relative URL of the route
     * @param string $fn     Function to execute
     */
    public function prepend($id, $method, $path, $fn)
    {
        $route = new Route($id, $method, $path, $fn);

        $this->__addRouteToCache($route, 'top');
    }

    /**
     * Adds single route to the bottom of routes list.
     * 
     * @param string $id     ID of the route
     * @param string $method HTTP method of the route
     * @param string $path   Relative URL of the route
     * @param string $fn     Function to execute
     */
    public function append($id, $method, $path, $fn)
    {
        $route = new Route($id, $method, $path, $fn);

        $this->__addRouteToCache($route, 'bottom');
    }

    /**
     * Adds single route to pre init cache
     *
     * @param  \Ponticlaro\Bebop\Api\Route $route    Route to cache
     * @param  string                      $position Route position on the list: 'top' or 'bottom'
     * @return void
     */
    protected function __addRouteToCache(\Ponticlaro\Bebop\Api\Route $route, $position)
    {
        $this->pre_init_cache->push((object) array(
            'position' => $position,
            'route'    => $route
        ));
    }

    /**
     * Set cached routes
     * 
     * @return void
     */
    public function __setCachedRoutes()
    {
        foreach ($this->pre_init_cache->getAll() as $item) {
            
            switch ($item->position) {

                case 'top':

                    self::__prependRoute($item->route);
                    break;
                
                case 'bottom':
                    
                    self::__appendRoute($item->route);
                    break;
            }
        }
    }

    /**
     * Internal function to add route instance to the top of the list
     * 
     * @param  \Ponticlaro\Bebop\Api\Route $route Route instance
     * @return void
     */
    protected function __prependRoute(\Ponticlaro\Bebop\Api\Route $route)
    {
        $this->routes->unshift($route);
    }

    /**
     * Internal function to add route instance to the bottom of the list
     * 
     * @param  \Ponticlaro\Bebop\Api\Route $route Route instance
     * @return void
     */
    protected function __appendRoute(\Ponticlaro\Bebop\Api\Route $route)
    {
        $this->routes->push($route);
    }

    /**
     * Returns single route using its 
     * 
     * @param  string                      $key   ID + method of the target route
     * @return \Ponticlaro\Bebop\Api\Route $route Route instance
     */
    public function get($key)
    {   
        if (!is_string($key)) 
            throw new \UnexpectedValueException('Route $key must be a string');

        $key_data = explode(':', strtolower($key));
        $id       = isset($key_data[0]) ? $key_data[0] : null;
        $method   = isset($key_data[1]) ? $key_data[1] : null;

        if (!$id || !$method)
            throw new \UnexpectedValueException('Route $key must have both an "id" and "method: e.g. posts/meta:get');

        $target_route = null;

        foreach ($this->routes->getAll() as $route) {

            if ($route->getId() == $id && $route->getMethod() == $method) {
                
                $target_route = $route;
                break;
            }
        }

        return $target_route;
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