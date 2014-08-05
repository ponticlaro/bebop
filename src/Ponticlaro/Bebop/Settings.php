<?php 

namespace Ponticlaro\Bebop;

use Ponticlaro\Bebop;

class Setting {

    /**
     * Required trackable object type
     * 
     * @var string
     */
    protected $__trackable_type = 'setting';

    /**
     * Required trackable object ID
     * 
     * @var string
     */
    protected $__trackable_id;

    /**
     * List of options to be saved
     * 
     * @var Ponticlaro\Bebop\Common\Collection
     */
    protected $options;

    /**
     * List of sections
     * 
     * @var Ponticlaro\Bebop\Common\Collection
     */
    protected $sections;

    /**
     * Instantiates 
     * 
     * @param [type] $group_name [description]
     */
    public function __construct($id)
    {
    	$this->options  = Bebop::Collection();
    	$this->sections = Bebop::Collection();

    	$this->setId($id);
    }

    public function setId($id)
    {
    	if (is_string($id))
    		$this->__trackable_id = $id;

    	return $this;
    }

    public function getId()
    {
   		return $this->__trackable_id;
    }

    public function addOptions(array $options)
    {
    	foreach ($options as $option) {
    		
    		$this->addOption($option);
    	}

    	return $this;
    }

    public function addOption($option)
    {
    	if (is_string($option))
    		$this->options->push($option);

    	return $this;
    }

    public function removeOptions(array $options)
    {
    	foreach ($options as $option) {
    		
    		$this->removeOption($option);
    	}

    	return $this;
    }

    public function removeOption($option)
    {
    	if (is_string($option))
    		$this->options->pop($option);

    	return $this;
    }

    public function getOptions()
    {
    	return $this->options->getAll();
    }

	public function addSection()
	{
		add_settings_section();
	}

	public function addField()
	{
		add_settings_field();
	}

	public function addErrors(array $errors)
	{
		foreach ($errors as $error) {
			
			$this->addError();
		}

		return $this;
	}

	public function addError()
	{
		add_settings_error();

		return $this;
	}

	public function renderFormFields()
	{
		settings_fields();

		return $this;
	}

	public function renderErrors()
	{
		settings_errors();

		return $this;
	}

	public function renderSections()
	{
		do_settings_sections();

		return $this;
	}

	public function renderFields()
	{
		do_settings_sections();

		return $this;
	}

	public function register()
	{
		register_setting();

		return $this;
	}

	public function unregister()
	{
		unregister_setting();

		return $this;
	}
}