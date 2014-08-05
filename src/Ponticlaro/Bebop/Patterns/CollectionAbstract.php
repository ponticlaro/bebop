<?php

namespace Ponticlaro\Bebop\Patterns;

abstract class CollectionAbstract implements CollectionInterface {

	/**
	 * Array that contains all the data in this collection
	 * 
	 * @var   array
	 */
	private $data = array();
	
	/**
	 * Initialize Collection with optionally 
	 * passed array with initial data
	 *
	 * @param   array   $data    Optional initial data to be added
	 */
	public function __construct(array $data = array())
	{
		$this->set($data);
	}

	/**
	 * Sets a value for a the given key
	 *
	 * By default it uses true as the value,
	 * making it easier to mimic indexed arrays by
	 * later calling getKeys() to get
	 *
	 * @param   string   $key     Key where the value should be stored
	 * @param   mixed    $value   Value that should be stored
	 */
	public function set($key, $value = true)
	{
		if (!$key) return $this;

		if (is_string($key)) $this->__set($key, $value);

		if (is_array($key) && !empty($key)) {

			$data_list = $key;

			foreach ($data_list as $key => $value) {

				$this->__set($key, $value);

			}

		}

		return $this;
	}

	/**
	 * Removed the first value from the indexed array
	 * with a given $key or directly from the $data indexed array
	 * 
	 * @param    string   $key   Target array key
	 * @return   mixed           The removed value or null
	 */
	public function shift($key = null)
	{
		if($key) return isset($this->data[$key]) && is_array($this->data[$key]) ? array_shift($this->data[$key]) : null;

		return isset($this->data[0]) ? array_shift($this->data) : null;
	}

	/**
	 * Adds one or more items to the beginning 
	 * of the target container array
	 *  
	 * @param    mixed    $value   Valuss to be inserted
	 * @param    string   $key     Namespace where the target array lives 
	 * @return   class             This collection
	 */
	public function unshift($values, $key = null)
	{
		if (is_array($values)) {

			foreach ($values as $key => $value) {

				$this->__unshiftItem($value, $key);
			}
		}

		else {

			$this->__unshiftItem($values, $key);
		}

		return $this;
	}

	/**
	 * Adds an item to to beginning of the target array
	 *
	 * @param    mixed    $value   The valued to be inserted
	 * @param    string   $key     Optional key to namespace the pushed value
	 * @return   void        
	 */
	private function __unshiftItem($value, $key = null)
	{
		if ($key) {

			if (!array_key_exists($key, $this->data)) {

	            $this->data[$key] = $value;

	        } elseif (is_array($this->data[$key])) {

	            array_unshift($this->data[$key], $value);

	        } else {

	            $this->data[$key] = array($value);
	        }

		} else {

			 array_unshift($this->data, $value);
		}
	}

	/**
	 * Adds a value to a given key or 
	 * to the $data indexed array
	 *
	 * @param    mixed    $value   [description]
	 * @param    string   $key     [description]
	 * @return   class             This class instance
	 */
	public function push($values, $key = null)
	{
		if(is_array($values)) {

			foreach ($values as $value) {
				
				$this->__pushItem($value, $key);

			}

		} else {

			$this->__pushItem($values);
		}

		return $this;
	}


	/**
	 * Push individual items to indexed arrays
	 *
	 * @param    mixed    $value   THe value to be inserted
	 * @param    string   $key     Optional key to namespace the pushed value
	 * @return   void        
	 */
	private function __pushItem($value, $key = null) 
	{
		if($key) {

			if (!array_key_exists($key, $this->data)) {

	            $this->data[$key] = $value;

	        } elseif (is_array($this->data[$key])) {

	            $this->data[$key][] = $value;

	        } else {

	            $this->data[$key] = array($this->data[$key], $value);

	        }

		} else {

			 $this->data[] = $value;
		}
	}

	/**
	 * Removes a value from a given key or 
	 * from the $data indexed array
	 *
	 * @param    string    $value   Value to be popped 
	 * @param    string    $key     Optional key to pop the value from
	 * @return   class              This class instance
	 */
	public function pop($value, $key = null)
	{
		if(!$key){

			$value_key = array_search($value, $this->data);

	        if($value_key !== false) 
	        	unset($this->data[$value_key]);

		} else {

			if (!array_key_exists($key, $this->data)) return $this;

	        if (is_array($this->data[$key])) {

	        	$value_key = array_search($value, $this->data[$key]);

	        	if($value_key) unset($this->data[$key][$value_key]);

	        } else {

	            $this->remove($key);
	            
	        }

	    }

		return $this;
	}

	/**
	 * Gets values for provide key, keys or
	 * all the existing data if no key is passed
	 *
	 * @param    string|array   $key 
	 * @return   mixed               
	 */
	public function get($key)
	{
		if (is_string($key)) return $this->__get($key);

		if (is_array($key) && !empty($key)) {

			$keys    = $key;
			$results = array();

			foreach ($keys as $key) {
				$results[$key] = $this->__get($key);
			}

			return $results;

		}
	}

	/**
	 * Returns all data
	 * 
	 * @return array All data currently stored
	 */
	public function getAll()
	{
		return $this->data;
	}

	/**
	 * Removes one key or mroe key
	 * 
	 * @param    string|array   $key Key or keys to be removed
	 * @return   class          This class instance
	 */
	public function remove($key)
	{
		if (!$key) return $this;

		if (is_string($key)) $this->__unset($key);

		if (is_array($key) && !empty($key)){

			$keys = $key;

			foreach ($keys as $key) {

				$this->__unset($key);

			}

		}

		return $this;
	}

	/**
	 * Removes all data
	 * 
	 * @return   class   This class instance
	 */
    public function clear()
    {
        $this->data = array();

        return $this;
    }

	/**
	 * Get list of keys
	 *
	 * @param    boolean   $with_value   Optionally only return keys with value
	 * @return   array                   Indexed array with keys
	 */
	public function getKeys($with_value = false)
	{	
		if(!$with_value) return array_keys($this->data);

		$keys = array();

		foreach ($this->data as $key => $value) {
			
			if($with_value && !$value) continue;

			$keys[] = $key;
		}

		return $keys;
	}

	/**
	 * Case insensitive $key search
	 * 
	 * @param    string   $key   Key to be searched
	 * @return   mixed           The match for the searched key or false if not found
	 */
    public function keySearch($key)
    {
        foreach (array_keys($this->data) as $k) {

            if (!strcasecmp($k, $key)) {

                return $k;

            }

        }

        return false;
    }

    /**
     * Checks if the provided $key have an exact match
     * 
     * @param    string    $key   Key to search for
     * @return   boolean          True if the $key exists and false if not
     */
	public function hasKey($key)
	{
		return array_key_exists($key, $this->data);
	}

	/**
	 * Checks if the provided $value have an exact match
	 * 
	 * @param    mixed     $value   Value to be searched for
	 * @param    string    $key     Optional key to be searched for the given $value
	 * @return   boolean            True if the value was found and false if not
	 */
	public function hasValue($value, $key = null)
    {
    	if (!$key) {

			$key = array_search($value, $this->data);

    	} else {

			if (!array_key_exists($key, $this->data)) return false;

	        if (is_array($this->data[$key])) {

	            $key = array_search($value, $this->data[$key]);

	        } else {

	            $key = $this->data[$key] == $value ? true : false;

	        }

    	}

        return $key === false ? false : true;
    }

    /**
     * Counts the number of items in the target container
     * 
     * @param    boolean   $key   Optional key to look for and count items
     * @return   integer          Number of items contained
     */
    public function count($key = false)
    {
        if($key && isset($this->data[$key])) return is_array($this->data[$key]) ? count($this->data[$key]) : (!empty($this->data[$key]) ? 1 : 0);

        return count($this->data);
    }

    /**
     * Taking control over the __set overloading magic method
     * 
     * @param   string   $key     Key that will hold the $value
     * @param   mixed    $value   Value to be stored
     */
	public function __set($key, $value)
	{	
		$this->data[$key] = $value;
	}

	/**
	 * Taking control over the __get overloading magic method
	 * 
	 * @param    string   $key   Key to lok for and return its value
	 * @return   mixed           Value of the key or null
	 */
	public function __get($key)
	{
		$value = array_key_exists($key, $this->data) ? $this->data[$key] : null;

		return $value;
	}

	/**
	 * Taking control over the __unset overloading magic method
	 * 
	 * @param   string   $key   Key to be unset
	 */
	public function __unset($key)
    {
    	unset($this->data[$key]);

    	return $this;
    }

}