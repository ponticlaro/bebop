<?php

namespace Ponticlaro\Bebop\Api;

class Route {

    /**
     * Route HTTP method
     * 
     * @var string
     */
    protected $method;

    /**
     * Route relative path
     * 
     * @var string
     */
    protected $path;

    /**
     * Route function
     * 
     * @var string
     */
    protected $fn;

    /**
     * Instantiates new route
     * 
     * @param string  $method Route HTTP method
     * @param string  $path   Route relative path
     * @param string  $fn     Route function
     */
    public function __construct($method, $path, $fn)
    {
        // Validate method and path
        if (!is_string($method) || !is_string($path)) 
            throw new \UnexpectedValueException('Both $method and $paht need to be a string');

        // Validate function
        if (!is_callable($fn) || (is_array($fn) && !is_object($fn[0]) && !is_callable($fn[1])))
            throw new \UnexpectedValueException('$fn needs to be callable');

        // Save route data
        $this->method = strtolower($method);
        $this->path   = $path;
        $this->fn     = $fn;
    }

    /**
     * Returns route HTTP method
     * 
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Returns route relative path
     * 
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Returns route function
     * 
     * @return mixed
     */
    public function getFunction()
    {
        return $this->fn;
    }

    /**
     * Returns parsed path using provided arguments to replace placeholders
     * 
     * @return string Full URL for the Api endpoint
     */
    public function parsePath()
    {
        // Get function arguments
        $args = func_get_args();

        // Get relative path
        $path = $this->getPath();
        
        // Get placeholders in route path
        preg_match_all('/:[A-Za-z_-]+/', $path, $placeholders);

        // Replace argument placeholders in route path
        if ($placeholders) {
            
            foreach ($placeholders[0] as $index => $placeholder) {
                
                $value = isset($args[$index]) ? $args[$index] : '';
                $path  = str_replace($placeholder, $value, $path);
            }
        }

        // Find optional sections
        preg_match_all('/\([^\)]+\)/', $path, $optional_sections);

        // Remove empty optional sections
        if ($optional_sections) {
            
            foreach ($optional_sections[0] as $index => $raw_section) {

                $section = str_replace(array('/', ' '), '', $raw_section);

                // Remove optional section if empty
                if ($section === '()')
                    $path = str_replace($raw_section, '', $path);
            }
        }

        // Return relative URL with trailing slash
        return '/'. rtrim($path, '/') .'/';
    }
}