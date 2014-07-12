<?php

namespace Ponticlaro\Bebop\Db\Query;

interface ArgInterface {
    
    public function isParent();
    public function addChild();
    public function getCurrentChild();
    public function hasMultipleKeys();
    public function setKey($key);
    public function getKey();
    public function setValue($value);
    public function getValue();
}