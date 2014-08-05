<?php

namespace Ponticlaro\Bebop\Http;

use Ponticlaro\Bebop;

class Client {

    /**
     * Base URL to make requests
     * 
     * @var string
     */
    private $__url;

    /**
     * General configuration value
     * 
     * @var \Ponticlaro\Common\Collection
     */
    private $__config;

    /**
     * Request Headers
     * 
     * @var \Ponticlaro\Common\Collection
     */
    private $__headers;

    /**
     * Request Cookies
     * 
     * @var \Ponticlaro\Common\Collection
     */
    private $__cookies;

    /**
     * Instantiate new Http Client
     * 
     * @param string $url Base URL for all requests
     */
    public function __construct($url = '/')
    {
        // Throw error if there is no base URL
        if (!is_string($url))
            throw new Exception("You must specify the base URL for all requests", 1);

        // Store reference to base url
        $this->__url = $url;

        // Get WordPress version from global
        global $wp_version;

        // Default configuration
        $default_config = array(
            'timeout'     => 5,
            'redirection' => 5,
            'httpversion' => '1.0',
            'user-agent'  => 'WordPress/' . $wp_version . '; ' . get_bloginfo('url'),
            'blocking'    => true,
            'body'        => null,
            'compress'    => false,
            'decompress'  => true,
            'sslverify'   => true,
            'stream'      => false,
            'filename'    => null
        );

        // Instantiate configuration collections
        $this->__config  = Bebop::Collection($default_config);
        $this->__headers = Bebop::Collection();
        $this->__cookies = Bebop::Collection();
    }

    /**
     * Set base URL
     * 
     * @param string $auth The target base URL
     */
    public function setUrl($url)
    {
        $this->__url = $url;

        return $this;
    }

    /**
     * Set Authorization header string
     * 
     * @param string $auth The Authorization header string
     */
    public function setAuth($value)
    {
        $this->__set('Authorization', $value, 'headers');

        return $this;
    }

    /**
     * Sets single header
     * 
     * @param string $header Header name
     * @param mixed  $value  Header value
     */
    public function setHeader($name, $value)
    {
        $this->__setConfig($name, $value, 'headers');
        
        return $this;
    }

    /**
     * Sets single cookie
     * 
     * @param string $header Cookie name
     * @param mixed  $value  Cookie value
     */
    public function setCookie($name, $value)
    {
        $this->__setConfig($name, $value, 'cookies');
        
        return $this;
    }

    /**
     * Set configuration value
     * 
     * @param string $key   Configuration key
     * @param mixed  $value Configuration value
     */
    public function set($key, $value)
    {
        $this->__setConfig($key, $value);

        return $this;
    }

    /**
     * Sets single value into target configuration collection
     * 
     * @param string $key        Configuration key
     * @param mixed  $value      Configuration value
     * @param string $collection Configuration collection
     */
    private function __setConfig($key, $value, $collection_name = 'config')
    {   
        if (is_string($key) && (is_string($value) || is_bool($value) || is_numeric($value))) {

            $collection = '__'. $collection_name;

            $this->{$collection}->set($key, $value);
        }
    }

    /**
     * Call magic method to allow any request method/verb
     * 
     * @param  string                           $method Method/Verb of the request
     * @param  array                            $args   Array with all the arguments
     * @return Ponticlaro\Bebop\Http\Response         Response object
     */
    public function __call($method, $args)
    {   
        // Define URL
        $url = isset($args[0]) && is_string($args[0]) ? $this->__url .'/'. $args[0] : $this->__url;

        // Get current call configuration array
        $call_config = isset($args[1]) ? $args[1] : array();

        // Get current call headers
        $call_headers = isset($call_config['headers']) && is_array($call_config['headers']) ? $call_config['headers'] : array();
  
        // Get current call cookies
        $call_cookies = isset($call_config['cookies']) && is_array($call_config['cookies']) ? $call_config['cookies'] : array();
        
        // Unset headers and cookies before mixing with config collection
        if (isset($call_config['headers'])) unset($call_config['headers']);
        if (isset($call_config['cookies'])) unset($call_config['cookies']);

        // Mix with cached configuration
        $this->__config->set($call_config);
        $this->__headers->set($call_headers);
        $this->__cookies->set($call_cookies);

        // Build final request configuration
        $config = $this->__config->getAll();
        $config['headers'] = $this->__headers->getAll();
        $config['cookies'] = $this->__cookies->getAll();

        // Build args array
        $args = array(
            $url,
            $config
        );

        // Make request and return response
        return self::__makeRequest($method, $args);
    }

    /**
     * Call static magic method to allow any request method/verb
     * 
     * @param  string                           $name Method/Verb of the request
     * @param  array                            $args Array with all the arguments
     * @return Ponticlaro\Bebop\Http\Response       Response object
     */
    public static function __callStatic($method, $args)
    {
        return self::__makeRequest($method, $args);
    }

    /**
     * Make single request based on magic call methods
     * 
     * @param  string                           $name Method/Verb of the request
     * @param  array                            $args Array with all the arguments
     * @return Ponticlaro\Bebop\Http\Response       Response object
     */
    private static function __makeRequest($method, $args)
    {
        // Get url
        $url = isset($args[0]) ? $args[0] : null;

        if (!$url)
            throw new Exception("You must specify the target URL for this request", 1);
        
        // Set request method from called method method
        $args[1]           = $args[1] ?: array();
        $args[1]['method'] = strtoupper($method);

        var_dump($args);

        // Make request and return response object
        $response = call_user_func_array('wp_remote_request', $args);

        // Build and return response object
        return self::__getResponse($response);
    }

    /**
     * Build response object
     * 
     * @param  mixed                            $response Response from wp_remote_request
     * @return Ponticlaro\Bebop\Http\Response           Response object
     */
    private static function __getResponse($response)
    {
        return new Response($response);
    }
}