<?php

namespace Ponticlaro\Bebop\Db\Query\Presets;

use Ponticlaro\Bebop\Db\Query\Arg;

class ParentArg extends Arg {

    public function __construct($data = null) 
    {
        if ($data) {
            
            if (is_array($data)) {
                
                $this->in($data);
            }

            elseif (is_numeric($data)) {
                
                $this->is($data);
            }
        }
    }

    public function is($id)
    {
        if (is_numeric($id)) {
            
            $this->key   = 'post_parent';
            $this->value = $id;
        }

        return $this;
    }

    public function in(array $ids = array())
    {   
        $this->key   = 'post_parent__in';
        $this->value = $ids;

        return $this;
    }

    public function notIn(array $ids = array())
    {   
        $this->key   = 'post_parent__not_in';
        $this->value = $ids;

        return $this;
    }
}