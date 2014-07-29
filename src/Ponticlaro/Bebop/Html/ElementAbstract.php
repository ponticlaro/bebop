<?php 

namespace Ponticlaro\Bebop\Html;

use \Ponticlaro\Bebop;
use \Ponticlaro\Bebop\Html\HtmlFactory;

abstract class ElementAbstract implements ElementInterface
{	
	/**
	 * Contains element tag
	 * 
	 * @var string
	 */
	protected $tag;

    /**
     * Attributes for this element 
     * 
     * @var Ponticlaro\Bebop\Common\Collection
     */
	protected $attributes;

    /**
     * Element children
     * 
     * @var Ponticlaro\Bebop\Common\Collection
     */
	protected $children;

    /**
     * Configuration for this element 
     * 
     * @var Ponticlaro\Bebop\Common\Collection
     */
	protected $config;

	/**
	 * Contains parent element
	 * 
	 * @var Ponticlaro\Bebop\Html\ElementAbstract
	 */
	protected $parent;

	/**
	 * Contains label for the element
	 * 
	 * @var string
	 */
	protected $label;

	/**
	 * Instantiates new HTML element object
	 *
	 * @param string $tag HTML element tag
	 */
	public function __construct($tag = null)
	{	
		$this->__init();

		// Save element tag
		$this->setTag(is_null($tag) ? 'div' : $tag);
	}

	protected function __init()
	{
		// Instantiate configuration object
		$this->config = Bebop::Collection(array(
			'self_closing'           => false,
			'allows_multiple_values' => false
		));

		// Instantiate attributes object
		$this->attributes = Bebop::Collection();

		// Instantiate children object
		$this->children = Bebop::Collection();
	}

	public function getFactoryId()
	{
		return HtmlFactory::getInstanceId($this);
	}

	public function setParent(ElementAbstract $el)
	{
		$this->parent = $el;
	}

	public function hasParent()
	{
		return is_null($this->parent) ? false : true;
	}

	public function getParent()
	{
		return $this->parent;
	}

	public function setLabel($label)
	{
		if (is_string($label))
			$this->label = $label;

		return $this;
	}

	public function hasLabel()
	{
		return is_null($this->label) ? false : true;
	}

	public function getLabel()
	{
		return $this->label;
	}

	public function setConfigs(array $config)
	{
		foreach ($config as $key => $value) {
			
			$this->setConfig($key, $value);
		}

		return $this;
	}

	public function setConfig($key, $value)
	{
		if (!is_string($key))
			throw new \Exception('Config key name must be a string');

		$this->config->set($key, $value);

		return $this;
	}

	public function getConfig($key)
	{
		return $this->config->get($key);
	}

	public function setTag($tag)
	{
		if (!is_string($tag))
			throw new \Exception('Element tag must be a string');

		$this->tag = Bebop::util('slugify', $tag);

		return $this;
	}

	public function getTag()
	{
		return $this->tag;
	}

	public function setId($value)
	{	
		if (is_string($value))
			$this->attributes->set('id', $value);

		return $this;
	}

	public function getId()
	{
		return $this->attributes->get('id');
	}

	public function setClasses($classes)
	{	
		if (is_string($classes))
			$this->attributes->set('class', trim($classes));

		return $this;
	}

	public function addClass($class)
	{	
		if (is_string($class)) {

			$current_classes = $this->attributes->get('class');

			$this->attributes->set('class', $current_classes .' '. trim($class));
		}

		return $this;
	}

	public function removeClass($class)
	{
		if (is_string($class)) {

			$current_str     = $this->attributes->get('class');
			$current_classes = explode(' ', $current_str);
			$class_key       = array_search(trim($class), $current_classes);

			if ($class_key !== false)
				unset($current_classes[$class_key]);

			$this->attributes->set('class', implode(' ', $current_classes));
		}

		return $this;
	}

	public function getClass()
	{
		return $this->attributes->get('class');
	}

	public function setAttrs(array $attrs)
	{
		foreach ($attrs as $name => $value) {
			
			$this->setAttr($name, $value);
		}

		return $this;
	}

	public function setAttr($name, $value = null)
	{
		if (!is_string($name))
			throw new \Exception('Element attribute name must be a string');

		$this->attributes->set($name, is_null($value) ? $name : $value);

		return $this;
	}

	public function removeAttrs(array $attrs)
	{
		foreach ($attrs as $name) {
			
			$this->removeAttr($name);
		}

		return $this;
	}

	public function removeAttr($name)
	{
		if (is_string($name))
			$this->attributes->remove($name);

		return $this;
	}

	public function getAttrs()
	{
		return $this->attributes->getAll();
	}

	public function getAttr($name)
	{
		return $this->attributes->get($name);
	}

	public function setName($name)
	{
		if (is_string($name))
			$this->attributes->set('name', Bebop::util('slugify', $name));

		return $this;
	}

	public function getName()
	{
		return $this->attributes->get('name');
	}

	public function setValue($value)
	{
		if (is_string($value) || is_integer($value) || is_bool($value)) {
			
			$this->attributes->set('value', $value);
		}

		return $this;
	}

	public function getValue()
	{
		return $this->attributes->get('value');
	}

	public function allowsMultipleValues()
	{
		return $this->getConfig('allows_multiple_values');
	}

	public function prepend($el)
	{
		if (!is_string($el) && !$el instanceof ElementAbstract)
			throw new \Exception('Element children must be either strings of children of \Ponticlaro\Bebop\Html\ElementAbstract.');

		$this->children->unshift($el);

		return $this;
	}

	public function append($el)
	{
		if (!is_string($el) && !$el instanceof ElementAbstract)
			throw new \Exception('Element children must be either strings of children of \Ponticlaro\Bebop\Html\ElementAbstract.');

		$this->children->push($el);

		return $this;
	}

	public function getChildren()
	{
		return $this->children->getAll();
	}

	public function isSelfClosing()
	{
		return $this->config->get('self_closing');
	}

	public function getOpeningTag()
	{
		return '<'. $this->tag . $this->getAttrsHtml() . ($this->isSelfClosing() ? '' : '>');
	}

	public function getClosingTag()
	{
		return $this->isSelfClosing() ? '/>' : '</'. $this->tag . '>';
	}

	public function getAttrsHtml()
	{
		$html       = '';
		$attributes = $this->attributes->get();

		foreach ($attributes as $key => $value) {

			if ($key == 'name' && $this->allowsMultipleValues()) {
				
				$value = $value . '[]';
			}

			$html .= ' ' . $key . '="' . $value . '"';
		}

		return $html;
	}

	public function getChildrenHtml()
	{ 
		$html = '';

		if (!$this->isSelfClosing()) {

			foreach ($this->children->get() as $child) {
				
				if (is_string($child)) {
					
					$html .= $child;
				}

				else {

					$html .= $child->getHtml();
				}
			}
		}

		return $html;
	}

	public function getHtml()
	{
		return $this->getOpeningTag() . $this->getChildrenHtml() . $this->getClosingTag();
	}

	public function render()
	{
		echo $this->getHtml();
	}

	public function __toString()
	{
		return $this->getHtml();
	}
}