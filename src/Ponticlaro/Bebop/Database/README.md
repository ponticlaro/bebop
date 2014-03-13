Class Usage
-----------

## Option.php
Wrapper class for the [Options API](http://codex.wordpress.org/Options_API)

    // Instantiate option object
	//
	// First parameter is the key stored in database
	//
	// Second parameter expects an associative array of data or a boolean value to set the enabled/disable the autosave feature, whenever a set or remove method is called 
	//
	// Third parameter expects a boolean to enabled/disable the autosave feature adn should only be used if you passed an associative array of data in the second parameter
	
    $option = new \Ponticlaro\Bebop\Database\Option('option_hook', $data_array, true);

	// You can also use the method bellow to enable the autosave feature
    $option->setConfig('autosave', true);

	// Fetch existing data in database, if any
	// Used during instantiation, so that the object is initiated with the existing data in the database
    $option->fetch();

	// Set object data from associative array
    $option->set(array(
    	'key1' => 'value1', 
		'key2' => 'value2',
		'key3' => 'value3'
	));

	// Set single key
    $option->set('key', 'value1');

	// Get all data
    $option->get();

	// Get partial data
	$option->get(array('key1', 'key2'. 'key3'));
    $option->get('key');

	// Unset keys
    $option->remove(array('key1', 'key2'. 'key3'));
    $option->remove('key');

	// Store current object data in database
    $option->save();

	// Destroy option object and data in database
    $option->destroy();
