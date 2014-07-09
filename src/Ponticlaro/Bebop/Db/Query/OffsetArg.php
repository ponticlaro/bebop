<?php

namespace Ponticlaro\Bebop\Db\Query;

class OffsetArg extends Arg {
	
	protected $key = 'offset';

	public function __construct($offset = null)
	{
		if ($offset) 
			$this->is($offset);
	}

	public function	is($offset)
	{
		if (is_numeric($offset))
			$this->value = $offset;

		return $this;
	}
}