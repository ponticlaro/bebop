<?php

namespace Ponticlaro\Bebop\Db\Query\Presets;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\Db\Query\ArgChild;

class TaxArgChild extends ArgChild {

    protected $data;

    public function __construct($tax = null)
    {
        $this->data = Bebop::Collection();

        if ($tax && is_string($tax))
            $this->data->set('taxonomy', $tax);
    }

    public function in($terms)
    {
        if ($terms) {
            
            $this->data->set('field', is_string($terms) || is_array($terms) && is_string($terms[0]) ? 'slug' : 'term_id');
            $this->data->set('operator', 'IN');
            $this->data->set('terms', $terms);
        }

        return $this;
    }

    public function notIn($terms)
    {
        if ($terms) {
            
            $this->data->set('field', is_string($terms) || is_array($terms) && is_string($terms[0]) ? 'slug' : 'term_id');
            $this->data->set('operator', 'NOT IN');
            $this->data->set('terms', $terms);
        }

        return $this;
    }

    public function allOf($terms)
    {
        if ($terms) {
            
            $this->data->set('field', is_string($terms) || is_array($terms) && is_string($terms[0]) ? 'slug' : 'term_id');
            $this->data->set('operator', 'AND');
            $this->data->set('terms', $terms);
        }

        return $this;
    }

    public function includeChildren($include)
    {
        if (is_bool($include))
            $this->data->set('include_children', $include);

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

                case 'in':
                case 'notIn':
                case 'allOf':
                    
                    return $this->data->hasKey('field') && $this->data->hasKey('operator') && $this->data->hasKey('terms') ? false : true;
                    break;

                case 'includechildren':
                    
                    return $this->data->hasKey('include_children') ? false : true;
                    break;
            }
        }

        return false;
    }

    public function isValid()
    {
        return $this->data->hasKey('taxonomy') && $this->data->hasKey('field') && $this->data->hasKey('operator') && $this->data->hasKey('terms') ? true : false;
    }

    public function getValue()
    {   
        return $this->isValid() ? $this->data->getAll() : null;
    }
}