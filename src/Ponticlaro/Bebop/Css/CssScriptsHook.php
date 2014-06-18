<?php

namespace Ponticlaro\Bebop\Css;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\Css\CssScript;

class CssScriptsHook extends \Ponticlaro\Bebop\Patterns\ScriptsHook {

    /**
     * Registers a single script
     * 
     * @param string  $id           Script ID
     * @param string  $path         Script path
     * @param array   $dependencies Script dependencies
     * @param string  $version      Script version
     * @param string  $media        String specifying the media for which this stylesheet has been defined
     */
    public function register($id, $path, array $dependencies = array(), $version = null, $media = 'all')
    {
        $script = new CssScript($id, $path, $dependencies, $version, $media);

        $this->scripts->set($id, $script);
        $this->register_list->push($id);

        return $this;
    }
}