<?php

namespace Ponticlaro\Bebop;

use Ponticlaro\Bebop;

class API {

	const API_PREFIX = '_bebop-api';

	/**
	 * Bebop API instance
	 * 
	 * @var Ponticlaro\Bebop\API
	 */
	private static $__instance;

	private $client;

	private function __construct()
	{
		$url          = Bebop::getUrl('home') .'/'. self::API_PREFIX;
		$this->client = new Http\Client($url);
	}

	/**
	 * Gets single instance of Bebop UI
	 * 
	 * @return Ponticlaro\Bebop\UI Bebop UI class instance
	 */
	public static function getInstance() 
	{
		if(!self::$__instance || !is_a(self::$__instance, 'Ponticlaro\Bebop\API')) {

			self::$__instance = new API();
		}

		return self::$__instance;
	}

	public function __call($method, $args)
	{
		$resource = isset($args[0]) ? $args[0] : '';

		return $this->client->$method($resource);
	}
}