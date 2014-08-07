<?php

namespace Ponticlaro\Bebop\Db\Query\Presets;

use Ponticlaro\Bebop;

abstract class DateArgAbstract extends \Ponticlaro\Bebop\Db\Query\Arg {

    protected $key;

    protected $is_child = true;

    protected $parent_key = 'date_query';

    protected $data;

    public function __construct($value = null)
    {
        $this->data = Bebop::Collection();

        if ($value) 
            $this->is($value);
    }

    public function is($value)
    {
        if ($value) {

            $this->data->set($this->key, $value);
            $this->data->set('compare', '=');
        }

        return $this;
    }

    public function isNot($value)
    {
        if ($value) {

            $this->data->set($this->key, $value);
            $this->data->set('compare', '!=');
        }

        return $this;
    }

    public function gt($value)
    {
        if ($value) {

            $this->data->set($this->key, $value);
            $this->data->set('compare', '>');
        }

        return $this;
    }

    public function gte($value)
    {
        if ($value) {

            $this->data->set($this->key, $value);
            $this->data->set('compare', '>=');
        }

        return $this;
    }

    public function lt($value)
    {
        if ($value) {

            $this->data->set($this->key, $value);
            $this->data->set('compare', '<');
        }

        return $this;
    }

    public function lte($value)
    {
        if ($value) {

            $this->data->set($this->key, $value);
            $this->data->set('compare', '<=');
        }

        return $this;
    }

    public function in($value)
    {
        if ($value) {

            $this->data->set($this->key, $value);
            $this->data->set('compare', 'IN');
        }

        return $this;
    }

    public function notIn($value)
    {
        if ($value) {

            $this->data->set($this->key, $value);
            $this->data->set('compare', 'NOT IN');
        }

        return $this;
    }

    public function between($start, $end, $inclusive = true)
    {       
        if ($start && $end) {

            $this->data->set($this->key, array($start, $end));
            $this->data->set('compare', 'BETWEEN');
            $this->data->set('inclusive', $inclusive);
        }

        return $this;
    }

    public function notBetween($start, $end, $inclusive = true)
    {       
        if ($start && $end) {

            $this->data->set($this->key, array($start, $end));
            $this->data->set('compare', 'NOT BETWEEN');
            $this->data->set('inclusive', $inclusive);
        }

        return $this;
    }

    public function has($key)
    {
        return $this->data->hasKey($key) ? true : false;
    }

    public function actionIsAvailable($name)
    {
        return $this->data->hasKey('compare') ? false : true;
    }

    public function isValid()
    {
        return $this->data->hasKey('compare') && $this->data->hasKey($this->key) ? true : false;
    }

    public function getValue()
    {   
        return $this->isValid() ? $this->data->getAll() : null;
    }
}