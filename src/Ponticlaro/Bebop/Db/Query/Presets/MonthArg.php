<?php

namespace Ponticlaro\Bebop\Db\Query\Presets;

class MonthArg extends \Ponticlaro\Bebop\Db\Query\Arg {
    
    protected $key = 'monthnum';

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