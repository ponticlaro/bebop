<?php

namespace Ponticlaro\Bebop\API;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\Resources\Models\Attachment;

class Router {

	/**
	 * Slim app container
	 * 
	 * @var object Slim\Slim
	 */
	private $app;

	public function __construct()
	{
		// Remove WordPress Content-Type header
		header_remove('Content-Type');

		// Instantiate Slim
		$app = new \Slim\Slim;

		// Set Response content-type header
		$app->response()->header('Content-Type', 'application/json');

		// Handle not found
		$app->notFound(function() use ($app) {

			$app->status(404);

			echo json_encode(array(
				'errors' => array(
					array(
						'message' => 'Resource not found',
						'status'  => 404
					)
				)
			));
		});

		// Pre-flight check 
		$app->hook('slim.before', function($data) use($app) {		 
			
		}); 

		// Routes
		$app->get('/_bebop/api(/)', function() use($app) {
			
			$data = array('Hello' => true);

			// Get response object
			$response = $app->response();

			// Send response
			$response->body(json_encode($data)); 
		});

		$post_types = get_post_types(array(), 'objects');

		// Add endpoint to inform about available endpoints
		$app->get("/_bebop/api/_resources(/)", function() use($app, $post_types) {

			if (!current_user_can('manage_options')) {
		
				$app->halt(403, json_encode(array(
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

			// Send response
			$app->applyHook('handle_response', $resources);
		});

		/////////////////////////////////////////////////
		// Add endpoints for all available posts types //
		/////////////////////////////////////////////////
		foreach ($post_types as $slug => $post_type) {
			
			$resource_name = Bebop::util('slugify', $post_type->labels->name);

			$app->get("/_bebop/api/$resource_name(/)(:id)(/)", function($id = null) use($app, $post_type, $resource_name) {

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

					$query = self::__queryDb();
					$meta  = self::__getPaginationMeta($query, $resource_name);
					
					$response = array(
						'meta'  => $meta,
						'items' => $query->posts
					);
				}

				// Enable developers to modify response
				$response = apply_filters('bebop:api:response', $response);

				// Send response
				$app->applyHook('handle_response', $response);
			});
		}

		// Handle Response 
		$app->hook('handle_response', function($data) use($app) {		 
			
			// Send response
			$app->response()->body(json_encode($data)); 
		});

		// Error Handling 
		$app->error(function (\Exception $e) use ($app) {

		});

		// Run SLIM app 
		$app->run();
	}

	private static function __queryDb()
	{
		// Map short references to full query argument key
		$params_map = array(
			'type'           => 'post_type',
			'status'         => 'post_status',
			'parent'         => 'post_parent',
			'mime_type'      => 'post_mime_type',
			'max_results'    => 'posts_per_page',
			'sort_by'        => 'orderby',
			'sort_direction' => 'order',
			'page'           => 'paged'
		);

		$raw_params      = $_GET;
		$filtered_params = array(

			'tax_query' => array(

				'relation' => isset($raw_params['tax_relation']) ? $raw_params['tax_relation'] : 'AND'
			),

			'meta_query' => array(

				'relation' => isset($raw_params['meta_relation']) ? $raw_params['meta_relation'] : 'AND'
			)
		);

		foreach ($raw_params as $key => $value) {

			// Check for tax query params
			if (preg_match('/^tax\:/', $key)) {

				$data_string = str_replace('tax:', '', $key);

				if ($data_string) {
					
					$data = null;
					
					if (Bebop::util('isJson', $data_string)) {
						
						$data = $data_string ? json_decode($data_string, true) : null;
						
					} else {

						$data = array('taxonomy' => $data_string);
					}

					if ($data) {

						if (!isset($data['operator'])) $data['operator'] = 'IN';
						if (!isset($data['field'])) $data['field'] = 'slug';

						$data['terms'] = array();

						$values = explode(',', $value);

						foreach ($values as $value) {
							$data['terms'][] = $value;
						}

						$filtered_params['tax_query'][] = $data;
					}
				}

			// Check for meta query params
			} elseif (preg_match('/^meta\:/', $key)) {

				$data_string = str_replace('meta:', '', $key);

				if ($data_string) {
					
					$data = null;
					
					if (Bebop::util('isJson', $data_string)) {
						
						$data = $data_string ? json_decode($data_string, true) : null;
						
					} else {

						$data = array('key' => $data_string);
					}

					if ($data) {

						if (!isset($data['compare'])) $data['compare'] = '=';
						if (!isset($data['type'])) $data['type'] = 'CHAR';
		
						$data['value'] = $value;

						$filtered_params['meta_query'][] = $data;
					}
				}

			} else {

				// Check if we should map a query parameter to a built-in query parameter
				if (array_key_exists($key, $params_map)) $key = $params_map[$key];

				$filtered_params[$key] = $value;
			}
		}

		return new \WP_Query($filtered_params);
	}

	public static function __getPaginationMeta(\WP_Query $query, $resource_name)
	{
		$meta  = array();
		$posts = $query->posts;

		$meta['items_total']    = (int) $query->found_posts;
		$meta['items_returned'] = (int) $query->post_count;
		$meta['total_pages']    = (int) $query->max_num_pages;
		$meta['current_page']   = (int) max(1, $query->query_vars['paged']);
		$meta['has_more']       = $meta['current_page'] == $meta['total_pages'] || $meta['total_pages'] == 0 ? false : true;

		$params = $_GET;

		// Remove post_type parameter when not querying the /posts resource
		if ($resource_name != 'posts' && isset($params['post_type'])) {

			unset($params['post_type']);
		} 

		$meta['previous_page'] = $meta['current_page'] == 1 ? null : self::__buildPreviousPageUrl($params);
		$meta['next_page']     = $meta['current_page'] == $meta['total_pages'] || $meta['total_pages'] == 0 ? null : self::__buildNextPageUrl($params);

		return $meta;
	}

	private static function __buildPreviousPageUrl(array $params = array())
	{
		$params['page'] = isset($params['page']) ? $params['page'] - 1 : 1; 

		return '?'. self::__buildQuery($params);
	}

	private static function __buildNextPageUrl(array $params = array())
	{
		$params['page'] = isset($params['page']) ? $params['page'] + 1 : 2;

		return '?'. self::__buildQuery($params);
	}

	private static function __buildQuery(array $params = array())
	{
		array_walk($params, function(&$value, $key) {
			$value = urldecode($value);
		});

		return http_build_query($params);
	}
}