MVC - Model
---

## Usage
### Extending
```php
class Product extends \Ponticlaro\Bebop\Mvc\Model {
    
    // Post type name 
    protected $type = 'product';
}
```

### Setting modifications for all instances of the model
**Notes:** Each model instance is passed as a reference, so you do not need to return any value.  

```php
Product::onInit(function($product) {
    
    // Make any changes you need
});
```

### Setting loadable content
**Note:** Each model instance is passed as a reference, so you do not need to return any value.  

```php
Product::addLoadable('gallery', function($product) {
    
    // Get gallery meta and everything else gallery related
});
```

### Modifying models on target contexts
**Notes:** 
- Each model instance is passed as a reference, so you do not need to return any value.  
- This feature relies on `Bebop::Context()->is()` to determine what is the current context.  

```php
Product::onContext('single/product', function($product) {
    
    // Load loadables
    $product->load(['gallery', 'links']);

    // Do other stuff
});
```

### Querying

#### Finding single post by ID
**Note:** If you do not pass an ID and the method is called within the `single` context of the model `post_type` it will fetch the global `$post` variable and use it to return a model.  

```php
$product = Product::find(16);
```

#### Finding multiple posts by ID
```php
$products = Product::find([23, 14, 56]);
```

#### Finding multiple posts using ORM-like methods
**Note:** For all the available methods check the `$manufacturable` property on the `\Ponticlaro\Bebop\Db\Query\ArgFactory` class.  

```php
$products = Product::tax('manufacturer')->in(['ford', 'renault', 'toyota'])
                   ->meta('product_year')->between(2006, 2014)
                   ->ppp(16)
                   ->orderByMeta('price', 'ASC', true) // Last argument defines meta values as numeric
                   ->findAll();
```

#### Finding multiple posts using the same arguments as `WP_Query`
```php
$products = Product::findAll([
    'tax_query' => [
        [
            'taxonomy' => 'manufacturer',
            'compare'  => 'IN',
            'terms'    => ['ford', 'renault', 'toyota']
        ]
    ],
    'meta_query' => [
        [
            'key'     => 'product_year',
            'compare' => 'BETWEEN',
            'value'   => [2006, 2014]
        ]
    ],
    'posts_per_page' => 16,
    'orderby'        => 'meta_value_num',
    'meta_key'       => 'price',
    'order'          => 'ASC'
]);
```