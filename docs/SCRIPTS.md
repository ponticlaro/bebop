Scripts Registration
---
This file explains in detail the available tools to manage registration and enqueuing of scripts.

## Getting the scripts manager

```php
$scripts_manager = Bebop::Scripts();
```

## Default script registration hooks
Bebop automatically sets 3 registration hooks:
- **front**: Used to register and enqueue scripts on the front-end
- **back** Used to register and enqueue scripts on the back-end
- **login**: Used to register and enqueue scripts on the login page

```php
$front_scripts = Bebop::Scripts('front');
$back_scripts  = Bebop::Scripts('back');
$login_scripts = Bebop::Scripts('login');
```

## Adding a new registration hook
You can add new registration hooks and use them the same way as the default ones.

```php
Bebop::Scripts()->addHook('admin/head', 'admin_head ');
```

## Script registration, deregistration, enqueuing and dequeuing

### Script Registration
```php
Bebop::Scripts('front')->register($id, $file_path, $dependencies, $version, $in_footer);
```

### Script Deregistration
```php
Bebop::Scripts('front')->deregister('jquery');
```

### Script Enqueuing
```php
Bebop::Scripts('front')->enqueue('app');
```

### Script Dequeuing
```php
Bebop::Scripts('front')->dequeue('jquery-ui');
```

## Modifying registration hook on specific environments
You can modify a registration hook on specific environments.  
The function will receive the registration hook object as the first and only argument.  
**Note:** This functionality depends on `Bebop::Env()` to determine which is the current environment.  

```php
Bebop::Scripts('front')->onEnv('production', function($hook) {
    
    // This will make all scripts have this URL as the base,
    // instead of the theme URL
    $hook->setBaseUrl('http://cdn.my-project.com');
});
```

## Modifying single script on specific environment
You can modify a single script on specific environments.  
The function will receive the script object as the first and only argument.  
**Note:** This functionality depends on `Bebop::Env()` to determine which is the current environment.  

```php
$app = Bebop::Scripts('front')->getScript('app');

$app->onEnv('production', function($script) {
    
    $script->setFilePath('assets/js/app.min.js')
           ->setDependencies(['jquery']);
});
```