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
     * Status of multidimensional arrays access through dotted notation
     * 
     * @var boolean
     */
    protected $dotted_notation_enabled = true;

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
     * Enables dotted notation to access multidimensional arrays
     * 
     * @return Ponticlaro\Bebop\Patterns\CollectionAbstract This class instance
     */
    public function enableDottedNotation()
    {
        $this->dotted_notation_enabled = true;

        return $this;
    }

    /**
     * Disables dotted notation
     * 
     * @return Ponticlaro\Bebop\Patterns\CollectionAbstract This class instance
     */
    public function disableDottedNotation()
    {
        $this->dotted_notation_enabled = false;

        return $this;
    }

    /**
     * Checks if dotted notation is enabled
     * 
     * @return boolean True if enabled, false otherwise
     */
    protected function isDottedNotationEnabled()
    {
        return $this->dotted_notation_enabled;
    }

    /**
     * Overrides the default path separator
     * 
     * @param  string                                       $separator
     * @return Ponticlaro\Bebop\Patterns\CollectionAbstract            This class instance
     */
    public function setPathSeparator($separator)
    {
        if (is_string($separator))
            $this->path_separator = $separator;

        return $this;
    }

    /**
     * Removes all data
     *
     * @return Ponticlaro\Bebop\Patterns\CollectionAbstract This class instance
     */
    public function clear()
    {   
        $this->data = array();

        return $this;
    }

    /**
     * Sets a value for the given path
     *
     * @param  string                                       $path Path where the value should be stored
     * @param  mixed                                        value Value that should be stored
     * @return Ponticlaro\Bebop\Patterns\CollectionAbstract       This class instance
     */
    public function set($path, $value = true)
    {
        if (is_string($path) || is_numeric($path)) {

            $this->__set($path, $value);
        }

        elseif (is_array($path)) {

            $this->setList($path);
        }

        return $this;
    }

    /**
     * Sets a list of values
     * 
     * @param  array                                        $values
     * @return Ponticlaro\Bebop\Patterns\CollectionAbstract         This class instance
     */
    public function setList(array $values)
    {
        foreach ($values as $path => $value) {

            if (is_string($path)){

                $this->__set($path, $value);
            }

            elseif (is_numeric($path)) {
                
                $this->push($value);
            }
        }

        return $this;
    }

    /**
     * Adds items to the target path
     * 
     * @param  string                                       $path   Target path
     * @param  mixed                                        $values Key/Values pairs to be added
     * @return Ponticlaro\Bebop\Patterns\CollectionAbstract         This class instance
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
     * Adds a single item to the beginning of the target container array
     *  
     * @param  mixed                                        $value Value to be inserted
     * @param  string                                       $path  Optional path to unshift the value to
     * @return Ponticlaro\Bebop\Patterns\CollectionAbstract        This class instance
     */
    public function unshift($value, $path = null)
    {
        if (is_array($value)) {

            $this->unshiftList($value, $path);
        }

        else {
            
            $this->__unshiftItem($value, $path);
        }

        return $this;
    }

    /**
     * Adds a list of values to the beginning of the target container array
     * 
     * @param  array                                        $values List of valus to be inserted
     * @param  string                                       $path   Optional path to unshift values to
     * @return Ponticlaro\Bebop\Patterns\CollectionAbstract         This class instance
     */
    public function unshiftList(array $values, $path = null)
    {
        foreach (array_reverse($values) as $value) {
            
            $this->unshift($value, $path);
        }
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
     * @param  mixed                                        $values Values to be inserted
     * @param  string                                       $path   Optional path to push the value to
     * @return Ponticlaro\Bebop\Patterns\CollectionAbstract         This class instance
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
     * Pushes list of values to target path
     * 
     * @param  array                                        $values List of values to be pushed
     * @param  string                                       $path   Optional path to push values to
     * @return Ponticlaro\Bebop\Patterns\CollectionAbstract         This class instance
     */
    public function pushList(array $values, $path = null)
    {
        foreach ($values as $value) {
            
            $this->push($value, $path);
        }

        return $this;
    }

    /**
     * Removes a value from a given path or 
     * from the $data indexed array
     *
     * @param  string                                       $value Value to be popped 
     * @param  string                                       $path  Optional path to pop the value from
     * @return Ponticlaro\Bebop\Patterns\CollectionAbstract        This class instance
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
     * Pops a list of values
     * 
     * @param  array                                        $values List of values to be popped
     * @param  string                                       $path   Optional path to pop values from
     * @return Ponticlaro\Bebop\Patterns\CollectionAbstract         This class instance
     */
    public function popList(array $values, $path = null)
    {
        foreach ($values as $value) {
            
            $this->pop($value, $path);
        }

        return $this;
    }

    /**
     * Removes a single path
     * 
     * @param  string|array                                 $path Path to be removed
     * @return Ponticlaro\Bebop\Patterns\CollectionAbstract       This class instance
     */
    public function remove($path)
    {
        if (is_string($path)) {
            
            $this->__unset($path);
        }

        elseif (is_array($path)) {

            $this->removeList($path);
        }

        return $this;
    }

   /**
     * Removes a list of paths
     * 
     * @param  array                                        $paths List of paths to be removed
     * @return Ponticlaro\Bebop\Patterns\CollectionAbstract        This class instance
     */
    public function removeList(array $paths)
    {
        foreach ($paths as $path) {
            
            $this->remove($path);
        }
    }

    /**
     * Gets value for target path
     *
     * @param  string|array $path 
     * @return mixed               
     */
    public function get($path)
    {
        if (is_string($path)) {

            return $this->__get($path);
        }

        elseif (is_array($path)) {

            return $this->getList($path);
        }

        return null;
    }

    /**
     * Returns values for target paths
     * 
     * @param  array $paths Target paths
     * @return array        List of existing results
     */
    public function getList(array $paths)
    {
        $results = array();

        foreach ($paths as $path) {

            if (is_string($path)) {

                if ($this->isDottedNotationEnabled()) {

                    $data  = &$results;
                    $paths = explode($this->path_separator, $path);

                    while (count($paths) > 1) {
                            
                        $key = array_shift($paths);

                        if (!isset($data[$key]) || !is_array($data[$key])) {

                            $data[$key] = array(); 
                        }
                        
                        $data = &$data[$key];
                    }

                    $data[array_shift($paths)] = $this->__get($path);
                }

                else {

                    $results[$path] = $this->__get($path);
                }
            }
        }

        return $results;
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
     * @param  string                                       $path  Path that will hold the $value
     * @param  mixed                                        $value Value to be stored
     * @return Ponticlaro\Bebop\Patterns\CollectionAbstract        This class instance
     */
    public function __set($path, $value)
    {   
        if (is_string($path)) {

            if ($this->isDottedNotationEnabled()) {

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

                if (is_array($data)) {
                    
                    $data[array_shift($keys)] = $value;
                }

                else {

                    $data = $value;
                }
            }

            else {

                $this->data[$path] = $value;
            }
        }

        elseif (is_numeric($path)) {
            
            $this->push($value);
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

        if ($this->isDottedNotationEnabled()) {

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

        else {

            return $this->data[$path];
        }
    }

    /**
     * Taking control over the __unset overloading magic method
     * 
     * @param  string                                       $path Path to be unset
     * @return Ponticlaro\Bebop\Patterns\CollectionAbstract       This class instance
     */
    public function __unset($path)
    {
        if (is_string($path)) {

            if ($this->isDottedNotationEnabled()) {

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

            else {

                if (isset($this->data[$path]))
                    unset($this->data[$path]);
            }
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

        if ($this->isDottedNotationEnabled()) {

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

        else {

            return isset($this->data[$path]) ? true : false;
        }
    }
}