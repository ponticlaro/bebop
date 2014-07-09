<?php

namespace Ponticlaro\Bebop\Db\Query;

class AuthorArg extends Arg {
	
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
			
			$this->key   = 'author_name';
			$this->value = $data;
		}

		elseif(is_numeric($data)) {

			$this->key   = 'author';
			$this->value = $data;
		}

		return $this;
	}

	public function in(array $ids = array())
	{
		$this->key   = 'author__in';
		$this->value = $ids;

		return $this;
	}

	public function notIn(array $ids = array())
	{
		$this->key   = 'author__not_in';
		$this->value = $ids;

		return $this;
	}
}