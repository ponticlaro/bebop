MVC - View
---
*This documentation is incomplete*

## Default views base path
The default views base path will be the ´views´ directory inside the current theme.

## Changing the views base path
```php
\Ponticlaro\Bebop\Mvc\View::setViewsDir($path);
```

## Getting a view object for target template
**Notes:** You should not pass the file extension

```php
$view = Bebop::View('products/single');
```

## Setting a single variable
```php
$view->setVar($key, $value);
```

## Setting multiple variables
```php
$view->setVars($vars);
```

## Rendering a full view
**Notes:**
- If the second parameter is `false`, `$vars` will replace the existing variables instead of merging with them. 
```php
Bebop::View('products/single')->render($vars, false);
```

## Rendering a partial inside a view
**Notes:**
- You should not pass the file extension

```php
$this->partial('products/single/partials/details', $vars);
```