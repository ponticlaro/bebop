<?php

namespace Ponticlaro\Bebop\UI;

interface PluginInterface{

	public function __construct();
	public function load();
	public static function setKey($key);
	public static function getKey();
	public function setConfig($key, $value);
	public function setUI($key, $value);
	public function render();
}