<?php

namespace Ponticlaro\Bebop\Css;

use Ponticlaro\Bebop;

class CssScriptsHook extends \Ponticlaro\Bebop\Patterns\ScriptsHook {

	/**
	 * Registers a single script
	 * 
	 * @param string  $id           Script ID
	 * @param string  $file_path    Script file path
	 * @param array   $dependencies Script dependencies
	 * @param string  $version      Script version
	 * @param string  $media        String specifying the media for which this stylesheet has been defined
	 */
	public function register($id, $file_path, array $dependencies = array(), $version = null, $media = 'all')
	{
		$script = new \Ponticlaro\Bebop\Css\CssScript($id, $file_path, $dependencies, $version, $media);

		$this->scripts->set($id, $script);
		$this->register_list->push($id);

		return $this;
	}
}