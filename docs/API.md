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

## Resources
### Status Resources
#### GET /_bebop/api/
This should be used to check if the API is working properly.  
It will return an array with a single item: **Hello World**.  

#### GET /_bebop/api/_resources
This resource will return a list of registered post types and correspondent base resource.  
**Note:** You need to be logged into WordPress as and administrator to be able to access this resource.  

### Default Resources
Bebop will create the following endpoints for each registered post type:
- GET /_bebop/api/**{ post_type }**/
- GET /_bebop/api/**{ post_type }**/:id/
- GET /_bebop/api/**{ post_type }**/:id/meta/:key/

## Querying
### Querying Posts Types
#### Available query parameters
As query parameters you can use all parameters you would use for a new instance of [`WP_Query`](http://codex.wordpress.org/Class_Reference/WP_Query#Parameters).  

Here is a list of the most relevant parameters:
  
- `page`
- `max_results` or `posts_per_page`
- `post_type` or `type`
- `post_status` or `status`
- `post_mime_type` or `mime_type`
- `post_parent` or `parent`
- `orderby` or `sort_by`
- `order` or `sort_direction`

##### Pagination
- Use the `page` parameter as it is mapped to the internal `paged` argument for `WP_Query`.
- Check the `meta['has_more']` parameter to know if you have more pages.

##### Tax queries
If you need to use the [`tax_query`](http://codex.wordpress.org/Class_Reference/WP_Query#Taxonomy_Parameters) argument for `WP_Query` you need to use one of the following structures to build a query parameter:
- tax:{{taxonomy_slug}}={{term(s)}}
- tax:{{tax_query_json}}={{term(s)}}

###### Notes:
- You can specify the relation of tax queries with `tax_relation` query parameter.   
- You can specify comma separated values.

###### Simple query with `IN` as compare operator:

	meta:manufacturer=ford

###### Complex query with multiple values: 

	tax:{"taxonomy":"year","field":"slug","operator":"IN"}=2012,2013,2014

After the `tax:` segment, you need to add a JSON object like:

```json
{
    "taxonomy": "year",
    "field": "slug",
    "operator": "IN"
}
```

#### Meta Queries
##### Structure
If you need to use the [`meta_query`](http://codex.wordpress.org/Class_Reference/WP_Query#Custom_Field_Parameters) argument for `WP_Query` you need to use one of the following structures to build a query parameter:
- meta:{{meta_key}}={{meta_value(s)}}
- meta:{{meta_query_json}}={{meta_value(s)}}

##### Notes:
- You can specify the relation of meta queries with `meta_relation` query parameter.  
- You can specify comma separated values.

##### Examples
###### Simple query with `=` as compare operator:

	meta:download_enabled=1

###### Complex query with multiple values: 

	meta:{"key":"latitude","compare":"between","type":"numeric"}=35,45

After the `meta:` segment, you need to add a JSON object like:

```json
{
    "key": "latitude",
    "compare": "between",
    "type": "numeric"
}
```

#### Response Structure
All resources that are not for a specific object ID, will return the following structure.

```json
{
    "meta": {
    	"items_total": 20,
    	"items_returned": 10,
    	"total_pages": 2,
    	"current_page": 1,
    	"has_more": true,
    	"previous_page": null,
    	"next_page": "?page=2"
    },
    "items": [
    	// Array or objects
    ]
}
```
	
### Querying Meta
