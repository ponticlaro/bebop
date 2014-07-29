<?php 

namespace Ponticlaro\Bebop\Html\Elements;

use Ponticlaro\Bebop;

class Select extends \Ponticlaro\Bebop\Html\ControlElementWithOptions {

	const SELECTED_OPTION_ATTRIBUTE = 'selected';

	protected $option_groups;

	public function __construct($name = null, array $options = array())
	{
		$this->__init();

		$this->setTag('select');

		if (!is_null($name))
			$this->setName($name);

		if ($options)
			$this->setOptions($options);
	}

	public function allowsMultipleValues()
	{
		return $this->getConfig('allows_multiple_values') && $this->getAttr('multiple') ? true : false;
	}

	public function getOptionsHtml()
	{
		$html = '';

		foreach ($this->getOptions() as $value => $label) {
			
			$current_value = $this->getValue();
			$is_current    = false;

			if (is_string($current_value) && $current_value == $value || 
				is_array($current_value) && in_array($value, $current_value)) {
				
				$is_current = true;
			}

			$html .= '<option';
			$html .= $is_current ? ' selected' : '';
			$html .= ' value="'. $value .'">'. $label .'</option>';
		}

		return $html;
	}

	public function getHtml()
	{
		return $this->getOpeningTag() . $this->getOptionsHtml() . $this->getClosingTag();
	}
}