<?php

namespace Ponticlaro\Bebop\Db\Query\Presets;

use Ponticlaro\Bebop\Db\Query\Arg;

class ResultsPageArg extends Arg {
    
    protected $key = 'paged';

    public function __construct($page = null)
    {
        if (is_numeric($page)) 
            $this->is($page);
    }

    public function is($page)
    {
        if (is_numeric($page))
            $this->value = $page;

        return $this;
    }
}