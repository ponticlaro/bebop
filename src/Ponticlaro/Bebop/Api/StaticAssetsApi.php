<?php 

namespace Ponticlaro\Bebop\Api;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\Api\WpApi;

class StaticAssetsApi {

	public function __construct($directory)
	{
		// Set Bebop static assets URL
		Bebop::setPath('_bebop/static', $directory);

		// Set Bebop static assets directory
        Bebop::setUrl('_bebop/static', Bebop::getUrl('home', '_bebop/static'));

		$api = new WpApi('bebop_static_assets');
        $api->setBaseUrl('_bebop/static');

        // Set unique resource to capture all requests
        $api->get('/.*?', function() use($api) {

            // Get slim instance
            $slim = $api->slim();

            // Find asset path
            $relative_path = str_replace('/_bebop/static', '', $slim->request()->getResourceUri());
            $path          = Bebop::getPath('_bebop/static', $relative_path);

            // Search for target files
            $list = glob($path .'.*');

            // Return 404 if asset do not exist
            if (!$list) {
                
                $slim->halt(404, json_encode(array(
                    'message' => "You're looking for a Bebop asset that do not exist"
                )));
            }
            
            $path = $list[0];

            // Check file extension
            $file_parts = pathinfo($path);

            switch($file_parts['extension'])
            {
                case "css":

                    $content_type = 'text/css; charset=UTF-8';
                    break;

                case "js":

                    $content_type = 'application/javascript; charset=UTF-8';
                    break;

                default:
                    $content_type = 'text/html; charset=UTF-8';
                    break;
            }

            // Set response headers
            $slim->response()->header('Content-Type', $content_type);
            $slim->response()->header('Content-Length', filesize($path));
            $slim->response()->header('Cache-Control', 'public, max-age=31536000');

            // Do not JSON encode response
            $slim->hook('handle_response', function ($data) use($slim) {      
            
                $slim->response()->body($data); 
            });

            // Return file content
            return file_get_contents($path);
        });
	}
}