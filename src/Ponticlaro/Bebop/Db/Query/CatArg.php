<?php

namespace Ponticlaro\Bebop\Db\Query;

class CatArg extends Arg {
	
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

	public function	is($data)
	{
		if (is_string($data)) {

			$this->key   = 'category_name';
			$this->value = $data;
		}

		elseif (is_numeric($data)) {

			$this->key   = 'cat';
			$this->value = $data;
		}

		return $this;
	}

	public function	in(array $ids = array())
	{
		$this->key   = 'category__in';
		$this->value = $ids;

		return $this;
	}

	public function	notIn(array $ids = array())
	{
		$this->key   = 'category__not_in';
		$this->value = $ids;

		return $this;
	}

	public function	allOf(array $ids = array())
	{
		$this->key   = 'category__and';
		$this->value = $ids;

		return $this;
	}
}