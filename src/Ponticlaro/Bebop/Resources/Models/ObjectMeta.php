<?php

namespace Ponticlaro\Bebop\Resources\Models;

use Ponticlaro\Bebop\Utils;

class ObjectMeta {

	public $__type;

	public $__post_id;

	public $__user_id;

	public $__comment_id;

	public $__id;

	public $__key;

	public $value;

	public function __construct()
	{
		$this->__id = (int) $this->__id;

		if (isset($this->__post_id)) {

			$this->__post_id = (int) $this->__post_id;
			$this->__type    = 'postmeta';
		
		} else {

			unset($this->__post_id);
		}

		if (isset($this->__user_id)) {

			$this->__user_id = (int) $this->__user_id;
			$this->__type    = 'usermeta';
		
		} else {

			unset($this->__user_id);
		}

		if (isset($this->__comment_id)) {

			$this->__comment_id = (int) $this->__comment_id;
			$this->__type       = 'commentmeta';
		
		} else {

			unset($this->__comment_id);
		}

		// Handle JSON value
		if (is_string($this->value) && Utils::isJson($this->value)) {

			$this->value = json_decode($this->value);
		}

		// Try to unserialize
		elseif(unserialize($this->value)) {

			$this->value = unserialize($this->value);
		}
	}

	public function __toString()
	{
		return $this->value;
	}
}