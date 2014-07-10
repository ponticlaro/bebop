<?php

namespace Ponticlaro\Bebop\Db\Query\Presets;

use Ponticlaro\Bebop\Db\Query\Arg;

class StatusArg extends Arg {
    
    protected $key = 'post_status';

    public function __construct($status = null)
    {
        if ($status) {

            if (is_array($status)) {
                
                $this->in($status);
            }

            elseif (is_string($status)) {
                
                $this->is($status);
            }
        }
    }

    public function is($status)
    {
        if (is_string($status))
            $this->value = $status;

        return $this;
    }

    public function in(array $statuses = array())
    {
        $this->value = $statuses;

        return $this;
    }
}