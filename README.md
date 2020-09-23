Ponticlaro&trade; Bebop
==================


## Modules
### [Bebop Core](https://github.com/ponticlaro/bebop-core)

### [Bebop Common](https://github.com/ponticlaro/bebop-common)

### [Bebop CMS](https://github.com/ponticlaro/bebop-cms)

### [Bebop UI](https://github.com/ponticlaro/bebop-ui)

### [Bebop MVC](https://github.com/ponticlaro/bebop-mvc)

### [Bebop DB](https://github.com/ponticlaro/bebop-db)

### [Bebop Scripts Loader](https://github.com/ponticlaro/bebop-scripts-loader)

### [Bebop HTTP API](https://github.com/ponticlaro/bebop-http-api)

### [Bebop HTTP Client](https://github.com/ponticlaro/bebop-http-client)


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
        "ponticlaro/bebop": "^3"
    }
}
```

After installing, you need to require Composer's autoloader:

```php
require 'vendor/autoload.php';
```

## Usage
### Boot Bebop
You should call `Bebop::boot()` before using any of its features. This is needed to generate defaults and initialize several useful objects.  
```php
use Ponticlaro\Bebop;
    
Bebop::boot();
```

### Custom Features
- [RESTful API](docs/API.md)
- [MVC Architecture](docs/MVC.md)
- Admin UI Widgets

### Utilities
- [Environment Manager](docs/ENVIRONMENT_MANAGER.md)
- [Context Manager](docs/CONTEXT_MANAGER.md)
- [Paths Manager](docs/PATHS_MANAGER.md)
- [URLs Manager](docs/URLS_MANAGER.md)

### WordPress API Wrappers
- [CSS Registration](docs/CSS.md)
- [JS Registration](docs/JS.md)
- [Custom Post Types](docs/CUSTOM_POST_TYPES.md)
- [Custom Taxonomies](docs/CUSTOM_TAXONOMIES.md)
- [Custom Metaboxes](docs/CUSTOM_METABOXES.md)
- [Administration Menus](docs/ADMINISTRATION_MENUS.md)
- [Shortcodes](docs/SHORTCODES.md)
- [Options](docs/OPTIONS.md)
- [HTTP Client](docs/HTTP_CLIENT.md)
