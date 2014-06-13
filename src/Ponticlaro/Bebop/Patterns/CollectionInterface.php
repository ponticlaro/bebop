<?php

namespace Ponticlaro\Bebop\Patterns;

interface CollectionInterface {	

	public function __construct(array $data = array());
	public function set($key, $value = true);
	public function shift($key = null);
	public function unshift($values, $key = null);
	public function push($values, $key = null);
	public function pop($value, $key = null);
	public function get($key = null);
	public function getAll();
	public function remove($key);
    public function clear();
	public function getKeys($with_value = false);
    public function keySearch($key);
	public function hasKey($key);
	public function hasValue($value, $key = null);
    public function count($key = false);
}