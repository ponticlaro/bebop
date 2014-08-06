Administration Menus
---
This is the documentation for the [Administration Menus API](http://codex.wordpress.org/Administration_Menus) wrapper

## Simple instantiation based on defaults 
```php
Bebop::AdminPage('Admin Page title', function() 
{    
    // Your admin page code here
});
```

## Simple instantiation, but as a child of the 'Settings' page
```php
Bebop::AdminPage('Admin Page title', function() 
{    
    // Your admin page code here
}, 
array(
    'parent' => 'settings'
));
```

## Full configuration
```php
Bebop::AdminPage('Admin Page title', function() {
    
    // Your admin page code here
}, 
array(
    'capability' => 'read',
    'menu_slug'  => 'custom-admin-page',
    'icon_url'   => '',
    'position'   => '',
    'parent'     => ''
));
```