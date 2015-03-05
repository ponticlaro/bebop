Bebop API - Post Type Resources
---

## Default Resources
Bebop will create by default the following resources for each post type:
- GET /_bebop/api/**{ post_type_slug }**/
- GET /_bebop/api/**{ post_type_slug }**/:post_id/

**Note:** the `post_type_slug` is generated from the post type lowercased plural name by also replacing spaces with an underscore, using `Ponticlaro\Bebop\Utils::slugify()` utility.

## Querying
### Available query parameters
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

### Pagination
- Use the `page` parameter as it is mapped to the internal `paged` argument for `WP_Query`.
- Check the `meta['has_more']` parameter to know if you have more pages.

### Tax queries
If you need to use the [`tax_query`](http://codex.wordpress.org/Class_Reference/WP_Query#Taxonomy_Parameters) argument for `WP_Query` you need to use one of the following structures to build a query parameter:
- tax:{{taxonomy_slug}}:{{operator}}={{field}}:{{term(s)}}
- tax:{{tax_query_json}}={{term(s)}}

**Notes:**  
- The `:{{operator}}` section is optional. `IN` is the default operator value.
- The `{{field}}:` section is optional. `slug` is the default field.
- You can specify the relation of tax queries with `tax_relation` query parameter.   
- You can specify comma separated values.

#### Operators Map

```php
array( 
    'in'    => 'IN', 
    'notin' => 'NOT IN',
    'and'   => 'AND'
);
```

**Simple query with `IN` as compare operator:**  

    meta:manufacturer=ford

**Complex query with multiple values:**  

    tax:{"taxonomy":"year","field":"slug","operator":"IN"}=2012,2013,2014

After the `tax:` segment, you need to add a JSON object like:

```json
{
    "taxonomy": "year",
    "field": "slug",
    "operator": "IN"
}
```

### Meta Queries
#### Structure
If you need to use the [`meta_query`](http://codex.wordpress.org/Class_Reference/WP_Query#Custom_Field_Parameters) argument for `WP_Query` you need to use one of the following structures to build a query parameter:
- meta:{{meta_key}}:{{compare}}={{value_type}}:{{meta_value(s)}}
- meta:{{meta_query_json}}={{meta_value(s)}}

**Notes:**  
- The `:{{compare}}` section is optional. `=` is the default compare value.
- The `{{value_type}}:` section is optional. `CHAR` is the default value type.
- You can specify the relation of meta queries with `meta_relation` query parameter.  
- You can specify comma separated values.

#### Compare Map

```php
array(
    'eq'         => '=', 
    'noteq'      => '!=', 
    'is'         => '=', 
    'isnot'      => '!=', 
    'gt'         => '>', 
    'gte'        => '>=', 
    'lt'         => '<', 
    'lte'        => '<=', 
    'like'       => 'LIKE', 
    'notlike'    => 'NOT LIKE', 
    'in'         => 'IN', 
    'notin'      => 'NOT IN', 
    'between'    => 'BETWEEN', 
    'notbetween' => 'NOT BETWEEN'
);
```

**Examples:**  
Simple query with `=` as compare operator:  

    meta:download_enabled=1

Complex query with multiple values:  

    meta:{"key":"latitude","compare":"between","type":"numeric"}=35,45

After the `meta:` segment, you need to add a JSON object like:  

```json
{
    "key": "latitude",
    "compare": "between",
    "type": "numeric"
}
```

### Response Structure
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