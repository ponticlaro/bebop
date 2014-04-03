<?php

namespace Ponticlaro\Bebop;

class Utils
{
	private function __construct(){}

	private function __clone(){}

	public static function isNetwork()
	{
		return is_multisite();
	}

	public static function camelcaseToUnderscore($string) 
	{
		preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $string, $matches);
		$ret = $matches[0];
		foreach ($ret as &$match) {
			$match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
		}
		return implode('_', $ret);
	}

	public static function toCamelCase($string)
	{
		
	}

	public static function slugify($string, $options = array("separator" => "_") )
	{	
		// Remove any accented characters
		$string = \remove_accents($string);

		// Make it all lowercase
		$string = \strtolower($string);

		// Replace white spaces
		$string = \preg_replace("/ /", $options["separator"], $string);

		return $string;
	}

	public static function toCleanUrl($string)
	{


	}

	/**
	 * Checks if a variable contains valid JSON
	 * 
	 * @param  string  $value String to be checked
	 * @return boolean        True if the string is JSON, false if not
	 */
	public static function isJson($value)
	{
		json_decode($value);

 		return ((preg_match('/^\[/', $value) || preg_match('/^{/', $value)) && json_last_error() == JSON_ERROR_NONE) ? true : false;
	}
}