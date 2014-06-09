<?php

namespace Ponticlaro\Bebop\API;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\API\Exceptions\DefaultException AS ApiException;
use Ponticlaro\Bebop\DB;
use Ponticlaro\Bebop\Patterns\SingletonAbstract;
use Ponticlaro\Bebop\Resources\Models\Attachment;
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
	 * Instantiates Router
	 */
	protected function __construct()
	{
		// Instantiate Slim
		self::$slim = new \Slim\Slim(array(
			'debug' => false
		));

		// Set Response content-type header
		self::$slim->response()->header('Content-Type', 'application/json');
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
	public static function preFlightCheck()
	{
		self::$slim->hook('slim.before', function() {		 
			
			$request      = self::$slim->request();
			$method       = $request->getMethod();
			$resourceUri  = $request->getResourceUri();
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
	}

	/**
	 * Handles response when resource does not exist
	 * 
	 * @return void
	 */
	public static function handleNotFound()
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
	}

	/**
	 * Handles exceptions
	 * 
	 * @return void
	 */
	public static function handleErrors()
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
	}

	/**
	 * Handles the response and set the response bodu
	 * 
	 * @return void
	 */
	public static function handleResponse()
	{
		self::$slim->hook('handle_response', function ($data) {		 
			
			self::$slim->response()->body(json_encode($data)); 
		});
	}

	/**
	 * Sets default API routes
	 *
	 * @return void
	 */
	public static function setDefaultRoutes()
	{
		// Hello World route
		self::Routes()->set('GET', '/', function() {
			
			return array('Hello World');
		});

		// Get all registered post types 
		$post_types = get_post_types(array(), 'objects');

		// Add endpoint to inform about available endpoints
		self::Routes()->set('GET', "_resources", function() use($post_types) {

			if (!current_user_can('manage_options')) {
		
				self::$slim->halt(403, json_encode(array(
					'error' => array(
						'status' => 403,
						'message' => "You're not an authorized user."
					)
				)));

				exit;
			}

			$home      = Bebop::getUrl('home');
			$resources = array();

			foreach ($post_types as $post_type) {

				$resources[] = array(
					'name' => $post_type->labels->name, 
					'url'  => $home .'/_bebop/api/'. Bebop::util('slugify', $post_type->labels->name)
				);
			}

			// Return resources
			return $resources;
		});

		/////////////////////////////////////////////////
		// Add endpoints for all available posts types //
		/////////////////////////////////////////////////
		foreach ($post_types as $slug => $post_type) {
			
			$resource_name = Bebop::util('slugify', $post_type->labels->name);

			// Add post resource
			self::Routes()->set('GET', "$resource_name(/)(:id)", function($id = null) use($post_type, $resource_name) {

				if (is_numeric($id)) {

					$post = get_post($id);

					if ($post instanceof \WP_Post) {

						if ($post->post_type == 'attachment') {
							
							$post = new Attachment($post);
						}

						$response = $post;
					}

				} else {

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

					$response = DB::query('posts', $_GET, array('with_meta' => true));
				}

				// Enable developers to modify response for target resource
				$response = apply_filters("bebop:api:$resource_name:response", $response);

				// Return response
				return $response;
			});
			
			////////////////////////////
			// Add meta data resource //
			////////////////////////////

			// GET
			self::Routes()->set('GET', "$resource_name/:id/meta/:key(/)", function($id, $key) use($post_type, $resource_name) {

				// Throw error if post do not exist
				if (!get_post($id) instanceof \WP_Post)
					throw new ApiException("Target entry do not exist", 404);

				// Get meta data
				$response = Bebop::PostMeta($id)->get($key);

				// Enable developers to modify response
				$response = apply_filters('bebop:api:postmeta:response', $response, $key, $id);

				// Return response
				return $response;
			});

			// POST
			self::Routes()->set('POST', "$resource_name/:id/meta/:key(/:storage_method)", function($id, $key, $storage_method = 'json') use($post_type, $resource_name) {

				// Check if current user can edit the target post
				if (!current_user_can('edit_post', $id))
					throw new ApiException("You cannot edit the target entry", 403);
					
				// Get request body
				$data = json_decode(self::$slim->request()->getBody(), true);

				// Throw error if payload is null
				if (is_null($data))
					throw new ApiException("You cannot send an empty request body", 400);

				// Check storage type
				if (!in_array($storage_method, array('json', 'serialize')))
					throw new ApiException("Storage method needs to be either 'json' or 'serialize'", 400);

				// Throw error if post do not exist
				if (!get_post($id) instanceof \WP_Post)
					throw new ApiException("Target entry do not exist", 404);

				// Instantiate PostMeta object
				$post_meta = Bebop::PostMeta($id);

				// Loop through all data
				if (empty($data)) {
					
					// Delete all entries
					$post_meta->delete($key);
				}

				else {

					// Delete all entries
					$post_meta->delete($key);

					foreach ($data as $value) {
						
						// Encode value as JSON if that is the desired method
						if ($storage_method == 'json' && (is_object($value) || is_array($value))) $value = json_encode($value);

						// Add single entry with same meta_key
						$post_meta->add($key, $value);
					}
				}

				// Return response
				return $post_meta->get($key);
			});
		}
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

		self::handleErrors();
		self::preFlightCheck();
		self::handleNotFound();
		self::handleResponse();

		// Set all default routes on instantiation
		// so that users can modify then before running the router
		self::setDefaultRoutes();

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