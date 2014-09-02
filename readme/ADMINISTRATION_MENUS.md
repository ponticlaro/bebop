Administration Menus
---
This is the documentation for the [Administration Menus API](http://codex.wordpress.org/Administration_Menus) wrapper.

All admin page callable containers will be searched for control elements with the name attribute so that it can auto-save and fetch data. That data is passed as a `Ponticlaro\Bebop\Common\Collection` instance as the first and only argument of the callable. Each callable must contain at least these two features on its content:
- Valid markup
- Submit button

## Available UI settings
### Admin Page with a single page
```php
Bebop::AdminPage('Page Title', function($data) 
{    
    // Your admin page code here
});
```

### Admin Page with multiple tabs
```php
Bebop::AdminPage('Page Title')->addTab('Tab 1 Title', function($data) {    

    // Tab 1 code here
})
->addTab('Tab 2 Title',function($data) {    

    // Tab 2 code here
});
```

## Available methods
For all the available methods please check the `src/Ponticlaro/Bebop/AdminPage.php` file.

### Set parent page
```php
$page = Bebop::AdminPage('Page Title', function($data) {    
    
    // Your admin page code here
});

$page->setParent('settings');
```