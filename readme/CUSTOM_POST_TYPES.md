Custom Post Types
---
*This documentation is incomplete*

## Registering a custom post type

```php
Bebop::PostType($name);
```

### Arguments
- `$name`: This can be either a string or an array:
  - **string**: Should be the singular form of the post type name, including white-spaces and capital letters. Plural form will be automatically assumed to be the singular form plus an "**s**" at the end.
  - **array**: Should only contain two elements and both must be strings. The first element is the singular form and the second element is the plural form.

### Returned value
- A `Ponticlaro\Bebop\PostType` instance. This can be passed when registering taxonomies and metaboxes.

## Getting the post type object anywhere
All custom post types are tracked by Bebop, so you can get the object for a post type anywhere you need by using its `ID`. The `ID` of a post type is always the slugified (with underscores) version of the singular name: e.g. **Product Range** will have **product_range** as the ID.

```php
$post_type = Bebop::ObjectTracker()->get('post_type', $id);
```

## Available methods

- setLabels(array $labels = array())
- setLabel($key, $value)
- getLabels()
- getLabel($key)
- setDescription($description)
- getDescription()
- makePublic($enabled = true)
- isPublic()
- archiveEnabled($enabled)
- hasArchive()
- setExcludeFromSearch($enabled = true)
- isExcludedFromSearch()
- setHierarchical($enabled = true)
- isHierarchical()
- setExportable($enabled = true)
- isExportable()
- setPubliclyQueryable($enabled = true)
- isPubliclyQueryable()
- showUi($enabled)
- showInNavMenus($enabled)
- showInMenu($value)
- showInAdminBar($show)
- setMenuPosition($position)
- getMenuPosition()
- setMenuIcon($icon)
- getMenuIcon()
- setCapabilityType($type)
- getCapabilityType()
- replaceCapabilities(array $capabilities = array())
- setCapabilities(array $capabilities = array())
- addCapability($capability)
- removeCapabilities(array $capabilities = array()
- removeCapability($capability)
- getCapabilities()
- setMapMetaCapabilities($enabled = true)
- replaceFeatures(array $features = array())
- addFeatures(array $features = array())
- addFeature($feature)
- removeFeatures(array $features = array())
- removeFeature($feature)
- getFeatures()
- setMetaboxesCallback($callback)
- getMetaboxesCallback()
- replaceTaxonomies(array $taxonomies = array())
- addTaxonomies(array $taxonomies = array())
- addTaxonomy($taxonomy)
- removeTaxonomies(array $taxonomies = array())
- removeTaxonomy($taxonomy)
- getTaxonomies()
- setPermalinkEpmask($epmask)
- getPermalinkEpmask()
- setRewrite(array $args = array())
- setRewriteSlug($slug)
- setRewriteWithFront($enabled)
- setRewriteFeeds($enabled)
- setRewritePages($enabled)
- setRewriteEpmask($epmask)
- getRewrite()
- setQueryVar($query_var)
- getQueryVar()
- applyRawArgs(array $args = array())