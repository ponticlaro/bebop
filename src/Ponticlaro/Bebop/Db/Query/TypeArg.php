<?php

namespace Ponticlaro\Bebop\Db\Query;

class TypeArg extends Arg {
	
	protected $key = 'post_type';

	public function __construct($post_types = null)
	{	
		if ($post_types) {

			if (is_array($post_types)) {
				
				$this->in($post_types);
			}

			elseif (is_string($post_types)) {
				
				$this->is($post_types);
			}
		}
	}

	public function	is($type)
	{
		if (is_string($type))
			$this->value = $type;

		return $this;
	}

	public function	in(array $types = array())
	{
		$this->value = $types;

		return $this;
	}
}