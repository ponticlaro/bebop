Urls Manager
---
## Default URLs
- **bebop**
- **home**
- **admin**
- **plugins**
- **content**
- **uploads**
- **themes**
- **theme**: Current theme  

## Getting URLs
### Getting home URL
```php
$home_url = Bebop::getPath();
$home_url = Bebop::getPath('home');
```

### Getting single URL
```php
$theme_path = Bebop::getPath('theme');
```

### Getting single URL, suffixed with whatever you need
```php
$template = Bebop::getPath('theme', 'assets/js/app.min.js);
```

## Setting URLs
### Setting a single URL
```php
Bebop::setUrl('favicon', 'favicon.ico'));
```

### Setting a single URL, based on another existing URL
```php
Bebop::setUrl('favicon', Bebop::getUrl('theme', 'favicon.ico'));
```

### Setting multiple URLs
```php
Bebop::setUrl(array(
    'theme/css'    => Bebop::getUrl('theme', 'assets/css'),
    'theme/js'     => Bebop::getUrl('theme', 'assets/js'),
    'theme/fonts'  => Bebop::getUrl('theme', 'assets/fonts'),
    'theme/images' => Bebop::getUrl('theme', 'assets/i'),
    'favicon'      => Bebop::getUrl('theme', 'favicon.ico')
));
```

## URLs shortcode
Bebop generates a shortcode to use these URLs on the built-in editor.    
```
[bebop url="theme"]
```




