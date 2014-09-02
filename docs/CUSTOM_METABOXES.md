Custom Metaboxes
---
*This documentation is incomplete*

## Registering a custom metabox

```php
Bebop::Metabox($title, $post_types, function($data) { ?>
  
   <input type="text" name="title" value="<?php echo $data->get('title', true); ?>">

<?php });
```

### Arguments
- `$title`: Title of the metabox. Will also be used at its `id`
- `$post_types`: can be either:
  - **string**: matching the [$post_type](http://codex.wordpress.org/Function_Reference/register_post_type#Parameters) parameter on the `register_post_type` function, which is equal to the `Ponticlaro\Bebop\PostType` instance `ID`. 
  - **`Ponticlaro\Bebop\PostType` instance**
  - **array**: each element on this list can be either:
    - a `Ponticlaro\Bebop\PostType` instance
    - a string matching the [$post_type](http://codex.wordpress.org/Function_Reference/register_post_type#Parameters) parameter on the `register_post_type` function, which is equal to the `Ponticlaro\Bebop\PostType` instance `ID`. 

- `$callable`: this will receive a `\Ponticlaro\Bebop\Helpers\MetaboxData` instance with all the metabox data.

### Returned value
A `Ponticlaro\Bebop\Metabox` instance.

## Getting the metabox object anywhere
All custom metaboxes are tracked by Bebop, so you can get the object for a metabox anywhere you need by using its `ID`. The `ID` of a metabox is always the slugified (with underscores) version of the singular name: e.g. **Product Details** will have **product_details** as the `ID`.

```php
$metabox = Bebop::ObjectTracker()->get('metabox', $id);
```