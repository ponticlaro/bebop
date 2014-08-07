<?php

namespace Ponticlaro\Bebop\Db\Query;

class Arg implements ArgInterface {
    
    protected $key;

    protected $value;

    protected $is_parent = false;
    
    protected $is_child = false;

    protected $parent_key;

    protected $has_multiple_keys = false;

    protected $current_child;
    
    public function isParent()
    {
        return $this->is_parent;
    }

    public function isChild()
    {
        return $this->is_child;
    }

    public function getParentKey()
    {
        return $this->parent_key;
    }

    public function addChild()
    {

    }

    public function getCurrentChild()
    {
        return $this->current_child;
    }

    public function hasMultipleKeys()
    {
        return $this->has_multiple_keys;
    }

    public function setKey($key)
    {
        if (is_string($key))
            $this->key = $key;
        
        return $this; 
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setValue($value)
    {   
        $this->value = $value;
        
        return $this; 
    }

    public function getValue()
    {
        return $this->value;
    }
}