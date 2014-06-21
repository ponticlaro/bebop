HTTP Client
---
The is the documentation for the [HTTP API](http://codex.wordpress.org/HTTP_API) wrapper.  

## Instantiation
### New client
```php
use \Ponticlaro\Bebop\Http\Client as HttpClient;

$client = new HttpClient();
```

### New client with a base URL for all requests
```php
use \Ponticlaro\Bebop\Http\Client as HttpClient;

$client = new HttpClient('https://api.base-url.com');
```

## Requests
### Setting Authorization header
$client->setAuth('Basic '. base64_encode('username:password'));
```

## Making requests
```php
$response = $client->get('account');
$response = $client->post('account');
$response = $client->put('account');
$response = $client->patch('account');
$response = $client->delete('account');
```
## Responses
Each request will return a `\Ponticlaro\Bebop\Http\Response` instance.  

### Get HTTP status code
```php
$response->getCode();
```

### Get HTTP status message
```php
$response->getMessage();
```

### Get response headers 
```php
$response->getHeaders();
```

### Get a specific response header
```php
$response->getHeader('content-type');
```

### Get response body
```php
$response->getBody();
```

### Echo response body (relies on __toString magic method)
```php
echo $response;
```
