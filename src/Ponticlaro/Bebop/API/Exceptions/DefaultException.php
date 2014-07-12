<?php

namespace Ponticlaro\Bebop\API\Exceptions;

use Ponticlaro\Bebop;

class DefaultException extends \Exception {

	const DEFAULT_HTTP_STATUS = 400;

	protected $http_status;

	/**
	 * Collection of optional data
	 * 
	 * @var Ponticlaro\Bebop\Common\Collection
	 */
	public $data;

	public function __construct($message, $http_status, array $options = array())
	{	
		// Set message
		$this->message = $message;

		// Set defaults
		$this->http_status = self::DEFAULT_HTTP_STATUS;

		// Set options
		$this->data = Bebop::Collection();
		$this->data->set($options);
	}

	public function getHttpStatus()
	{
		return $this->http_status;
	}

	public function __call($name, $args)
	{
		if (!method_exists($this->data, $args)) return;

		return call_user_func_array($this->data, $args);
	}
}	