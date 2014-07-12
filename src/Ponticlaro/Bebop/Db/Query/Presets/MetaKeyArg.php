<?php

namespace Ponticlaro\Bebop\Db\Query\Presets;

use Ponticlaro\Bebop\Db\Query\Arg;

class MetaKeyArg extends Arg {
    
    protected $key = 'meta_key';

    public function __construct($key = null)
    {
        if (is_string($key)) 
            $this->is($key);
    }

    public function is($key)
    {
        if (is_string($key))
            $this->value = $key;

        return $this;
    }
}