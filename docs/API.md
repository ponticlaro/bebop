API
---
The Bebop RESTful API allows you to query for all the content on your database.

## Base URL
The URL for all resources will be prefixed with `/_bebop/api/`.

## Customization

### Getting the API Object
The API object is a singleton with several components that allow API customization.  

```php
$api = \Ponticlaro\Bebop::API();
```

### Getting the Router Object
The Router object is a singleton that handles all API requests, responses, authentication, error handling, etc...

```php
$router = \Ponticlaro\Bebop::API()->Router();
```

### Getting the Routes Object
The Routes object is a singleton that is used to manage routes.

```php
$routes = \Ponticlaro\Bebop::API()->Routes();
```

### Adding/Replacing resources
You can either add or replace an existing route by using the `Routes` object.  
You just need to define these for the route:  
- **$method**: GET, POST, PUT, PATCH, HEAD, OPTIONS, etc...
- **$path**: URL path. Will be automatically prefixed with `/_bebop/api/`
- **$function**: The function that will handle the route. All route parameters will be correctly passed to this function.  
```php
$routes = \Ponticlaro\Bebop::API()->Routes();
    
$routes->set('get', 'pages/:id/child-pages', function($id) {
        
    // Your code goes here.
    
    return $response;
});
```

## Available Resources
### Status Resources
Read [this](API/STATUS_RESOURCES.md) documentation file

### Post Type Resources
Read [this](API/POST_TYPE_RESOURCES.md) documentation file

### Post Meta Resources
Read [this](API/POST_META_RESOURCES.md) documentation file

### Options Resources
*Not implemented*