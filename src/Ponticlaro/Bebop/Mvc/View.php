<?php

namespace Ponticlaro\Bebop\Mvc;

use Ponticlaro\Bebop;

class View {

    /**
     * The base path for all views
     * 
     * @var string
     */
    protected static $views_dir;

    /**
     * The path to the template file for this view
     * 
     * @var string
     */
    protected $template;

    /**
     * Collection of all vars
     * 
     * @var Ponticlaro\Bebop\Common\Collection
     */
    protected $vars;

    /**
     * Intantiates a new view
     * 
     */
    public function __construct()
    {
        $this->vars = Bebop::Collection();
    }

    /**
     * Sets views directory for all views
     * 
     * @param [type] $path [description]
     */
    public static function setViewsDir($path)
    {
        if (is_string($path) && is_readable($path)) 
            self::$views_dir = rtrim($path, '/');
    }

    /**
     * Returns template relative path
     * 
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Sets template relative path
     * 
     * @param string $template Template relative path, without file extension
     */
    public function setTemplate($template)
    {
        if(is_string($template)) $this->template = $template;

        return $this;
    }

    /**
     * Returns all vars
     * 
     * @return array
     */
    public function getAllVars()
    {
        return $this->vars->getAll();
    }

    /**
     * Get the value for a single var key
     * 
     * @param  string $key Target var key
     * @return mixed       The value for the target var key
     */
    public function getVar($key)
    {
        return $this->vars->get($key);
    }

    /**
     * Cheks if the target var key exist in the vars collection
     * 
     * @param  string  $key Target var key
     * @return boolean      True if it exists, false otherwise
     */
    public function hasVar($key)
    {
        return $this->vars->hasKey($key);
    }

    /**
     * Checks if the target var key have a value
     * 
     * @param  string  $key Target var key
     * @return boolean      True if there is a value, false otherwise
     */
    public function varHasValue($key)
    {
        return $this->vars->hasKey($key) && !empty($this->vars->get($key)) ? true : false;
    }

    /**
     * Sets the value for a single var key
     * 
     * @param string $key   Target var key
     * @param string $value Target var value
     */
    public function setVar($key, $value)
    {
        $this->vars->set($key, $value);

        return $this;
    }

    /**
     * Sets an array of key/values pairs on the vars collection
     * 
     * @param array   $vars  List of vars to be set
     * @param boolean $merge True if these vars should merge with existing ones, false otherwise
     */
    public function setVars(array $vars = array(), $merge = true)
    {   
        if ($merge) {

            // Get current vars
            $current_vars = $this->vars->getAll();

            // Merge with new vars
            $vars = array_merge($current_vars, $vars);
        }

        $this->vars->set($vars);

        return $this;
    }

    /**
     * Replaces all existing vars
     * 
     * @param  array  $vars List of vars to be set
     */
    public function replaceVars(array $vars = array())
    {   
        $this->vars->clear()->set($vars);

        return $this;
    }

    /**
     * Renders a partial view
     * 
     * @param  string $template Relative path of the partial view template
     * @param  array  $vars     Vars that will be passed to the partial rendering
     */
    public function partial($template, array $vars = array())
    {
        if (is_string($template)) {
            
            foreach ($vars as $key => $value) {
                ${$key} = $value;
            }

            include $this->__getTemplatePath($template);
        }

        return $this;
    }

    /**
     * Renders this view
     * 
     * @param  string  $template Template relative path, without file extension
     * @param  array   $vars     List of vars for rendering
     * @param  boolean $merge    True if these vars should merge with existing ones, false otherwise
     * @return void
     */
    public function render($template = null, array $vars = array(), $merge = true)
    {
        // Throw error if we neither already have a template 
        // or one is not provided in this method
        if (is_null($this->template) && (!$template || !is_string($template))) {

            throw new \Exception('You must defined a template to be rendered');
        }

        // If we already have $this->template
        // and the $template variable is an array
        elseif ($this->template && is_array($template)) {
            
            $vars  = $template;
            $merge = is_bool($vars) ? $vars : true;
        }

        // Set template and variables
        $this->setTemplate($template);
        $this->setVars($vars, $merge);

        // Expose variables to template
        foreach ($this->vars->getAll() as $key => $value) {
            ${$key} = $value;
        }

        // Include template
        include $this->__getTemplatePath($this->getTemplate());
    }

    /**
     * Gets the template paths base on:
     * - the base directory for views
     * - the relative path of the template
     * 
     * @param  string $template Template relative path, without file extension
     * @return string           Full path to the file
     */
    private function __getTemplatePath($template)
    {
        // Get file path
        $file_path = self::$views_dir ? self::$views_dir .'/'. $template .'.php' : str_replace('.php', '', $template) .'.php';

        // Throw error if file do not exist
        if (!is_file($file_path))
            throw new \Exception("Template '$template' do not exist or is not readable in the following location: $file_path");

        return $file_path;
    }
}