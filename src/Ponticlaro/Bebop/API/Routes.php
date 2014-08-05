<?php

namespace Ponticlaro\Bebop\API;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\Patterns\SingletonAbstract;

class Routes extends SingletonAbstract {

    /**
     * Pre init hook cache for routes
     * 
     * @var \Ponticlaro\Bebop\Common\Collection
     */
    protected static $pre_init_cache;

    /**
     * Routes list
     * 
     * @var \Ponticlaro\Bebop\Common\Collection
     */
    protected static $routes;

    /**
     * Initializes the Routes singleton instance
     * 
     */
    protected function __construct()
    {
        // Pre initialization cache with the list of all routes
        self::$pre_init_cache = Bebop::Collection();

        // Routes list
        self::$routes = Bebop::Collection();

        // Register cached routes on the init hook, after having all custom post types registered
        add_action('init', array('\Ponticlaro\Bebop\API\Routes', '__setCachedRoutes'), 3);
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

        self::__addRouteToCache($route, 'top');
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

        self::__addRouteToCache($route, 'bottom');
    }

    /**
     * Adds single route to pre init cache
     *
     * @param  \Ponticlaro\Bebop\API\Route $route    Route to cache
     * @param  string                      $position Route position on the list: 'top' or 'bottom'
     * @return void
     */
    protected static function __addRouteToCache(\Ponticlaro\Bebop\API\Route $route, $position)
    {
        self::$pre_init_cache->push((object) array(
            'position' => $position,
            'route'    => $route
        ));
    }

    /**
     * Set cached routes
     * 
     * @return void
     */
    public static function __setCachedRoutes()
    {
        foreach (self::$pre_init_cache->getAll() as $item) {
            
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
     * @param  \Ponticlaro\Bebop\API\Route $route Route instance
     * @return void
     */
    protected static function __prependRoute(\Ponticlaro\Bebop\API\Route $route)
    {
        self::$routes->unshift($route);
    }

    /**
     * Internal function to add route instance to the bottom of the list
     * 
     * @param  \Ponticlaro\Bebop\API\Route $route Route instance
     * @return void
     */
    protected static function __appendRoute(\Ponticlaro\Bebop\API\Route $route)
    {
        self::$routes->push($route);
    }

    /**
     * Returns single route using its 
     * 
     * @param  string                      $key   ID + method of the target route
     * @return \Ponticlaro\Bebop\API\Route $route Route instance
     */
    public static function get($key)
    {   
        if (!is_string($key)) 
            throw new \UnexpectedValueException('Route $key must be a string');

        $key_data = explode(':', strtolower($key));
        $id       = isset($key_data[0]) ? $key_data[0] : null;
        $method   = isset($key_data[1]) ? $key_data[1] : null;

        if (!$id || !$method)
            throw new \UnexpectedValueException('Route $key must have both an "id" and "method: e.g. posts/meta:get');

        $target_route = null;

        foreach (self::$routes->getAll() as $route) {

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
    public static function getAll()
    {
        return self::$routes->getAll();
    }
}