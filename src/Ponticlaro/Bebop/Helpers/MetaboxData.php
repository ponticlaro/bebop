<?php

namespace Ponticlaro\Bebop\Helpers;

use \Ponticlaro\Bebop;
use \Ponticlaro\Bebop\Common\CollectionAbstract;

class MetaboxData {

	private $data;

	public function __construct(array $data = array()) 
	{
		$this->data = Bebop::Collection($data);
	}

	public function setDataContainer(CollectionAbstract $container)
	{
		// Get currently stored data
		$current_data = $this->data->get();

		// Set container and pass current data
		$this->data = new $container($current_data);
	}

	public function get($key, $is_single = false) 
	{
		$data = $this->data->get($key);

		// If single, try to unserialize it
		if ($is_single) {

			// Try to unserialize if it is a string
			if (is_string($data)) unserialize($data[0]);

			// Get first item
			$data = $data[0];
		}

		// Handle arrays that only contain empty values
		elseif (is_array($data) && !array_filter($data)) {

			$data = array();
		}

		return $data;
	}

	public function __call($name, $args)
	{
		if (!method_exists($this->data, $name))
			throw new \Exception("MetaboxData->$name method do not exist", 1);

		return call_user_func_array(array($this->data, $name), $args);
	}
}