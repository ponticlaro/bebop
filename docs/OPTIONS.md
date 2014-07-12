Options
---

This is the documentation for the [Options API](http://codex.wordpress.org/Options_API) wrapper.

## Instantiate option object
```php
use Ponticlaro\Bebop;

$option = Bebop::Option($option_hook, $data_array, $auto_save_enabled);
```

## Enabling auto-save on each modification
```php
$option->setConfig('autosave', true);
```

## Fetch existing data from database
```php
$option->fetch();
```

## Returns all data
```php
$option->get();
```

## Setting single key
```php
$option->set('key', 'value1');
```

## Setting object data from associative array
```php
$option->set(array(
    'key1' => 'value1', 
    'key2' => 'value2',
    'key3' => 'value3'
));
```

## Getting partial data
```php
$data = $option->get(array('key1', 'key2'. 'key3'));
$data = $option->get('key');
```

## Unsetting keys
```php
$option->remove(array('key1', 'key2'. 'key3'));
$option->remove('key');
```

## Store current object data in database
```php
$option->save();
```

## Destroy option object and data in database
```php
$option->destroy();
```