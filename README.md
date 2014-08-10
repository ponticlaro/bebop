Bebop
==================
[![Code Climate](https://codeclimate.com/github/ponticlaro/bebop/badges/gpa.svg)](https://codeclimate.com/github/ponticlaro/bebop)
[![Latest Stable Version](https://poser.pugx.org/ponticlaro/bebop/v/stable.png)](https://packagist.org/packages/ponticlaro/bebop)
[![License](https://poser.pugx.org/ponticlaro/bebop/license.png)](https://packagist.org/packages/ponticlaro/bebop)
[![Total Downloads](https://poser.pugx.org/ponticlaro/bebop/downloads.png)](https://packagist.org/packages/ponticlaro/bebop)

## Installing via Composer

The recommended way to install Bebop is through
[Composer](http://getcomposer.org).

```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php
```

Next, update your project's composer.json file to include Bebop:

```javascript
{
    "require": {
        "ponticlaro/bebop": "~1"
    }
}
```

After installing, you need to require Composer's autoloader:

```php
require 'vendor/autoload.php';
```

## Boot Bebop
You should call `Bebop::boot()` before using any of its features. This is needed to generate defaults and initialize several useful objects.  
```php
use Ponticlaro\Bebop;
    
Bebop::boot();
```

## Custom Features
- [RESTful API](readme/API.md)
- [MVC Architecture](readme/MVC.md)
- Admin UI Widgets

## Utilities
- [Environment Manager](readme/ENVIRONMENT_MANAGER.md)
- [Context Manager](readme/CONTEXT_MANAGER.md)
- [Paths Manager](readme/PATHS_MANAGER.md)
- [URLs Manager](readme/URLS_MANAGER.md)

## WordPress API Wrappers
- [CSS Registration](readme/CSS.md)
- [JS Registration](readme/JS.md)
- [Custom Post Types](readme/CUSTOM_POST_TYPES.md)
- [Custom Taxonomies](readme/CUSTOM_TAXONOMIES.md)
- [Custom Metaboxes](readme/CUSTOM_METABOXES.md)
- [Administration Menus](readme/ADMINISTRATION_MENUS.md)
- [Options](readme/OPTIONS.md)
- [HTTP Client](readme/HTTP_CLIENT.md)
