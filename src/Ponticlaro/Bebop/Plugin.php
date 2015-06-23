<?php 

namespace Ponticlaro\Bebop;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\Css\CssManager;
use Ponticlaro\Bebop\Js\JsManager;
use Ponticlaro\Bebop\Patterns\TrackableObjectAbstract;

class Plugin extends TrackableObjectAbstract {

    /**
     * Required trackable object type
     * 
     * @var string
     */
    protected $__trackable_type = 'plugin';

    /**
     * Required trackable object ID
     * 
     * @var string
     */
    protected $__trackable_id;

    protected $file;

    protected $function;

    protected $url;

    protected $path;

    protected $css_manager;

    protected $js_manager;

	public function __construct($file, $function)
	{
		if (!is_string($file))
			throw new \Exception('Plugin file must be a string and a readable file');

		if (!is_callable($function))
			throw new \Exception('Plugin function must be callable');

		// Set trackable ID
		$this->__trackable_id = plugin_basename($file);

		// Save plugin file
		$this->file = $file;

		// Save function
		$this->function = $function;

		// Defined plugin URL
		$this->url = plugins_url('', $this->file);

		// Defined plugin path
		$this->path = plugin_dir_path($this->file);

		// Instantiate CSS manager
		$this->css_manager = CssManager::getInstance()->setBaseUrl($this->url);

		// Instantiate JS manager
		$this->js_manager = JsManager::getInstance()->setBaseUrl($this->url);

		// Register plugin stuff on the init hook
		add_action('plugins_loaded', array($this, '__register'));
	}

	public function getFile()
	{
		return $this->file;
	}

	public function getFunction()
	{
		return $this->function;
	}

	public function getUrl()
	{
		return $this->url;
	}

	public function getPath()
	{
		return $this->path;
	}

    public function CSS($hook_id = null)
    {
        return $hook_id ? $this->css_manager->getHook($hook_id) : $this->css_manager;
    }

    public function JS($hook_id = null)
    {
 		return $hook_id ? $this->js_manager->getHook($hook_id) : $this->js_manager;
    }

	public function setSettingsUrl($url)
	{
		if (is_string($url)) {
			
			add_filter("plugin_action_links_{$this->__trackable_id}", function($links) use($url) {

				$settings_link = '<a href="'. $url .'">Settings</a>'; 
				array_unshift($links, $settings_link); 
				
				return $links; 
			});
		}

		return $this;
	}

	public function onActivation($callable)
	{
		if (is_callable($callable))
			register_activation_hook($this->file, $callable);

		return $this;
	}

	public function onDeactivation($callable)
	{
		if (is_callable($callable))
			register_deactivation_hook($this->file, $callable);

		return $this;
	}

	public function onUninstallation($callable)
	{
		if (is_callable($callable))
			register_uninstall_hook($this->file, $callable);

		return $this;
	}

	public function __register()
	{
		call_user_func_array($this->function, array($this));
	}
}