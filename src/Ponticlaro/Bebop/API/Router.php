<?php

namespace Ponticlaro\Bebop\API;

use Ponticlaro\Bebop\Resources\Models\Attachment;

class Router {

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

		$app->get('/_bebop/api/posts(/)(:id)(/)', function($id = null) use($app) {

			$response = array();

			if ($id) {

				$post = get_post($id);

				if ($post instanceof \WP_Post) {

					if ($post->post_type == 'attachment') {
						
						$post = new Attachment($post);
					}

					$response = $post;
				}
			
			} else {

				// TO DO
			}

			// Send response
			$app->applyHook('handle_response', $response);
		});

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
}