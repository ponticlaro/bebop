<?php 

namespace Ponticlaro\Bebop\Html;

interface ElementInterface
{	
	public function setTag($tag);

	public function getTag();

	public function setAttrs(array $attrs);

	public function setAttr($name, $value = null);

	public function removeAttrs(array $attrs);

	public function removeAttr($name);

	public function getAttrs();

	public function getAttr($name);

	public function append($el);

	public function prepend($el);

	public function getOpeningTag();

	public function getClosingTag();

	public function isSelfClosing();

	public function getHtml();

	public function render();

	public function __toString();
}