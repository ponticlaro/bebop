<?php 

namespace Ponticlaro\Bebop\Html;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\Html;

abstract class ControlElementWithOptions extends ControlElement implements ControlElementWithOptionsInterface {

	const SELECTED_OPTION_ATTRIBUTE = '';

	protected $options;

	public function __init()
	{
		parent::__init();

		$this->options = Bebop::Collection();

		$this->setConfig('allows_multiple_values', true);
	}

	public function setValue($value)
	{
		$this->setAttr('value', $value);

		return $this;
	}

	public function setOptions(array $options)
	{
		foreach ($options as $value => $label) {
			
			$this->addOption($value, $label);
		}

		return $this;
	}

	public function addOption($value, $label)
	{
		if (!is_string($value))
			throw new \Exception('Option value must be a string');

		if (!is_string($label))
			throw new \Exception('Option label must be a string');

		$this->options->set($value, $label);

		return $this;
	}

	public function clearOptions()
	{
		$this->options->clear();

		return $this;
	}

	public function removeOptions(array $options)
	{
		foreach ($options as $value) {
			
			$this->removeOption($value);
		}

		return $this;
	}

	public function removeOption($value)
	{
		if (!is_string($value))
			throw new \Exception('Option value must be a string');

		$this->options->remove($value);

		return $this;
	}

	public function hasOptions()
	{
		return $this->options->getAll() ? true : false;
	}

	public function hasMultipleOptions()
	{
		return count($this->options->getAll()) > 1 ? true : false;
	}

	public function getOptions()
	{
		return $this->options->getAll();
	}

	public function getOptionsElements()
	{
		$elements = array();

		foreach ($this->getOptions() as $value => $label) {

			$factory_id = $this->getFactoryId();

			$el = Html::$factory_id($this->getName())
					  ->setAttrs($this->getAttrs())
			          ->addOption($value, $label)
			          ->setLabel($label)
			          ->setValue($value);

			// If this instance have a parent, get its value to check which options are selected
			if ($this->hasParent()) {

				$parent_value = $this->getParent()->getValue();

				if (is_string($parent_value) && $value == $parent_value) {
					
					$el->setAttr(static::SELECTED_OPTION_ATTRIBUTE);
				}

				elseif (is_array($parent_value) && in_array($value, $parent_value)) {
					
					$el->setAttr(static::SELECTED_OPTION_ATTRIBUTE);
				}
			} 

			else {

				$el->setParent($this);

				if (is_string($this->getValue()) && $value == $this->getValue()) {
					
					$el->setAttr(static::SELECTED_OPTION_ATTRIBUTE);
				}

				elseif (is_array($this->getValue()) && in_array($value, $this->getValue())) {
					
					$el->setAttr(static::SELECTED_OPTION_ATTRIBUTE);
				}
			}

			$elements[] = $el;
		}

		return $elements;
	}

	public function getOptionsHtml()
	{
		$html = '';

		foreach ($this->getOptionsElements() as $el) {

			$html .= $el->getSingleElementHtml();
		}

		return $html;
	}

	public function getSingleElementHtml()
	{
		return $this->getOpeningTag() . $this->getClosingTag();
	}

	public function getHtml()
	{
		return $this->getOptionsHtml();
	}
}