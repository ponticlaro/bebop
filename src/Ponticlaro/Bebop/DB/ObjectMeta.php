<?php

namespace Ponticlaro\Bebop\DB;

use Ponticlaro\Bebop\Utils;

class ObjectMeta {

	protected $object_type;

	protected $object_id;

	public function __construct($object_type, $object_id)
	{
		$this->object_type = $object_type;
		$this->object_id   = $object_id;
	}

	public function get($meta_key, $single = false)
	{
		$data = get_metadata($this->object_type, $this->object_id, $meta_key, $single);

		if ($data) {
			foreach ($data as $key => $value) {
				
				if (Utils::isJson($value)) $data[$key] = json_decode($value);
			}
		}

		return $data;
	}

	public function add($meta_key, $meta_value, $unique = false)
	{	
		add_metadata($this->object_type, $this->object_id, $meta_key, $meta_value, $unique);

		return $this;
	}

	public function update($meta_key, $meta_value, $prev_value = null)
	{
		update_metadata($this->object_type, $this->object_id, $meta_key, $meta_value, $prev_value);

		return $this;
	}

	public function delete($meta_key = null, $meta_value = null, $delete_all = true)
	{
		delete_metadata($this->object_type, $this->object_id, $meta_key, $meta_value, $delete_all);

		return $this;
	}
}