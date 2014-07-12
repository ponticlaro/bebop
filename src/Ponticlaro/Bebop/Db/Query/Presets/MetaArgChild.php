<?php

namespace Ponticlaro\Bebop\Db\Query\Presets;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\Db\Query\ArgChild;

class MetaArgChild extends ArgChild {

    protected $data;

    public function __construct($key = null)
    {
        $this->data = Bebop::Collection();

        if ($key && is_string($key))
            $this->data->set('key', $key);
    }

    public function type($type)
    {
        if (is_string($type))
            $this->data->set('type', $type);

        return $this;
    }

    public function exists()
    {
        $this->data->set('compare', 'EXISTS');

        return $this;
    }

    public function notExists()
    {
        $this->data->set('compare', 'NOT EXISTS');

        return $this;
    }

    public function is($value)
    {
        $this->data->set('compare', '=');
        $this->data->set('value', $value);

        return $this;
    }

    public function isNot($value)
    {
        $this->data->set('compare', '!=');
        $this->data->set('value', $value);

        return $this;
    }

    public function in($value)
    {   
        $this->data->set('compare', 'IN');
        $this->data->set('value', $value);

        return $this;
    }

    public function notIn($value)
    {
        $this->data->set('compare', 'NOT IN');
        $this->data->set('value', $value);

        return $this;
    }

    public function like($value)
    {
        $this->data->set('compare', 'LIKE');
        $this->data->set('value', $value);

        return $this;
    }

    public function notLike($value)
    {
        $this->data->set('compare', 'NOT LIKE');
        $this->data->set('value', $value);

        return $this;
    }

    public function between($start_value, $end_value)
    {           
        $this->data->set('compare', 'BETWEEN');
        $this->data->set('value', array($start_value, $end_value));

        return $this;
    }

    public function notBetween($start_value, $end_value)
    {
        $this->data->set('compare', 'NOT BETWEEN');
        $this->data->set('value', array($start_value, $end_value));

        return $this;
    }

    public function lt($value)
    {
        $this->data->set('compare', '<');
        $this->data->set('value', $value);

        return $this;
    }

    public function lte($value)
    {           
        $this->data->set('compare', '<=');
        $this->data->set('value', $value);

        return $this;
    }

    public function gt($value)
    {
        $this->data->set('compare', '>');
        $this->data->set('value', $value);

        return $this;
    }

    public function gte($value)
    {
        $this->data->set('compare', '>=');
        $this->data->set('value ', $value);

        return $this;
    }

    public function has($key)
    {
        return $this->data->hasKey($key) ? true : false;
    }

    public function actionIsAvailable($name)
    {
        if (method_exists($this, $name)) {

            switch ($name) {

                case 'is':
                case 'isnot':
                case 'in':
                case 'notin':
                case 'like':
                case 'notLike':
                case 'between':
                case 'notbetween':
                case 'lt':
                case 'lte':
                case 'gt':
                case 'gte':

                    return $this->data->hasKey('compare') && $this->data->hasKey('value') ? false : true;
                    break;

                case 'exists':
                case 'notexists':

                    return $this->data->hasKey('compare') ? false : true;
                    break;

                case 'type':

                    return $this->data->hasKey('type') ? false : true;
                    break;
            }
        }

        return false;
    }

    public function isValid()
    {
        if ($this->data->hasKey('key') && $this->data->hasKey('compare')) {
            
            if (in_array($this->data->get('compare'), array('EXISTS', 'NOT EXISTS'))) {

                return true;
            }

            elseif ($this->data->hasKey('value')) {
                
                return true;
            }
        }

        return false;
    }

    public function getValue()
    {
        return $this->isValid() ? $this->data->get() : null;
    }
}