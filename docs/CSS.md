CSS Registration
---
This file explains in detail the available tools to manage registration and enqueuing of CSS files.

## Getting the CSS manager

```php
$css_manager = Bebop::CSS();
```

## Default CSS registration hooks
Bebop automatically sets 3 registration hooks:
- **front**: Used to register and enqueue scripts on the front-end
- **back** Used to register and enqueue scripts on the back-end
- **login**: Used to register and enqueue scripts on the login page

```php
$front_css = Bebop::CSS('front');
$back_css  = Bebop::CSS('back');
$login_css = Bebop::CSS('login');
```

## Adding a new registration hook
You can add new registration hooks and use them the same way as the default ones.

```php
Bebop::CSS()->addHook('admin/head', 'admin_head ');
```

## CSS registration, deregistration, enqueuing and dequeuing

### CSS Registration
```php
Bebop::CSS('front')->register($id, $file_path, $dependencies, $version, $media);
```

### CSS Deregistration
```php
Bebop::CSS('front')->deregister('jquery-ui', 'thickbox');
```

### CSS Enqueuing
```php
Bebop::CSS('front')->enqueue('main', 'fontcustom');
```

### CSS Dequeuing
```php
Bebop::CSS('front')->dequeue('jquery-ui');
```

## Modifying registration hook on specific environments
You can modify a registration hook on specific environments.  
The function will receive the registration hook object as the first and only argument.  
**Note:** This functionality depends on `Bebop::Env()` to determine which is the current environment.  

```php
Bebop::CSS('front')->onEnv('production', function($hook) {
    
    // This will make all scripts have this URL as the base,
    // instead of the theme URL
    $hook->setBaseUrl('http://cdn.my-project.com');
});
```

## Modifying single CSS file on specific environment
You can modify a single CSS file on specific environments.  
The function will receive the CSS file object as the first and only argument.  
**Note:** This functionality depends on `Bebop::Env()` to determine which is the current environment.  

```php
$app = Bebop::CSS('front')->getFile('main');

$app->onEnv('production', function($file) {
    
    $file->setFilePath('assets/js/app.min.js')
         ->setDependencies(['jquery']);
});
```