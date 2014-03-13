<?php

namespace Ponticlaro\Bebop\Http;

class Response {

	/**
	 * Response from a WP_Http request
	 * 
	 * @var mixed
	 */
	private $__response;

	/**
	 * Instantiates this class storing a reference to the response data
	 * 
	 * @param mixed $response Response from a WP_Http request
	 */
	public function __construct($response) 
	{	
		$this->__response = $response;
	}

	/**
	 * Get full response object
	 * 
	 * @return mixed
	 */
	public function getResponse()
	{
		return $this->__response;
	}

	/**
	 * Get target header
	 * 
	 * @param  string $header Target header string
	 * @return mixed          Header data
	 */
	public function getHeader($header) 
	{
		return is_string($header) ? wp_remote_retrieve_header($this->__response, $header) : false; 
	}

	/**
	 * Get all response headers
	 * 
	 * @return array
	 */
	public function getHeaders() 
	{
		return wp_remote_retrieve_headers($this->__response);
	}

	/**
	 * Get HTTP status code
	 * 
	 * @return int
	 */
	public function getCode() 
	{
		return wp_remote_retrieve_response_code($this->__response);
	}

	/**
	 * Get HTTP status message
	 * 
	 * @return string
	 */
	public function getMessage() 
	{
		return wp_remote_retrieve_response_message($this->__response);
	}

	/**
	 * Get response body
	 * 
	 * @return string
	 */
	public function getBody() 
	{
		return wp_remote_retrieve_body($this->__response);
	}

	/**
	 * Return response body when this object is used as a string
	 * 
	 * @return string Response body
	 */
	public function __toString()
	{
		return $this->getBody();
	}
}