Bebop
==================
[![Build Status](https://travis-ci.org/ponticlaro/bebop.svg?branch=feature/ci)](https://travis-ci.org/ponticlaro/bebop)
[![Latest Stable Version](https://poser.pugx.org/ponticlaro/bebop/v/stable.png)](https://packagist.org/packages/ponticlaro/bebop)
[![License](https://poser.pugx.org/ponticlaro/bebop/license.png)](https://packagist.org/packages/ponticlaro/bebop)
[![Total Downloads](https://poser.pugx.org/ponticlaro/bebop/downloads.png)](https://packagist.org/packages/ponticlaro/bebop)

## Usage Goals & Examples

### Boot tools

    use Ponticlaro\Bebop;
    
    Bebop::boot();

### Urls & Paths tools
#### Get Urls & Paths
Some default urls and paths are added when booting Bebop

    Bebop::getUrl();
    Bebop::getUrl('/');
    Bebop::getUrl('theme');
    Bebop::getUrl('uploads');
    Bebop::getUrl('theme', 'app.js');
    Bebop::getUrl('css', 'main.css');
    Bebop::getUrl('theme', 'assets/i/common/logo.png');
    
    Bebop::getPath();
    Bebop::getPath('/');
    Bebop::getPath('theme');
    Bebop::getPath('images');
    Bebop::getPath('js', 'app.js');
    Bebop::getPath('css', 'main.css');
    Bebop::getPath('theme', 'assets/i/common/logo.png');

#### Set Urls & Paths
	
	// Store a bunch of custom and reusable urls, based on already exiting urls
	Bebop::setUrl(array(
		"theme_css"    => Bebop::getUrl("theme", "assets/css"),
		"theme_js"     => Bebop::getUrl("theme", "assets/js"),
		"theme_fonts"  => Bebop::getUrl("theme", "assets/fonts"),
		"theme_images" => Bebop::getUrl("theme", "assets/i"),
		"favicon"      => Bebop::getUrl("theme", "favicon.ico")
	));
	
	// Storing a single url
	Bebop::setUrl('theme_assets', Bebop::getUrl("theme", "assets));
    
	// or
	Bebop::setUrlThemeAssets( Bebop::getUrl("theme", "assets) );

### HTTP API

    use \Ponticlaro\Wptools\Http;
    
    // Instantiate new client with a base URL
    $http = new Http\Client('https://api.base-url.com');
    
    // Set Authorization header
    $http->setAuth('Basic '. base64_encode('username:password'));
    
    // Request an endpoint
    $response = $http->get('account');
    
    // Get HTTP status code
    $response->getCode();
    
    // Get HTTP status message
    $response->getMessage();
    
    // Get response headers 
    $response->getHeaders();
    
    // Get a specific response header
    $response->getHeader('content-type');
    
    // Get response body
    $response->getBody();
    
    // Echo response body (relies on __toString magic method)
    echo $response;

### Easily use WordPress API with wrapper functions 

#### Example wrapper for the [Administration Menus API](http://codex.wordpress.org/Administration_Menus)

##### Simple instantiation based on defaults 

    Bebop::AdminPage('Admin Page title', function() 
    {    
        // Your admin page here
    });

##### Simple instantiation, but as a child of the 'Settings' page

    Bebop::AdminPage('Admin Page title', function() 
    {    
        // Your admin page here
    }, 
    array(
        'parent' => 'settings'
    ));

##### Full configuration

    Bebop::AdminPage('Admin Page title', 'function', array(
    	'capability' => 'read',
    	'menu_slug'  => 'custom-admin-page',
    	'icon_url'   => '',
    	'position'   => '',
    	'parent'     => ''
    ));

#### Already working wrapper for the [Options API](http://codex.wordpress.org/Options_API) 

	// Instantiate option object
	//
	// First parameter is the key stored in database
	//
	// Second parameter expects an associative array of data or a boolean value to set the enabled/disable the autosave feature, whenever a set or remove method is called 
	//
	// Third parameter expects a boolean to enabled/disable the autosave feature adn should only be used if you passed an associative array of data in the second parameter
	
    $option = new \Ponticlaro\Bebop\Database\Option('option_hook', $data_array, true);
	// or
	$option = Ponticlaro\Bebop::createOption('option_hook', $data_array, true);

	// You can also use the method bellow to enable the autosave feature
    $option->setConfig('autosave', true);

	// Fetch existing data in database, if any
	// Used during instantiation, so that the object is instantiated with the existing database data
    $option->fetch();

	// Set data from associative array
    $option->set(array(
    	'key1' => 'value1', 
		'key2' => 'value2',
		'key3' => 'value3'
	));

	// Set single key
    $option->set('key', 'value1');

	// Get all data
    $option->get();

	// Get specific data
	$option->get(array('key1', 'key2'. 'key3'));
    $option->get('key');

	// Unset keys
    $option->remove(array('key1', 'key2'. 'key3'));
    $option->remove('key');

	// Manually store current object data in database
    $option->save();

	// Remove data from database
    $option->destroy();