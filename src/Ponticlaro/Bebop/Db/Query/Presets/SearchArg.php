<?php

namespace Ponticlaro\Bebop\Db\Query\Presets;

use Ponticlaro\Bebop\Db\Query\Arg;

class SearchArg extends Arg {

    protected $key = 's';

    public function __construct($keywords = null)
    {
        if (is_string($keywords)) 
            $this->value = $keywords;
    }
}