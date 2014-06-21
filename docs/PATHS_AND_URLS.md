Paths and Urls
---

## Paths
### Default Paths
- **bebop**
- **root**
- **admin**
- **plugins**
- **content**
- **uploads**
- **themes**
- **theme**: Current theme  

### Getting Paths
#### Getting root path
```php
$root_path = Bebop::getPath();
$root_path = Bebop::getPath('root');
```

#### Getting single path
```php
$theme_path = Bebop::getPath('theme');
```

#### Getting single path, suffixed with whatever you need
```php
$template = Bebop::getPath('theme', 'templates/article.mustache');
```

### Setting Paths
#### Setting a single path
```php
Bebop::setPath('favicon', 'favicon.ico'));
```

#### Setting a single path, based on another existing path
```php
Bebop::setPath('favicon', Bebop::getPath('theme', 'favicon.ico'));
```

#### Setting multiple paths
```php
Bebop::setPath(array(
    'theme/css'    => Bebop::getPath('theme', 'assets/css'),
    'theme/js'     => Bebop::getPath('theme', 'assets/js'),
    'theme/fonts'  => Bebop::getPath('theme', 'assets/fonts'),
    'theme/images' => Bebop::getPath('theme', 'assets/i'),
    'favicon'      => Bebop::getPath('theme', 'favicon.ico')
));
```

### Paths shortcode
Bebop generates a shortcode to use these paths on the built-in editor.  

```
[bebop path="theme"]
```

## URLs
### Default URLs
- **bebop**
- **home**
- **admin**
- **plugins**
- **content**
- **uploads**
- **themes**
- **theme**: Current theme  

### Getting URLs
#### Getting home URL
```php
$home_url = Bebop::getPath();
$home_url = Bebop::getPath('home');
```

#### Getting single URL
```php
$theme_path = Bebop::getPath('theme');
```

#### Getting single URL, suffixed with whatever you need
```php
$template = Bebop::getPath('theme', 'assets/js/app.min.js);
```

### Setting URLs
#### Setting a single URL
```php
Bebop::setUrl('favicon', 'favicon.ico'));
```

#### Setting a single URL, based on another existing URL
```php
Bebop::setUrl('favicon', Bebop::getUrl('theme', 'favicon.ico'));
```

#### Setting multiple URLs
```php
Bebop::setUrl(array(
    'theme/css'    => Bebop::getUrl('theme', 'assets/css'),
    'theme/js'     => Bebop::getUrl('theme', 'assets/js'),
    'theme/fonts'  => Bebop::getUrl('theme', 'assets/fonts'),
    'theme/images' => Bebop::getUrl('theme', 'assets/i'),
    'favicon'      => Bebop::getUrl('theme', 'favicon.ico')
));
```

### URLs shortcode
Bebop generates a shortcode to use these URLs on the built-in editor.    
```
[bebop url="theme"]
```




