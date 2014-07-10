<?php

namespace Ponticlaro\Bebop\Db\Query;

class ArgChild implements ArgChildInterface {
    
    protected $key;

    protected $value;
    
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

    public function isComplete()
    {
        return $this->key && $this->value ? true : false;
    }
}