<?php

namespace Ponticlaro\Bebop\Db\Query\Presets;

class SecondArg extends \Ponticlaro\Bebop\Db\Query\Arg {
    
    protected $key = 'second';

    public function __construct($value = null)
    {
        if ($value) 
            $this->is($value);
    }

    public function is($value)
    {
        if ($value)
            $this->value = $value;

        return $this;
    }
}