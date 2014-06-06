<?php

namespace Ponticlaro\Bebop\API;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\DB;
use Ponticlaro\Bebop\Patterns\SingletonAbstract;
use Ponticlaro\Bebop\Resources\Models\Attachment;

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
		self::$slim = new \Slim\Slim;

		// Set Response content-type header
		self::$slim->response()->header('Content-Type', 'application/json');

		// Set all default routes on instantiation
		// so that users can modify then before running the router
		self::setDefaultRoutes();
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
		self::$slim->hook('slim.before', function($data) {		 
			
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
			self::Routes()->set('GET', "$resource_name(/)(:id)(/)", function($id = null) use($post_type, $resource_name) {

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
			
			// Add meta data resource
			self::Routes()->set('GET', "$resource_name/:id/meta/:key(/)", function($id, $key) use($post_type, $resource_name) {

				// Get meta data
				$data = get_post_meta($id, $key, false);

				// Decode JSON strings
				foreach ($data as $index => $entry) {
						
					if (Bebop::util('isJson', $entry)) $data[$index] = json_decode($entry);
				}

				// Enable developers to modify response
				$response = apply_filters('bebop:api:meta:response', $data);

				// Return response
				return $response;
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

		self::preFlightCheck();
		self::handleNotFound();
		self::handleErrors();
		self::handleResponse();

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