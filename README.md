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
        "ponticlaro/bebop": "~2"
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
- [RESTful API](docs/API.md)
- [MVC Architecture](docs/MVC.md)
- Admin UI Widgets

## Utilities
- [Environment Manager](docs/ENVIRONMENT_MANAGER.md)
- [Context Manager](docs/CONTEXT_MANAGER.md)
- [Paths Manager](docs/PATHS_MANAGER.md)
- [URLs Manager](docs/URLS_MANAGER.md)

## WordPress API Wrappers
- [CSS Registration](docs/CSS.md)
- [JS Registration](docs/JS.md)
- [Custom Post Types](docs/CUSTOM_POST_TYPES.md)
- [Custom Taxonomies](docs/CUSTOM_TAXONOMIES.md)
- [Custom Metaboxes](docs/CUSTOM_METABOXES.md)
- [Administration Menus](docs/ADMINISTRATION_MENUS.md)
- [Shortcodes](docs/SHORTCODES.md)
- [Options](docs/OPTIONS.md)
- [HTTP Client](docs/HTTP_CLIENT.md)
