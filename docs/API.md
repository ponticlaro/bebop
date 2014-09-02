API
---
The Bebop RESTful API allows you to query for all the content on your database.

## Base URL
The URL for all resources will be prefixed with `/_bebop/api/`.

## Default Resources
### Status Resources
Read [this](API/STATUS_RESOURCES.md) documentation file

### Post Type Resources
Read [this](API/POST_TYPE_RESOURCES.md) documentation file

### Post Meta Resources
Read [this](API/POST_META_RESOURCES.md) documentation file

### Options Resources
*Not implemented*

## Customization

### Getting the API Object
The API object is a singleton with several components that allow API customization.  

```php
$api = \Ponticlaro\Bebop::Api();
```

### Getting the Slim Framework instance

```php
$slim = \Ponticlaro\Bebop::Api()->slim();
```

### Getting the Router instance
The Router object handles all API requests, responses, authentication, error handling, etc...

```php
$router = \Ponticlaro\Bebop::Api()->router();
```

### Getting the Routes instance
The Routes object is used to manage routes.

```php
$routes = \Ponticlaro\Bebop::Api()->routes();
```

### Adding resources/routes
```php
$api = \Ponticlaro\Bebop::Api();

// GET resource
$api->get('custom/:id/child-pages(/)', function($id) {
        
    // Your code goes here.
    
    return $response;
});

// POST resource
$api->post('custom(/)', function() {
        
    // Your code goes here.
    
    return $response;
});

// PUT resource
$api->put('custom/:id(/)', function($id) {
        
    // Your code goes here.
    
    return $response;
});

// PATCH resource
$api->patch('custom/:id(/)', function($id) {
        
    // Your code goes here.
    
    return $response;
});

// DELETE resource
$api->delete('custom/:id(/)', function($id) {
        
    // Your code goes here.
    
    return $response;
});
```

**NOTE:** Inside the resource/route function you need to return the desired response. That response should be either an object or an array and will be converted to JSON when a request is made.

### Managing response headers
If you need to add/remove/modify headers, you need to first get the Slim Framework instance and use it to make any necessary changes:

```php
$slim = \Ponticlaro\Bebop::Api()->slim();

// Modify content-type header
$slim->response()->header('Content-Type', 'text/html');
```

### Modifying responses using filter hooks
The Bebop Api comes with several built-in hooks that allows you to modify responses.

#### Hooking into ALL responses
```php
add_filter('bebop:api:response', function($response) {

    // Modify response here

    return $response;
});
```

#### Hooking into post-type responses
```php
add_filter('bebop:api:$resource_name:response', function($response) {

    // Modify response here

    return $response;
});
```

**Examples of $resource_name:**
- posts
- pages
- events (hypothetical custom post-type)
- products (hypothetical custom post-type)
- projects (hypothetical custom post-type)

#### Hooking into ALL post-meta response
```php
add_filter('bebop:api:postmeta:response', function($response) {

    // Modify response here

    return $response;
});
```

#### Hooking into target post-meta meta_key response
```php
add_filter('bebop:api:postmeta:$meta_key:response', function($response) {

    // Modify response here

    return $response;
});
```