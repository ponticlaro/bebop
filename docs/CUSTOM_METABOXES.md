Custom Metaboxes
---
*This documentation is incomplete*

## Registering a custom metabox

```php
$custom_metabox_obj = Bebop::Metabox(mixed $title, array $args = array());
```

### Registration: arguments
- `$title`: Title of the metabox. Will also be used at its `id`
- `$post_types`: can be either:
  - **string**: matching the [$post_type](http://codex.wordpress.org/Function_Reference/register_post_type#Parameters) parameter on the `register_post_type` function, which is equal to the `Ponticlaro\Bebop\PostType` instance `ID`. 
  - **`Ponticlaro\Bebop\PostType` instance**
  - **array**: each element on this list can be either:
    - a `Ponticlaro\Bebop\PostType` instance
    - a string matching the [$post_type](http://codex.wordpress.org/Function_Reference/register_post_type#Parameters) parameter on the `register_post_type` function, which is equal to the `Ponticlaro\Bebop\PostType` instance `ID`. 

- `$args`: is optional and can contain any of the arguments you can pass to `add_meta_box` and that are documented [here](http://codex.wordpress.org/Function_Reference/add_meta_box#Parameters).

#### Registration: $args example:
```php
$args = array(
    'id'            => '',
    'title'         => '',
    'callback'      => '',
    'post_type'     => '',
    'context'       => '',
    'priority'      => '',
    'callback_args' => '',
);
```

### Registration: returned value
- `$custom_metabox_obj`: This is a `Ponticlaro\Bebop\Metabox` instance that can be passed when registering taxonomies and metaboxes.

## Getting the metabox object anywhere
All custom post types are tracked by Bebop, so you can get the object for a metabox anywhere you need by using its `ID`. The `ID` of a metabox is always the slugified (with underscores) version of the singular name: e.g. **Product Details** will have **product_details** as the `ID`.

```php
$custom_metabox_obj = Bebop::ObjectTracker()->get('metabox', $id);
```