<?php

namespace Ponticlaro\Bebop\Db\Query;

class MetaArgItem {

	protected $key;

	protected $value;

	protected $type;

	protected $compare;

	public function __construct($key = null)
	{
		if ($key && is_string($key))
			$this->key = $key;
	}

	public function type($type)
	{
		if (is_string($type))
			$this->type = $type;

		return $this;
	}

	public function exists()
	{
		$this->compare = 'EXISTS';

		return $this;
	}

	public function notExists()
	{
		$this->compare = 'NOT EXISTS';

		return $this;
	}

	public function is($value)
	{
		$this->compare = '=';
		$this->value   = $value;

		return $this;
	}

	public function isNot($value)
	{
		$this->compare = '!=';
		$this->value   = $value;

		return $this;
	}

	public function like($value)
	{
		$this->compare = 'LIKE';
		$this->value   = $value;

		return $this;
	}

	public function notLike($value)
	{
		$this->compare = 'NOT LIKE';
		$this->value   = $value;

		return $this;
	}

	public function in($value)
	{	
		$this->compare = 'IN';
		$this->value   = $value;

		return $this;
	}

	public function notIn($value)
	{
		$this->compare = 'NOT IN';
		$this->value   = $value;

		return $this;
	}

	public function between($start_value, $end_value)
	{			
		$this->compare = 'BETWEEN';
		$this->value   = array($start_value, $end_value);

		return $this;
	}

	public function notBetween($start_value, $end_value)
	{
		$this->compare = 'NOT BETWEEN';
		$this->value   = array($start_value, $end_value);

		return $this;
	}

	public function lt($value)
	{
		$this->compare = '<';
		$this->value   = $value;

		return $this;
	}

	public function lte($value)
	{			
		$this->compare = ' <= ';
		$this->value   = $value;

		return $this;
	}

	public function gt($value)
	{
		$this->compare = '>';
		$this->value   = $value;

		return $this;
	}

	public function gte($value)
	{
		$this->compare = ' >= ';
		$this->value   = $value;

		return $this;
	}

	public function has($key)
	{
		return isset($this->{$key}) && $this->{$key} ? true : false;
	}

	public function getArgs()
	{
		return array(
			'key'     => $this->key,
			'value'   => $this->value,
			'type'    => $this->type,
			'compare' => $this->compare
		);
	}
}