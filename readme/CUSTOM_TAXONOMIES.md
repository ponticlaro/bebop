Custom Taxonomies
---
*This documentation is incomplete*

## Registering a custom taxonomy

```php
Bebop::Taxonomy($name, $post_types);
```

### Arguments
- `$name`: This can be either a string or an array:
  - **string**: Should be the singular form of the post type name, including white-spaces and capital letters. Plural form will be automatically assumed to be the singular form plus an "**s**" at the end.
  - **array**: Should only contain two elements and both must be strings. The first element is the singular form and the second element is the plural form.

- `$post_types`: can be either:
  - **string**: matching the [$post_type](http://codex.wordpress.org/Function_Reference/register_post_type#Parameters) parameter on the `register_post_type` function, which is equal to the `Ponticlaro\Bebop\PostType` instance `ID`. 
  - **`Ponticlaro\Bebop\PostType` instance**
  - **array**: each element on this list can be either:
    - a `Ponticlaro\Bebop\PostType` instance
    - a string matching the [$post_type](http://codex.wordpress.org/Function_Reference/register_post_type#Parameters) parameter on the `register_post_type` function, which is equal to the `Ponticlaro\Bebop\PostType` instance `ID`. 

### Returned value
A `Ponticlaro\Bebop\Taxonomy` instance.

## Getting the taxonomy object anywhere
All custom taxonomies are tracked by Bebop, so you can get the object for a taxonomy anywhere you need by using its `ID`. The `ID` of a taxonomy is always the slugified (with underscores) version of the singular name: e.g. **Product Year** will have **product_year** as the `ID`.

```php
$taxonomy = Bebop::ObjectTracker()->get('taxonomy', $id);
```

## Available methods

- setLabels(array $labels = array())
- setLabel($key, $value)
- getLabels()
- getLabel($key)
- replaceCapabilities(array $capabilities = array())
- setCapabilities(array $capabilities = array())
- addCapability($capability)
- removeCapabilities(array $capabilities = array())
- removeCapability($capability)
- getCapabilities()
- setPostTypes(array $post_types = array())
- addPostTypes(array $post_types = array())
- addPostType($post_type)
- removePostTypes(array $post_types = array())
- removePostType($post_type)
- getPostTypes()
- setRewrite(array $args = array())
- setRewriteSlug($slug)
- setRewriteWithFront($enabled)
- setRewriteFeeds($enabled)
- setRewritePages($enabled)
- setRewriteEpmask($epmask)
- getRewrite()
- makePublic($enabled = true)
- isPublic()
- setHierarchical($enabled = true)
- isHierarchical()
- setQueryVar($query_var)
- getQueryVar()
- showUi($enabled)
- showInNavMenus($enabled)
- showTagcloud($enabled)
- showAdminColumn($enabled)
- setMetaboxCallback($callback)
- getMetaboxCallback()
- setUpdateCountCallback($callback)
- getUpdateCountCallback()
- setSort($enabled)
- getSort()