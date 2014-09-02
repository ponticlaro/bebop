Shortcodes
---

## Registering a shortcode

```php
Bebop::Shortcode($tag, $function);

// Example
Bebop::Shortcode('custom_shortcode', function($attributes, $content, $tag) {
    
    // Shortcode content here
    // There is no need to return or capture the content with an output buffer

})->setDefaultAttrs[
    'var1' => 1,
    'var2' => 2
]);
```

### Arguments
- `$tag`: Must be a string
- `$function`: Must be a callable. Its contents will be collected with an output buffer so you won't need to return anything.  It will receive 3 arguments: $attributes (Ponticlaro\Bebop\Common\Collection instance), $content and $tag.

### Returned value
- A `Ponticlaro\Bebop\Shortcode` instance.

## Getting the shortcode object anywhere
All shortcodes are tracked by Bebop, so you can get the object for a shortcode anywhere you need by using its `tag`. 

```php
$shortcode = Bebop::ObjectTracker()->get('shortcode', $tag);
```

## Available methods

- setDefaultAttrs(array $attrs)
- setDefaultAttr($key, $value)