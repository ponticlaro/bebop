<?php

namespace Ponticlaro\Bebop\Db\Query\Presets;

use Ponticlaro\Bebop\Db\Query\Arg;

class TagArg extends Arg {
    
    public function __construct($data = null)
    {   
        if ($data) {

            if (is_array($data)) {
            
                $this->in($data);
            }

            else {
                
                $this->is($data);
            }
        }
    }

    public function is($data)
    {
        if (is_string($data)) {
            
            $this->key   = 'tag';
            $this->value = $data;
        }

        if (is_numeric($data)) {

            $this->key   = 'tag_id';
            $this->value = $data;
        }

        return $this;
    }

    public function in(array $ids = array())
    {   
        if ($ids) {
            
            $this->key   = is_string($ids[0]) ? 'tag_slug__in' : 'tag__in';
            $this->value = $ids;
        }

        return $this;
    }

    public function notIn(array $ids = array())
    {
        $this->key   = 'tag__not_in';
        $this->value = $ids;

        return $this;
    }

    public function allOf(array $ids = array())
    {
        if ($ids) {
            
            $this->key   = is_string($ids[0]) ? 'tag_slug__and' : 'tag__and';
            $this->value = $ids;
        }

        return $this;
    }
}