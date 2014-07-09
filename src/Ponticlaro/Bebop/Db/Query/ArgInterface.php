<?php

namespace Ponticlaro\Bebop\Db\Query;

interface ArgInterface {
 	
 	public function isComplete();
 	public function isParent();
 	public function addChild();
 	public function getCurrentChild();
	public function setKey($key);
	public function getKey();
	public function setValue($value);
	public function getValue();
}