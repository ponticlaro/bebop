<?php

namespace Ponticlaro\Bebop\Patterns;

interface CollectionInterface {	

	public function clear();
	public function set($path, $value = true);
	public function setList(array $values);
	public function shift($path = null);
	public function unshift($value, $path = null);
	public function unshiftList(array $values, $path = null);
	public function push($value, $path = null);
	public function pushList(array $values, $path = null);
	public function pop($value, $path = null);
	public function popList(array $values, $path = null);
	public function remove($path);
	public function removeList(array $paths);
	public function get($path);
	public function getList(array $paths);
	public function getAll();
	public function getKeys($path = null);
	public function hasKey($path);
	public function hasValue($value, $path = null);
    public function count($path = false);
}