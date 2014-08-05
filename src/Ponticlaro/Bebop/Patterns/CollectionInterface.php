<?php

namespace Ponticlaro\Bebop\Patterns;

interface CollectionInterface {	

	public function set($paths, $value = true);
	public function shift($path = null);
	public function unshift($values, $key = null);
	public function push($values, $key = null);
	public function pop($values, $key = null);
	public function remove($paths);
    public function clear();
	public function get($paths);
	public function getAll();
	public function getKeys($path = null);
	public function hasKey($path);
	public function hasValue($value, $path = null);
    public function count($path = false);
}