<?php

namespace Ponticlaro\Bebop\Db\Query\Presets;

use Ponticlaro\Bebop\Db\Query\Arg;

class PostsPerPageArg extends Arg {
    
    protected $key = 'posts_per_page';

    public function __construct($ppp = null)
    {
        if (is_numeric($ppp)) 
            $this->is($ppp);
    }

    public function is($ppp)
    {
        if (is_numeric($ppp))
            $this->value = $ppp;

        return $this;
    }
}