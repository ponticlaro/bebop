<?php 

namespace Ponticlaro\Bebop\Html;

interface ControlElementWithOptionsInterface {

	public function setOptions(array $options);
	public function addOption($value, $label);
	public function getOptions();
	public function getOptionsHtml();
}