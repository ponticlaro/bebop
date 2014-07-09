<?php

namespace Ponticlaro\Bebop\Db\Query;

class TaxArgItem {

	protected $tax;

	protected $terms;

	protected $field;

	protected $operator;

	protected $include_children;

	public function __construct($tax = null)
	{
		if ($tax && is_string($tax))
			$this->tax = $tax;
	}

	public function in($terms)
	{
		if ($terms) {
			
			$this->field    = is_string($terms) || is_array($terms) && is_string($terms[0]) ? 'slug' : 'term_id';
			$this->operator = 'IN';
			$this->terms    = $terms;
		}

		return $this;
	}

	public function notIn($terms)
	{
		if ($terms) {
			
			$this->field    = is_string($terms) || is_array($terms) && is_string($terms[0]) ? 'slug' : 'term_id';
			$this->operator = 'NOT IN';
			$this->terms    = $terms;
		}

		return $this;
	}

	public function allOf($terms)
	{
		if ($terms) {
			
			$this->field    = is_string($terms) || is_array($terms) && is_string($terms[0]) ? 'slug' : 'term_id';
			$this->operator = 'AND';
			$this->terms    = $terms;
		}

		return $this;
	}

	public function includeChildren($include)
	{
		if (is_bool($include))
			$this->include_children = $include;

		return $this;
	}

	public function has($key)
	{
		return isset($this->{$key}) && $this->{$key} ? true : false;
	}

	public function isComplete()
	{
		return $this->tax && $this->terms && $this->field && $this->operator ? true : false;
	}

	public function getArgs()
	{
		return array(
			'taxonomy'         => $this->tax,
			'terms'            => $this->terms,
			'field'            => $this->field,
			'operator'         => $this->operator,
			'include_children' => $this->include_children
		);
	}
}