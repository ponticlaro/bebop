Custom Taxonomies
---
*This documentation is incomplete*

## Registering a custom taxonomy

```php
$custom_tax_obj = Bebop::Taxonomy(mixed $name, mixed $post_types, array $args = array());
```

### Registration: arguments
- `$name`: This can be either a string or an array:
  - **string**: Should be the singular form of the post type name, including white-spaces and capital letters. Plural form will be automatically assumed to be the singular form plus an "**s**" at the end.
  - **array**: Should only contain two elements and both must be strings. The first element is the singular form and the second element is the plural form.

- `$post_types`: can be either:
  - **string**: matching the [$post_type](http://codex.wordpress.org/Function_Reference/register_post_type#Parameters) parameter on the `register_post_type` function, which is equal to the `Ponticlaro\Bebop\PostType` instance `ID`. 
  - **`Ponticlaro\Bebop\PostType` instance**
  - **array**: each element on this list can be either:
    - a `Ponticlaro\Bebop\PostType` instance
    - a string matching the [$post_type](http://codex.wordpress.org/Function_Reference/register_post_type#Parameters) parameter on the `register_post_type` function, which is equal to the `Ponticlaro\Bebop\PostType` instance `ID`. 

- `$args`: is optional and exactly the same array you can pass as the third argument to `register_taxonomy` and that is documented [here](http://codex.wordpress.org/Function_Reference/register_taxonomy#Arguments).

### Registration: returned value
- `$custom_tax_obj`: This is a `Ponticlaro\Bebop\Taxonomy` instance that can be passed when registering taxonomies and metaboxes.

## Getting the taxonomy object anywhere
All custom post types are tracked by Bebop, so you can get the object for a taxonomy anywhere you need by using its `ID`. The `ID` of a taxonomy is always the slugified (with underscores) version of the singular name: e.g. **Product Year** will have **product_year** as the `ID`.

```php
$custom_tax_obj = Bebop::ObjectTracker()->get('taxonomy', $id);
```