<?php

namespace Ponticlaro\Bebop\AdminPage;

use Ponticlaro\Bebop;

class Tab {

	/**
	 * Tab ID
	 * 
	 * @var string
	 */
	protected $id;

	/**
	 * Tab title
	 * 
	 * @var string
	 */
	protected $title;

	/**
	 * Tab function
	 * 
	 * @var string
	 */
	protected $function;

	/**
     * Options names
     * 
     * @var Ponticlaro\Bebop\Common\Collection
     */
	protected $options;

	/**
     * Options names
     * 
     * @var Ponticlaro\Bebop\Common\Collection
     */
	protected $data;

	/**
	 * Instantiates a new tab
	 * 
	 * @param string   $title    
	 * @param callable $function
	 */
	public function __construct($title, $function)
	{
		$this->options = Bebop::Collection();
		$this->data    = Bebop::Collection();

		$this->setTitle($title);
		$this->setFunction($function);

		// Fet control elements name attribute from function
		$names = Bebop::util('getControlNamesFromCallable', $function, array($this->data));
		
		// Define options if there are control names
		if ($names)
			$this->setOptions($names);

		// Register Settings
        add_action('admin_init', array($this, '__handleSettingsRegistration'));
	}

	/**
	 * Sets tab ID
	 * 
	 * @param string $id
	 */
	public function setId($id)
	{
		if (is_string($id))
			$this->id = Bebop::util('slugify', $id, array('separator' => '-'));

		return $this;
	}

	/**
	 * Returns tab ID
	 * 
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Sets tab title
	 * 
	 * @param string $title
	 */
	public function setTitle($title)
	{
		if (is_string($title))
			$this->title = $title;

		if (!$this->id)
			$this->setId($title);

		return $this;
	}

	/**
	 * Returns tab title
	 * 
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Sets tab function
	 * 
	 * @param callable $function
	 */
	public function setFunction($function)
	{
		if (is_callable($function))
			$this->function = $function;

		return $this;
	}

	public function getFunction()
	{
		return $this->function;
	}

	/**
	 * Sets options name
	 * 
	 * @param array $options
	 */
	public function setOptions(array $options)
	{
		foreach ($options as $option) {
			
			$this->addOption($option);
		}

		return $this;
	}

	/**
	 * Adds a single option name
	 * 
	 * @param string $id
	 */
	public function addOption($option)
	{
		if (is_string($option))
			$this->options->push($option);

		return $this;
	}

	/**
	 * Returns all options
	 * 
	 * @return array
	 */
	public function getOptions()
	{
		return $this->options->getAll();
	}

    /**
     * Registers grouped settings
     * 
     * @return void
     */
    public function __handleSettingsRegistration()
    {
    	$options = $this->options->getAll();

    	if ($options) {

	        foreach ($options as $option) {
	            
	            register_setting($this->getId(), $option);
	        }
        }
    }

    /**
     * Sets data to be passed to the function
     * 
     * @return void
     */
    private function __setData()
    {
    	$options = $this->options->getAll();

        if ($options) {
            
            foreach ($options as $option) {
                
                $this->data->set($option, get_option($option));
            }
        }
    }

    /**
     * Renders tab content
     * 
     * @return void
     */
	public function render()
	{
		$this->__setData();
		call_user_func_array($this->getFunction(), array($this->data));
	}
}