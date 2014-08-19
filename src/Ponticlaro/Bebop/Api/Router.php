<?php

namespace Ponticlaro\Bebop\Api;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\Api\Exceptions\DefaultException AS ApiException;
use Ponticlaro\Bebop\Api\Routes;
use Respect\Validation\Validator as v;

class Router {

    /**
     * Rewrite tag
     * 
     * @var string
     */
    protected $rewrite_tag;

    /**
     * Base URL for all routes
     * 
     * @var string
     */
    protected $base_url;

    /**
     * Slim instance
     * 
     * @var object Slim\Slim
     */
    protected $slim;

    /**
     * List of routes
     * 
     * @var Ponticlaro\Bebop\Common\Collection;
     */
    protected $routes;

    /**
     * Instantiates Router
     * 
     */
    public function __construct($rewrite_tag, $base_url)
    {
        // Save rewrite tag
        $this->rewrite_tag = $rewrite_tag;

        // Save base URL
        $this->base_url = '/' . ltrim($base_url, '/');

        // Instantiate Routes object
        $this->routes = new Routes;

        // Instantiate Slim
        $this->slim = new \Slim\Slim(array(
            'debug' => true
        ));
    }

    /**
     * Returns Slim instance
     * 
     */
    public function slim()
    {
        return $this->slim;
    }

    /**
     * Returns Routes manager instance
     * 
     */
    public function routes()
    {
        return $this->routes;
    }

    /**
     * Does a pre-flight check
     * 
     * @return void
     */
    public function preFlightCheck()
    {
        $this->slim->hook('slim.before', function() {        
        
            $request      = $this->slim->request();
            $method       = $request->getMethod();
            $resource_uri = $request->getResourceUri();
            $content_type = $request->headers->get('CONTENT_TYPE');
            $request_body = $request->getBody();

            // var_dump($method);
            // var_dump($resource_uri);
            // var_dump($this->slim->router);
            // die;

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
        $this->slim->notFound(function() {

            $this->slim->status(404);

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
        $this->slim->error(function (\Exception $e) {

            if (is_a($e, '\Respect\Validation\Exceptions\ValidationException')) {

                $response = array(
                    'errors' => array(
                        array(
                            'code'    => 400,
                            'message' => $e->getFullMessage()
                        )
                    )
                );

                $this->slim->response()->body(json_encode($response));
                $this->slim->response()->status(400);

            } elseif(is_a($e, '\UnexpectedValueException')) {

                $response = array(
                    'errors' => array(
                        array(
                            'code'    => $e->getCode(),
                            'message' => $e->getMessage()
                        )
                    )
                );

                $this->slim->response()->body(json_encode($response));
                $this->slim->response()->status($e->getCode());


            } elseif (is_a($e, '\InvalidArgumentException')) {

                $response = array(
                    'errors' => array(
                        array(
                            'code'    => 400,
                            'message' => $e->getMessage()
                        )
                    )
                );

                $this->slim->response()->body(json_encode($response));
                $this->slim->response()->status(400);

            } elseif (is_a($e, '\Ponticlaro\Bebop\Api\Exceptions\DefaultException')) {

                $response = array(
                    'errors' => array(
                        array(
                            'code'    => $e->getHttpStatus(),
                            'message' => $e->getMessage()
                        )
                    )
                );

                $this->slim->response()->body(json_encode($response));
                $this->slim->response()->status($e->getHttpStatus());

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

                $this->slim->response()->body(json_encode($response));
                $this->slim->response()->status(500);
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
        $this->slim->hook('handle_response', function ($data) {      
            
            $this->slim->response()->body(json_encode($data)); 
        });

        return $this;
    }

    public function __call($name, $args)
    {
        if (method_exists($this->slim, $name))
            return call_user_method_array($name, $this->slim, $args);

        return $this;
    }

    /**
     * Starts router 
     * 
     * @return void
     */
    public function run()
    {
        // Remove WordPress Content-Type header & Set Slim response content-type header
        header_remove('Content-Type');
        $this->slim->response()->header('Content-Type', 'application/json');

        $this->handleErrors()
             ->preFlightCheck()
             ->handleNotFound()
             ->handleResponse();

        $rewrite_tag = $this->rewrite_tag;
        $base_url    = $this->base_url;

        // Loop through all defined routes
        foreach ($this->routes->getAll() as $route) {

            $this->slim->{$route->getMethod()} ($base_url . rtrim(ltrim($route->getPath(), '/'), '/'), function () use ($route, $rewrite_tag) {

                // Get data from route function
                $data = call_user_func_array($route->getFunction(), func_get_args());

                // Enable developers to modify global response
                $data = apply_filters($rewrite_tag . ':response', $data);

                // Send response
                $this->slim->applyHook('handle_response', $data);
            });
        }

        $this->slim->run();
    }
}