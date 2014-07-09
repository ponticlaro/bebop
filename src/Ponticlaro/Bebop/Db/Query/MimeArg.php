<?php

namespace Ponticlaro\Bebop\Db\Query;

class MimeArg extends Arg {
	
	protected $key = 'post_mime_type';

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

	public function	is($mime_type)
	{
		if (is_string($mime_type))
			$this->value = $mime_type;

		return $this;
	}

	public function	in(array $mime_types = array())
	{
		$this->value = $mime_types;

		return $this;
	}
}