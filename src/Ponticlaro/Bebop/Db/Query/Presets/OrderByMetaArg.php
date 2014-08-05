<?php

namespace Ponticlaro\Bebop\Db\Query\Presets;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\Db\Query\Arg;

class OrderByMetaArg extends Arg {
    
    protected $data;

    protected $has_multiple_keys = true;

    public function __construct($key = null, $direction = 'DESC', $numeric = false)
    {
        $this->data = Bebop::Collection();

        if (is_string($key) && is_string($direction) && is_bool($numeric)) {

            $this->data->set('orderby', $numeric ? 'meta_value_num' : 'meta_value');
            $this->data->set('meta_key', $key);
            $this->data->set('order', $direction);
        }
    }

    public function key($key, $numeric = false)
    {
        if (is_string($key)) {

            $this->data->set('orderby', $numeric ? 'meta_value_num' : 'meta_value');
            $this->data->set('meta_key', $key);
        }

        return $this;
    }

    public function direction($direction)
    {
        if (is_string($direction))
            $this->data->set('order', $direction);

        return $this;
    }

    public function isNumeric($is_numeric)
    {
        if (is_bool($is_numeric) && $is_numeric)
            $this->data->set('orderby', 'meta_value_num');

        return $this;
    }

    public function getValue()
    {
        return $this->data->getAll() ?: null;
    }
}