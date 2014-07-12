Bebop
==================
[![Build Status](https://travis-ci.org/ponticlaro/bebop.svg?branch=feature/ci)](https://travis-ci.org/ponticlaro/bebop)
[![Latest Stable Version](https://poser.pugx.org/ponticlaro/bebop/v/stable.png)](https://packagist.org/packages/ponticlaro/bebop)
[![License](https://poser.pugx.org/ponticlaro/bebop/license.png)](https://packagist.org/packages/ponticlaro/bebop)
[![Total Downloads](https://poser.pugx.org/ponticlaro/bebop/downloads.png)](https://packagist.org/packages/ponticlaro/bebop)

## Boot Bebop
You should call `Bebop::boot()` before using any of its features. This is needed to generate defaults and initialize several useful objects.  
```php
use Ponticlaro\Bebop;
    
Bebop::boot();
```

## Custom Features
- [RESTful API](docs/API.md)
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
- [Options](docs/OPTIONS.md)
- [HTTP Client](docs/HTTP_CLIENT.md)
