<?php

namespace Ponticlaro\Bebop\Db\Query\Presets;

class HourArg extends \Ponticlaro\Bebop\Db\Query\Arg {
    
    protected $key = 'hour';

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