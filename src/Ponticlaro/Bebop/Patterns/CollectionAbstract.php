<?php

namespace Ponticlaro\Bebop\Patterns;

abstract class CollectionAbstract implements CollectionInterface, \IteratorAggregate, \Countable {

    /**
     * Array that contains all the data
     * 
     * @var array
     */
    protected $data = array();

    /**
     * Separator for path keys
     * 
     * @var string
     */
    protected $path_separator = '.';
    
    /**
     * Initialize Collection with optionally 
     * passed array with initial data
     *
     * @param array $data Optional initial data to be added
     */
    public function __construct(array $data = array())
    {
        $this->set($data);
    }

    /**
     * Overrides the default path separator
     * 
     * @param string $separator
     */
    public function setPathSeparator($separator)
    {
        if (is_string($separator))
            $this->path_separator = $separator;

        return $this;
    }

    /**
     * Sets a value for the given path
     *
     * @param string $path Path where the value should be stored
     * @param mixed  value Value that should be stored
     */
    public function set($paths, $value = true)
    {
        if (is_string($paths)) 
            $this->__set($paths, $value);

        if (is_array($paths)) {

            foreach ($paths as $path => $value) {

                $this->__set($path, $value);
            }
        }

        return $this;
    }

    /**
     * Adds items to the target path
     * 
     * @param string $path   Target path
     * @param mixed  $values Key/Values pairs to be added
     */
    public function add($path, $values)
    {
        $data = $this->__get($path);

        if (is_array($values)) {

            if (is_array($data)) {
                
                $data = array_merge($data, $values);
            }

            else {

                $data = $values;
            }
        } 

        else {

            $data[] = $values;
        }

        $this->__set($path, $data);

        return $this;
    }

    /**
     * Removed the first value from the indexed array
     * with a given $path or directly from the $data indexed array
     * 
     * @param  string $path Target array path
     * @return mixed        The removed value or null
     */
    public function shift($path = null)
    {
        if ($path) {
            
            $data = $this->__get($path);

            if (is_array($data)) {
                
                $value = array_shift($data);

                $this->__set($path, $data);
            }
        }

        else {

            $value = isset($this->data[0]) ? array_shift($this->data) : null;
        }

        return $value;
    }

    /**
     * Adds one or more items to the beginning 
     * of the target container array
     *  
     * @param    mixed    $value   Valuss to be inserted
     * @param    string   $path    Optional path to unshift the value to
     * @return   class             This collection
     */
    public function unshift($values, $path = null)
    {
        if (is_array($values)) {

            foreach ($values as $path => $value) {

                $this->__unshiftItem($value, $path);
            }
        }

        else {

            $this->__unshiftItem($values, $path);
        }

        return $this;
    }

    /**
     * Adds an item to to beginning of the target array
     *
     * @param  mixed  $value The valued to be inserted
     * @param  string $path  Optional path to unshift the value to
     * @return void      
     */
    private function __unshiftItem($value, $path = null)
    {
        if ($path) {
            
            $data = $this->__get($path);

            if (is_array($data)) {

                array_unshift($data, $value);
            } 

            else {

                $data = array($value);
            }

            $this->__set($path, $data);
        }

        else {

            array_unshift($this->data, $value);
        }
    }

    /**
     * Adds a value to a given path or to the $data indexed array
     *
     * @param  mixed  $values Values to be inserted
     * @param  string $path   Optional path to push the value to
     * @return class          This class instance
     */
    public function push($value, $path = null)
    {
        if ($path) {
            
            $data = $this->__get($path);

            if (is_array($data)) {

                $data[] = $value;
                
            } else {

                $data = array($value);
            }

            $this->__set($path, $data);
        }

        else {

            $this->data[] = $value;
        }

        return $this;
    }

    /**
     * Removes a value from a given path or 
     * from the $data indexed array
     *
     * @param  string $value Value to be popped 
     * @param  string $path  Optional path to pop the value from
     * @return class         This class instance
     */
    public function pop($value, $path = null)
    {
        if ($path) {
            
            $data = $this->__get($path);

            if (is_array($data)) {

                $key = array_search($value, $data);

                if ($key !== false) 
                    $this->__unset($path . $this->path_separator . $key);
            } 

            else {

                $this->__unset($path);
            }
        }

        else {

            $key = array_search($value, $this->data);

            if ($key !== false) 
                unset($this->data[$key]);
        }

        return $this;
    }

    /**
     * Removes one key or more keys
     * 
     * @param  string|array $paths Path or paths to be removed
     * @return class               This class instance
     */
    public function remove($paths)
    {
        if (is_string($paths)) 
            $this->__unset($paths);

        if (is_array($paths)) {

            foreach ($paths as $path) {

                $this->__unset($path);
            }
        }

        return $this;
    }

    /**
     * Removes all data
     *
     * @return class This class instance
     */
    public function clear()
    {   
        $this->data = array();

        return $this;
    }

    /**
     * Gets values for provide path or paths
     *
     * @param  string|array $paths 
     * @return mixed               
     */
    public function get($paths)
    {
        if (is_string($paths)) 
            return $this->__get($paths);

        if (is_array($paths)) {

            $results = array();

            foreach ($paths as $path) {

                $results[$path] = $this->__get($path);
            }

            return $results;
        }

        return null;
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
     * Get list of keys
     *
     * @param  boolean $with_value Optionally only return keys with value
     * @return array               Indexed array with keys
     */
    public function getKeys($path = null)
    {   
        $data = $path ? $this->__get($path) : $this->data;

        return is_array($data) ? array_keys($data) : null;
    }

    /**
     * Checks if the provided $path have an exact match
     * 
     * @param  string  $path Path to search for
     * @return boolean       True if the $key exists and false if not
     */
    public function hasKey($path)
    {
        return $this->__hasPath($path) ? true : false;
    }

    /**
     * Checks if the provided $value have an exact match
     * 
     * @param  mixed   $value Value to search for
     * @param  string  $path  Optional path to be searched for the given $value
     * @return boolean        True if the value was found and false if not
     */
    public function hasValue($value, $path = null)
    {
        if ($path) {
            
            $data = $this->__get($path);

            if (is_array($data)) {

                $key = array_search($value, $data);
            } 

            else {

                $key = $data == $value ? true : false;
            }
        }

        else {

            $key = array_search($value, $this->data);
        }

        return $key === false ? false : true;
    }

    /**
     * Counts the number of items
     * 
     * @param  boolean $path Optional path to look for and count items
     * @return integer       Number of items contained
     */
    public function count($path = null)
    {
        $data = $path ? $this->__get($path) : $this->data;

        return $data && is_array($data) ? count($data) : null;
    }

    /**
     * Returns data as an ArrayIterator instance
     * 
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->data);
    }

    /**
     * Taking control over the __set overloading magic method
     * 
     * @param string $path  Path that will hold the $value
     * @param mixed  $value Value to be stored
     */
    public function __set($path, $value)
    {   
        if (is_string($path)) {

            // Get current data as reference
            $data = &$this->data;

            // Explode keys
            $keys = explode($this->path_separator, $path);

            // Crawl though the keys
            while (count($keys) > 1) {

                $key = array_shift($keys);

                if (!isset($data[$key])) {

                    $data[$key] = array();
                }
                
                $data =& $data[$key];
            }

            $data[array_shift($keys)] = $value;
        }

        return $this;
    }

    /**
     * Taking control over the __get overloading magic method
     * 
     * @param  string $path Path to look for and return its value
     * @return mixed        Value of the key or null
     */
    public function __get($path)
    {
        if (!is_string($path)) return null;

        // Get current data as reference
        $data = &$this->data;

        // Explode keys
        $keys = explode($this->path_separator, $path);

        // Crawl though the keys
        while (count($keys) > 1) {

            $key = array_shift($keys);

            if (!isset($data[$key])) {

                return null;
            }
            
            $data =& $data[$key];
        }

        return $data[array_shift($keys)];
    }

    /**
     * Taking control over the __unset overloading magic method
     * 
     * @param string $path Path to be unset
     */
    public function __unset($path)
    {
        if (is_string($path)) {

            // Get current data as reference
            $data = &$this->data;

            // Explode keys
            $keys = explode($this->path_separator, $path);

            // Crawl though the keys
            while (count($keys) > 1) {

                $key = array_shift($keys);

                if (!isset($data[$key])) {

                    return $this;
                }
                
                $data =& $data[$key];
            }

            unset($data[array_shift($keys)]);
        }

        return $this;
    }

    /**
     * Checks if the target path exists
     * 
     * @param  sring   $path Target path to ve checked
     * @return boolean       True if exists, false otherwise
     */
    protected function __hasPath($path)
    {   
        if (!is_string($path)) return false;

        // Get current data as reference
        $data = &$this->data;

        // Explode keys
        $keys = explode($this->path_separator, $path);

        // Crawl though the keys
        while (count($keys) > 1) {

            $key = array_shift($keys);

            if (!isset($data[$key])) {

                return false;
            }
            
            $data =& $data[$key];
        }

        return isset($data[array_shift($keys)]) ? true : false;
    }
}