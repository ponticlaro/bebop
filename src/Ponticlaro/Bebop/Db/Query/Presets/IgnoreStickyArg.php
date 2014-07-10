<?php

namespace Ponticlaro\Bebop\Db\Query\Presets;

use Ponticlaro\Bebop\Db\Query\Arg;

class IgnoreStickyArg extends Arg {
    
    protected $key = 'ignore_sticky_posts';

    public function __construct($ignore = null)
    {
        if (is_bool($ignore)) 
            $this->is($ignore);
    }

    public function is($ignore)
    {
        if (is_bool($ignore))
            $this->value = $ignore;

        return $this;
    }
}