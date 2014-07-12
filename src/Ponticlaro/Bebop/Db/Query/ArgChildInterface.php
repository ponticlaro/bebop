<?php

namespace Ponticlaro\Bebop\Db\Query;

interface ArgChildInterface {
    
    public function isComplete();
    public function setKey($key);
    public function getKey();
    public function setValue($value);
    public function getValue();
}