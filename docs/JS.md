Javascript Registration
---
This file explains in detail the available tools to manage registration and enqueuing of Javascript files.

## Getting the scripts manager

```php
$js_manager = Bebop::JS();
```

## Default script registration hooks
Bebop automatically sets 3 registration hooks:
- **front**: Used to register and enqueue scripts on the front-end
- **back** Used to register and enqueue scripts on the back-end
- **login**: Used to register and enqueue scripts on the login page

```php
$front_js = Bebop::JS('front');
$back_js  = Bebop::JS('back');
$login_js = Bebop::JS('login');
```

## Adding a new registration hook
You can add new registration hooks and use them the same way as the default ones.

```php
Bebop::JS()->addHook('admin/head', 'admin_head ');
```

## Script registration, deregistration, enqueuing and dequeuing

### Script Registration
```php
Bebop::JS('front')->register($id, $file_path, $dependencies, $version, $in_footer);
```

### Script Deregistration
```php
Bebop::JS('front')->deregister('jquery');
```

### Script Enqueuing
```php
Bebop::JS('front')->enqueue('app');
```

### Script Dequeuing
```php
Bebop::JS('front')->dequeue('jquery-ui');
```

## Modifying registration hook on specific environments
You can modify a registration hook on specific environments.  
The function will receive the registration hook object as the first and only argument.  
**Note:** This functionality depends on `Bebop::Env()` to determine which is the current environment.  

```php
Bebop::JS('front')->onEnv('production', function($hook) {
    
    // This will make all scripts have this URL as the base,
    // instead of the theme URL
    $hook->setBaseUrl('http://cdn.my-project.com');
});
```

## Modifying single Javascript file on specific environment
You can modify a single Javascript file on specific environments.  
The function will receive the Javascript file object as the first and only argument.  
**Note:** This functionality depends on `Bebop::Env()` to determine which is the current environment.  

```php
$app = Bebop::JS('front')->getFile('app');

$app->onEnv('production', function($file) {
    
    $file->setFilePath('assets/js/app.min.js')
         ->setDependencies(['jquery']);
});
```