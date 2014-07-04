Context
---
Bebop can find the current context using a set of context containers. Each context container consist of an `ID` and a function to execute and all of the containers are ran in the defined order, so you can either add one to the top or the bottom of the list. The first context container to return a context key will stop the loop and no other context containers will be ran.

## Default context container coverage
When Bebop is initialized, a default set of context keys are already being covered:  

- home/posts - When the home is the blog index
- home/page - When the home is a page
- search
- archive/:post_type_name - this includes `post` and `page`
- archive/author
- archive/date/year
- archive/date/month
- archive/date/day
- single/:post_type_name
- tax/:taxonomy - this includes `category` and `tag`
- error/404

## Getting the Context Object

```php
$context = \Ponticlaro\Bebop::Context();
```

## Getting the current context key

```php
$context_key = \Ponticlaro\Bebop::Context()->getCurrent();
```

## Checking if current context key is a partial match of the provided string

```php
\Ponticlaro\Bebop::Context()->is('single');
```

## Checking if current context key matches a regular expression

```php
\Ponticlaro\Bebop::Context()->is('/single\/(product|post|page)/', true);
```

## Checking if current context key is an exact match of the provided string

```php
\Ponticlaro\Bebop::Context()->equals('single/product');
```

## Adding new context container
You can add a custom context container to the top or bottom of the list.  
Each container consist of an `ID` and a function to execute. The function will receive `$wp_query` as the first and only argument and it **must return the context key if any was found OR `null` otherwise**.

### Adding new context container to the top of the list

```php
\Ponticlaro\Bebop::Context()->add('custom', function($wp_query) {
    
    // Your logic to find the current context key

    return $context_key; 
}));
```

### Adding new context container to the bottom of the list
```php
\Ponticlaro\Bebop::Context()->prepend('custom', function($wp_query) {
    
    // Your logic to find the current context key

    return $context_key; 
}));
```