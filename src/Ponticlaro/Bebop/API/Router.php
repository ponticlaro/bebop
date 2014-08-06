<?php

namespace Ponticlaro\Bebop\API;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\API\Exceptions\DefaultException AS ApiException;
use Ponticlaro\Bebop\Db;
use Ponticlaro\Bebop\Db\SqlProjection;
use Ponticlaro\Bebop\Patterns\SingletonAbstract;
use Ponticlaro\Bebop\Mvc\ModelFactory;
use Respect\Validation\Validator as v;

class Router extends SingletonAbstract {

    /**
     * Sets the base URL for all resources
     */
    const BASE_URL = '/_bebop/api/';

    /**
     * Slim instance
     * 
     * @var object Slim\Slim
     */
    protected static $slim;

    /**
     * Parsed request body
     * 
     * @var array
     */
    
    protected static $request_body;
    /**
     * List of routes
     * 
     * @var Ponticlaro\Bebop\Common\Collection;
     */
    protected static $routes;

    /**
     * Projection for post meta columns
     * 
     * @var \Ponticlaro\Bebop\Db\SqlProjection
     */
    protected static $postmeta_projection;

    /**
     * Instantiates Router
     */
    protected function __construct()
    {
        // Instantiate Routes object
        self::Routes();

        // Instantiate Slim
        self::$slim = new \Slim\Slim(array(
            'debug' => false
        ));

        // Set Response content-type header
        self::$slim->response()->header('Content-Type', 'application/json');

        // Set post meta projection
        $postmeta_projection = new SqlProjection();
        $postmeta_projection->addColumn('meta_id', '__id')
                            ->addColumn('post_id', '__post_id')
                            ->addColumn('meta_key', '__key')
                            ->addColumn('meta_value', 'value')
                            ->setClass('\Ponticlaro\Bebop\Resources\Models\ObjectMeta');

        self::$postmeta_projection = $postmeta_projection;
    }

    /**
     * Returns Slim instance
     * 
     */
    public static function Slim()
    {
        return self::$slim;
    }

    /**
     * Returns Routes manager instance
     * 
     */
    public static function Routes()
    {
        return Routes::getInstance();
    }

    /**
     * Does a pre-flight check
     * 
     * @return void
     */
    public function preFlightCheck()
    {
        self::$slim->hook('slim.before', function() {        
            
            $request      = self::$slim->request();
            $method       = $request->getMethod();
            $resource_uri = $request->getResourceUri();
            $content_type = $request->headers->get('CONTENT_TYPE');
            $request_body = $request->getBody();

            if (in_array($method, array('POST', 'PUT', 'PATCH'))) {
                
                if ($request_body) {
                    
                    // Throw error if content-type is not 'application/json'
                    if (!$content_type || $content_type != 'application/json')
                        throw new \UnexpectedValueException("You need to send the Content-type header with 'application/json as its value'", 1);
                    
                    // Validate request body as JSON string
                    if (!Bebop::util('isJson', $request_body))
                        throw new ApiException("Request body must be a valid JSON string", 400);

                    // Get Raw body as $input
                    $input = json_decode($request_body, true);

                    // Check if using json_decode($request_body, true) returns array
                    if (!is_array($input) && !is_object($input))
                        throw new ApiException("Request body must be either a JSON object or array", 400);
                }
            }
        });

        return $this;
    }

    /**
     * Handles response when resource does not exist
     * 
     * @return void
     */
    public function handleNotFound()
    {
        self::$slim->notFound(function() {

            self::$slim->status(404);

            echo json_encode(array(
                'errors' => array(
                    array(
                        'message' => 'Resource not found',
                        'status'  => 404
                    )
                )
            ));
        });

        return $this;
    }

    /**
     * Handles exceptions
     * 
     * @return void
     */
    public function handleErrors()
    {
        self::$slim->error(function (\Exception $e) {

            if (is_a($e, '\Respect\Validation\Exceptions\ValidationException')) {

                $response = array(
                    'errors' => array(
                        array(
                            'code'    => 400,
                            'message' => $e->getFullMessage()
                        )
                    )
                );

                self::$slim->response()->body(json_encode($response));
                self::$slim->response()->status(400);

            } elseif(is_a($e, '\UnexpectedValueException')) {

                $response = array(
                    'errors' => array(
                        array(
                            'code'    => $e->getCode(),
                            'message' => $e->getMessage()
                        )
                    )
                );

                self::$slim->response()->body(json_encode($response));
                self::$slim->response()->status($e->getCode());


            } elseif (is_a($e, '\InvalidArgumentException')) {

                $response = array(
                    'errors' => array(
                        array(
                            'code'    => 400,
                            'message' => $e->getMessage()
                        )
                    )
                );

                self::$slim->response()->body(json_encode($response));
                self::$slim->response()->status(400);

            } elseif (is_a($e, '\Ponticlaro\Bebop\API\Exceptions\DefaultException')) {

                $response = array(
                    'errors' => array(
                        array(
                            'code'    => $e->getHttpStatus(),
                            'message' => $e->getMessage()
                        )
                    )
                );

                self::$slim->response()->body(json_encode($response));
                self::$slim->response()->status($e->getHttpStatus());

            } else {

                // Catch everything here
                $response = array(
                    'errors' => array(
                        array(
                            'code'    => 500,
                            'message' => $e->getMessage()
                        )
                    )
                );

                self::$slim->response()->body(json_encode($response));
                self::$slim->response()->status(500);
            }
            
        });

        return $this;
    }

    /**
     * Handles the response and set the response bodu
     * 
     * @return void
     */
    public function handleResponse()
    {
        self::$slim->hook('handle_response', function ($data) {      
            
            self::$slim->response()->body(json_encode($data)); 
        });

        return $this;
    }

    /**
     * Sets default API routes
     *
     * @return void
     */
    public function setDefaultRoutes()
    {
        // Hello World route
        self::Routes()->append('status', 'GET', '/', function() {
            
            return array('Hello World');
        });

        // Get all registered post types 
        $post_types = get_post_types(array(), 'objects');

        /////////////////////////////////////////////////
        // Add endpoints for all available posts types //
        /////////////////////////////////////////////////
        foreach ($post_types as $slug => $post_type) {

            if ($post_type->public) {

                $resource_name = Bebop::util('slugify', $post_type->labels->name);

                // Add post resource
                self::Routes()->append($resource_name, 'GET', "$resource_name/(:id)", function($id = null) use($post_type, $resource_name) {

                    if (is_numeric($id)) {

                        // Override context
                        Bebop::Context()->overrideCurrent('api/single/'. $resource_name);

                        $post = get_post($id);

                        if ($post instanceof \WP_Post) {

                            if (ModelFactory::canManufacture($post->post_type)) {
                                
                                $post = ModelFactory::create($post->post_type, array($post));
                            }

                            $response = $post;
                        }

                    } else {

                        // Override context
                        Bebop::Context()->overrideCurrent('api/archive/'. $resource_name);

                        if($resource_name != 'posts') {

                            if (isset($_GET['type'])) unset($_GET['type']);

                            if ($resource_name == 'media') {

                                if (isset($_GET['status'])) unset($_GET['status']);

                                $_GET['post_type']   = 'attachment';
                                $_GET['post_status'] = 'inherit';

                            } else {

                                $_GET['post_type'] = $post_type->query_var;
                            }
                        }

                        $response = Db::queryPosts($_GET, array('with_meta' => true));

                        if ($response['items']) {

                            foreach ($response['items'] as $index => $post) {
                                
                                if (ModelFactory::canManufacture($post->post_type)) {

                                    $response['items'][$index] = ModelFactory::create($post->post_type, array($post));
                                }
                            }
                        }
                    }

                    // Enable developers to modify response for target resource
                    $response = apply_filters("bebop:api:$resource_name:response", $response);

                    // Return response
                    return $response;
                });
                
                /////////////////////////////////////
                // Get all or individual post meta //
                /////////////////////////////////////
                self::Routes()->append("$resource_name/meta", 'GET', "$resource_name/:post_id/meta/:meta_key(/:meta_id)", function($post_id, $meta_key, $meta_id = null) use($post_type, $resource_name) {

                    // Throw error if post do not exist
                    if (!get_post($post_id) instanceof \WP_Post)
                        throw new ApiException("Target entry do not exist", 404);

                    // Get meta data
                    $post_meta = Bebop::PostMeta($post_id, array(
                        'projection' => self::$postmeta_projection
                    ));

                    $response  = $meta_id ? $post_meta->get($meta_key, $meta_id) : $post_meta->getAll($meta_key);

                    // Enable developers to modify response
                    $response = apply_filters("bebop:api:postmeta:$meta_key:response", $response, $post_id);

                    // Enable developers to modify response
                    $response = apply_filters('bebop:api:postmeta:response', $response, $meta_key, $post_id);

                    // Return response
                    return $response;
                });

                /////////////////////////////
                // Create single post meta //
                /////////////////////////////
                self::Routes()->append("$resource_name/meta", 'POST', "$resource_name/:post_id/meta/:meta_key", function($post_id, $meta_key) {

                    // Check if current user can edit the target post
                    if (!current_user_can('edit_post', $post_id))
                        throw new ApiException("You cannot edit the target entry", 403);
                        
                    // Get request body
                    $data = json_decode(self::$slim->request()->getBody(), true);

                    // Throw error if payload is null
                    if (is_null($data))
                        throw new ApiException("You cannot send an empty request body", 400);

                    // Defined storage method
                    $storage_method = isset($_GET['storage_method']) ? $_GET['storage_method'] : 'json';

                    // Check storage type
                    if (!in_array($storage_method, array('json', 'serialize')))
                        throw new ApiException("Storage method needs to be either 'json' or 'serialize'", 400);

                    // Throw error if post do not exist
                    if (!get_post($post_id) instanceof \WP_Post)
                        throw new ApiException("Target entry do not exist", 404);

                    // Instantiate PostMeta object
                    $post_meta = Bebop::PostMeta($post_id, array(
                        'projection' => self::$postmeta_projection
                    ));

                    // Add new meta row
                    $new_item = $post_meta->add($meta_key, $data, $storage_method);

                    // Throw error if it was not able to create new postmeta item
                    if (!$new_item)
                        throw new ApiException("Failed to create new postmeta item", 500);

                    // Return response
                    return $new_item;
                });
                
                /////////////////////////////
                // Update single post meta //
                /////////////////////////////
                self::Routes()->append("$resource_name/meta", 'PUT', "$resource_name/:post_id/meta/:meta_key/:meta_id", function($post_id, $meta_key, $meta_id) {

                    // Check if current user can edit the target post
                    if (!current_user_can('edit_post', $post_id))
                        throw new ApiException("You cannot edit the target entry", 403);

                    // Get request body
                    $data = json_decode(self::$slim->request()->getBody(), true);

                    // Throw error if payload is null
                    if (is_null($data))
                        throw new ApiException("You cannot send an empty request body", 400);

                    // Defined storage method
                    $storage_method = isset($_GET['storage_method']) ? $_GET['storage_method'] : 'json';

                    // Check storage type
                    if (!in_array($storage_method, array('json', 'serialize')))
                        throw new ApiException("Storage method needs to be either 'json' or 'serialize'", 400);

                    // Throw error if post do not exist
                    if (!get_post($post_id) instanceof \WP_Post)
                        throw new ApiException("Target entry do not exist", 404);

                    // Instantiate PostMeta object
                    $post_meta = Bebop::PostMeta($post_id, array(
                        'projection' => self::$postmeta_projection
                    ));

                    // Update Meta
                    $updated_item = $post_meta->update($meta_key, $meta_id, $data, $storage_method);

                    // Throw error if it was not able to update the target postmeta item
                    if (!$updated_item)
                        throw new ApiException("Failed to update postmeta item", 500);

                    // Return updated item
                    return $updated_item;
                });

                /////////////////////////////
                // Delete single post meta //
                /////////////////////////////
                self::Routes()->append("$resource_name/meta", 'DELETE', "$resource_name/:post_id/meta/:meta_key/:meta_id", function($post_id, $meta_key, $meta_id) use($post_type, $resource_name) {

                    // Check if current user can edit the target post
                    if (!current_user_can('edit_post', $post_id))
                        throw new ApiException("You cannot edit the target entry", 403);

                    // Throw error if post do not exist
                    if (!get_post($post_id) instanceof \WP_Post)
                        throw new ApiException("Target entry do not exist", 404);

                    // Instantiate PostMeta object
                    $post_meta = Bebop::PostMeta($post_id, array(
                        'projection' => self::$postmeta_projection
                    ));

                    // Delete post meta
                    $remaining_items = $post_meta->delete($meta_key, $meta_id);

                    // Return remaining items
                    return $remaining_items;
                });

            }

            // Add endpoint to inform about available endpoints
            self::Routes()->append('resources', 'GET', "_resources", function() use($post_types) {

                if (!current_user_can('manage_options')) {
            
                    self::$slim->halt(403, json_encode(array(
                        'error' => array(
                            'status' => 403,
                            'message' => "You're not an authorized user."
                        )
                    )));

                    exit;
                }

                // Loop through all defined routes
                foreach (Routes::getAll() as $route) {

                    $resources[] = array(
                        'id'       => $route->getId() .':'. $route->getMethod(), 
                        'method'   => strtoupper($route->getMethod()),
                        'endpoint' => '/_bebop/api/'. ltrim($route->getPath(), '/')
                    );
                }

                // Return resources
                return $resources;
            });
        }

        return $this;
    }

    /**
     * Starts router 
     * 
     * @return void
     */
    public function run()
    {
        // Remove WordPress Content-Type header
        header_remove('Content-Type');

        $router = self::getInstance();
        $router->handleErrors()
               ->preFlightCheck()
               ->handleNotFound()
               ->handleResponse();

        // Loop through all defined routes
        foreach (Routes::getAll() as $route) {

            self::$slim->{$route->getMethod()} (self::BASE_URL . rtrim(ltrim($route->getPath(), '/'), '/') .'/', function () use ($route) {

                // Get data from route function
                $data = call_user_func_array($route->getFunction(), func_get_args());

                // Enable developers to modify global response
                $data = apply_filters('bebop:api:response', $data);

                // Send response
                self::$slim->applyHook('handle_response', $data);
            });
        }

        self::$slim->run();
    }
}