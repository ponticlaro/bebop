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

	/**
	 * Return target file version based on modification date
	 * 
	 * @param  string $file_path Path to target file
	 * @return string            Unix timestamp
	 */
	public static function getFileVersion($file_path)
	{
		return file_exists($file_path) ? filemtime($file_path) : null;
	}

	/**
	 * Returns the URL from an absolute path
	 * 
	 * @param  string  $path     Path from which we need the URL to
	 * @param  boolean $relative Return relative or absolute URL
	 * @return string            URL for the target path
	 */
	public static function getPathUrl($path, $relative = false)
	{
		if (!is_string($path)) return null;

		$content_base = basename(WP_CONTENT_URL);
		$path         = str_replace(ABSPATH, '', $path);
		$url          = '/'. preg_replace("/.*$content_base/", "$content_base", $path);
		
		return $relative ? $url : home_url() . $url; 
	}
}