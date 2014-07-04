Custom Post Types
---
*This documentation is incomplete*

## Registering a custom post type

```php
$custom_post_type_obj = Bebop::PostType(mixed $name, array $args = array());
```

### Registration: arguments
- `$name`: This can be either a string or an array:
  - **string**: Should be the singular form of the post type name, including white-spaces and capital letters. Plural form will be automatically assumed to be the singular form plus an *s*  at the end.
  - **array**: Should only contain two elements and both must be strings. The first element is the singular form and the second element is the plural form.

- `$args`: is optional and exactly the same array you can pass as the second argument to `register_post_type` and that is documented [here](http://codex.wordpress.org/Function_Reference/register_post_type#Arguments).

### Registration: returned value
- `$custom_post_type_obj`: This is a `Ponticlaro\Bebop\PostType` instance that can be passed when registering taxonomies and metaboxes.

## Getting the post type object anywhere
All custom post types are tracked by Bebop, so you can get the object for a post type anywhere you need by using its `ID`. The `ID` of a post type is always the slugified (with underscores) version of the singular name: e.g. **Product Range** will have **product_range** as the ID.

```php
$custom_post_type_obj = Bebop::ObjectTracker()->get('post_type', $id);
```
