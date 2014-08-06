<?php

namespace Ponticlaro\Bebop\Db;

use Ponticlaro\Bebop;

class Option
{	
    /**
     * Configuration
     * 
     * @var Ponticlaro\Bebop\Common\Collection
     */
	private $config;

    /**
     * Data
     * 
     * @var Ponticlaro\Bebop\Common\Collection
     */
	private $data;

	/**
	 * Creates new option instance 
	 * 
	 * @param string  $name      Option name
	 * @param array   $data      Option data
	 * @param boolean $auto_save True to enable autosave, false to keep it disabled
	 */
	public function __construct($name, array $data = array(), $auto_save = false)
	{	
		// Instantiated configuration object
		$this->config = Bebop::Collection(array(
			"autosave" => $auto_save
		));

		// Instantiated data object
		$this->data = Bebop::Collection();

		// Set input name
		if ($name)
			$this->setName($name);

		// Set input data
		if ($data) {

			$this->data->set($data);
			
			$this->__autosave();
		}

		// Fetch existing data from database, if any
		else {

			$this->fetch();
		}
	}

	/**
	 * Sets option name
	 * 
	 * @param string $name
	 */
	public function setName($name)
	{
		if (!is_string($name))
			throw new \Exception("Option name must be a string");
			
		$this->setConfig('name', $name);

		return $this;
	}

	/**
	 * Checks if current WordPress installation is a network
	 * 
	 * @return boolean True if it is a network, false otherwise
	 */
	protected function isNetwork()
	{
		return Bebop::util('isNetwork');
	}

	/**
	 * Fetches all data from databas using option name
	 * 
	 * @return [type] [description]
	 */
	public function fetch()
	{
		$name = $this->getConfig('name');
		$data = $this->isNetwork() ? get_site_option($name) : get_option($name);

		$this->data->set($data);

		return $this;
	}

	/**
	 * Sets a single configuration key/value pair
	 * 
	 * @param string $key
	 * @param mixed  $value
	 */
	public function setConfig($key, $value = null)
	{
		if (is_string($key))
			$this->config->set($key, $value);

		return $this;
	}

	/**
	 * Removes a single configuration value
	 * 
	 * @param  string $key
	 * @return class       This class instance
	 */
	public function removeConfig($key)
	{
		if (is_string($key))
			$this->config->remove($key);

		return $this;
	}

	/**
	 * Returns single configuration value
	 * 
	 * @param  string $key
	 * @return mixed
	 */
	public function getConfig($key)
	{
		return $this->config->get($key);
	}

	/**
	 * Sets a single key/value pair
	 * 
	 * @param string $key   
	 * @param mixed  $value 
	 */
	public function set($keys, $value = true)
	{
		if (is_array($keys)) {
			
			foreach ($keys as $key => $value) {
				
				$this->data->set($key, $value);
			}
		}

		if (is_string($keys)) {

			$this->data->set($keys, $value);
		}

		$this->__autosave();

		return $this;
	}

	/**
	 * Removes data stored on the target key
	 * 
	 * @param  string $key
	 * @return class       This class instance
	 */
	public function remove($key)
	{
		$this->data->remove($key);

		$this->__autosave();

		return $this;
	}

	/**
	 * Returns data stored on target key
	 * 
	 * @param  string $key
	 * @return mixed      
	 */
	public function get($key)
	{
		return $this->data->get($key);
	}

	/**
	 * Returns all data
	 * 
	 * @return array
	 */
	public function getAll()
	{
		return $this->data->getAll();
	}

	/**
	 * Saves current data
	 * 
	 * @return void
	 */
	public function save()
	{
		$name = $this->getConfig('name');
		$data = $this->getAll();

		$this->isNetwork() ? update_site_option($name, $data) : update_option($name, $data);

		return $this;
	}
	
	/**
	 * Destroys this option
	 * 
	 * @return void
	 */
	public function destroy()
	{
		$name = $this->getConfig('name');

		$this->isNetwork() ? delete_site_option($name) : delete_option($name);	
	}

	/**
	 * Autosaves current data
	 * 
	 * @return void
	 */
	protected function __autosave()
	{
		if ($this->getConfig('autosave')) $this->save();

		return $this;
	}
}