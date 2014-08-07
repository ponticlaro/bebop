<?php

namespace Ponticlaro\Bebop\Db\Query\Presets;

use Ponticlaro\Bebop;

class DateArgChild extends \Ponticlaro\Bebop\Db\Query\ArgChild {

    protected $data;

    protected static $date_args_whitelist = array(
        'year',
        'month',
        'day',
        'hour',
        'minute',
        'second'
    );

    public function __construct(array $args = null)
    {
        $this->data = Bebop::Collection();

        if ($args)
        	$this->is($args);
    }

    /**
     * WORKS
     * 
     */
    public function is(array $args)
    {
    	if ($args) {

    		$this->data->set($this->__cleanDateArgs($args));
    		$this->data->set('compare', '=');
    	}

    	return $this;
    }

    /**
     * WORKS
     * 
     */
    public function isNot(array $args)
    {
    	if ($args) {

    		$this->data->set($this->__cleanDateArgs($args));
    		$this->data->set('compare', '!=');
    	}

    	return $this;
    }

    public function gt(array $args)
    {
        if ($args) {

            $this->data->set($this->__cleanDateArgs($args));
            $this->data->set('compare', '>');
        }

        return $this;
    }

    /**
     * WORKS
     * 
     */
    public function gte(array $args)
    {
        if ($args) {

            $this->data->set($this->__cleanDateArgs($args));
            $this->data->set('compare', '>=');
        }

        return $this;
    }

    public function lt(array $args)
    {
        if ($args) {

            $this->data->set($this->__cleanDateArgs($args));
            $this->data->set('compare', '<');
        }

        return $this;
    }

    /**
     * WORKS
     * 
     */
    public function lte(array $args)
    {
        if ($args) {

            $this->data->set($this->__cleanDateArgs($args));
            $this->data->set('compare', '<=');
        }

        return $this;
    }

    public function in(array $args)
    {
        if ($args) {

            $this->data->set($this->__cleanDateArgs($args));
            $this->data->set('compare', 'IN');
        }

        return $this;
    }

    public function notIn(array $args)
    {    	
   		if ($args) {

    		$this->data->set($this->__cleanDateArgs($args));
    		$this->data->set('compare', 'NOT IN');
    	}

    	return $this;
    }

    public function between(array $start_args, array $end_args, $inclusive = true)
    {    	
   		if ($start_args && $end_args) {

    		$this->data->set($this->__cleanDateArgs($args));
    		$this->data->set('compare', 'BETWEEN');
    		$this->data->set('inclusive', $inclusive);
    	}

    	return $this;
    }

    public function notBetween(array $start_args, array $end_args, $inclusive = true)
    {
   		if ($start_args && $end_args) {

    		$this->data->set($this->__cleanDateArgs($args));
    		$this->data->set('compare', 'NOT BETWEEN');
    		$this->data->set('inclusive', $inclusive);
    	}

    	return $this;
    }

    /**
     * WORKS
     * 
     */
    public function before(array $args)
    {
    	if ($args) {
    		
            $this->data->set('before', $this->__cleanDateArgs($args));
            $this->data->set('inclusive', $inclusive);
    	}

    	return $this;
    }

    /**
     * WORKS
     * 
     */
    public function after(array $args)
    {
    	if ($args) {
    		
            $this->data->set('after', $this->__cleanDateArgs($args));
    		$this->data->set('inclusive', $inclusive);
    	}

    	return $this;	
    }

    public function column($column)
    {
        if (is_string($column))
            $this->data->set('column', $column);

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

            	case 'column':

            		return $this->data->hasKey('column') ? false : true;
            		break;

                case 'is':
                case 'isnot':
                case 'gt':
                case 'gte':
                case 'lt':
                case 'lte':
                case 'in':
                case 'notin':
                case 'between':
                case 'notbetween':
                    
                    return $this->data->hasKey('before') || $this->data->hasKey('after') || $this->data->hasKey('compare') ? false : true;
                    break;

                case 'before':
                    
                    return $this->data->hasKey('compare') || $this->data->hasKey('before') ? false : true;
                    break;

                case 'after':
                    
                    return $this->data->hasKey('compare') || $this->data->hasKey('after') ? false : true;
                    break;
            }
        }

        return false;
    }

    public function isValid()
    {
        return $this->data->hasKey('compare') || $this->data->hasKey('before') || $this->data->hasKey('after') ? true : false;
    }

    public function getValue()
    {   
        return $this->isValid() ? $this->data->getAll() : null;
    }

    protected function __setQueryArgsFromDateArray(array $dates)
    {

    }

    protected function __cleanDateArgs($args)
    {
        if (!is_array($args)) return array();

        $clean = array();

        foreach ($args as $key => $value) {
            
            if (in_array($key, static::$date_args_whitelist)) {
                
                $clean[$key] = $value;
            }
        }

        return $clean;
    }
}