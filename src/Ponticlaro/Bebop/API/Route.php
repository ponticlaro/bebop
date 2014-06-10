<?php

namespace Ponticlaro\Bebop\API;

use Ponticlaro\Bebop;

class Route {

    protected $method;

    protected $path;

    protected $fn;

    protected $public;

    public function __construct($method, $path, $fn, $public = false)
    {
        if (!is_string($method) || !is_string($path)) 
            throw new \UnexpectedValueException('Both $method and $paht need to be a string');

        if (!is_callable($fn) || (is_array($fn) && !is_object($fn[0]) && !is_callable($fn[1])))
            throw new \UnexpectedValueException('$fn needs to be callable');

        $this->method = $method;
        $this->path   = $path;
        $this->fn     = $fn;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getFunction()
    {
        return $this->fn;
    }
}