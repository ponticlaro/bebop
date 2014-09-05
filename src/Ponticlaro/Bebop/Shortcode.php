<?php 

namespace Ponticlaro\Bebop;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\Patterns\TrackableObjectAbstract;

class Shortcode {

    /**
     * Required trackable object type
     * 
     * @var string
     */
    protected $__trackable_type = 'shortcode';

    /**
     * Required trackable object ID
     * 
     * @var string
     */
    protected $__trackable_id;

    /**
     * Shortcode tag
     * 
     * @var string
     */
    protected $tag;

    /**
     * Shortcode function
     * 
     * @var callable
     */
    protected $function;

    /**
     * Shortcode attributes
     * 
     * @var Ponticlaro\Bebop\Common\Collection
     */
    protected $attributes;

    /**
     * Shortcode default attributes
     * 
     * @var Ponticlaro\Bebop\Common\Collection
     */
    protected $default_attributes;

    /**
     * Instantiates a new shortcode
     * 
     * @param string   $tag      Shortcode tag
     * @param callable $function Shortcode function
     */
    public function __construct($tag, $function)
    {
        if (!is_string($tag))
            throw new \Exception("Shortcode tag must be a string");

        if (!is_callable($function))
            throw new \Exception("Shortcode function must be callable");
        
        $this->__trackable_id     = $tag;  
        $this->tag                = $tag;
        $this->function           = $function;
        $this->attributes         = Bebop::Collection();
        $this->default_attributes = Bebop::Collection();

        add_shortcode($tag, array($this, '__registerShortcode'));
    }

    /**
     * Registers Shortcode and its function
     * 
     * @param  array  $attrs   Attributes from user input
     * @param  string $content Content on shortcodes with opening and closing tags
     * @param  string $tag     Shortcode tag
     * @return void
     */
    public function __registerShortcode($attrs, $content = null, $tag)
    {
        // Clear attributes
        $this->attributes->clear();

        // Set default attributes
        $this->attributes->setList($this->default_attributes->getAll());

        // Remove quotes from attributes
        if ($attrs) {

            if (is_array($attrs)) {

                foreach ($attrs as $key => $attr) {
                    $attrs[$key] = str_replace(array('\'', '"', '&#8217;', '&#8221;'), '', $attr);
                }

            } elseif(is_string($attrs)) {

                $attrs = str_replace(array('\'', '"', '&#8217;', '&#8221;'), '', $attrs);
            }

            $this->attributes->setList($attrs);
        }

        ob_start();

        // Execute shortcode function
        call_user_func_array($this->function, array($this->attributes, $content, $tag));
        
        $html = ob_get_contents();

        ob_end_clean();

        return $html;
    }

    /**
     * Sets several default atrributes
     * 
     * @param  array                      $attrs List of key/value pairs
     * @return Ponticlaro\Bebop\Shortcode        This class instance
     */
    public function setDefaultAttrs(array $attrs)
    {
        foreach ($attrs as $key => $value) {
            
            $this->setDefaultAttr($key, $value);
        }

        return $this;
    }

    /**
     * Sets a single default attribute
     * 
     * @param string                      $key   Attribute key
     * @param mixed                       $value Attribute value
     * @return Ponticlaro\Bebop\Shortcode        This class instance
     */
    public function setDefaultAttr($key, $value)
    {
        if (is_string($key))
            $this->default_attributes->set($key, $value);

        return $this;
    }
}