<?php

namespace Ponticlaro\Bebop\UI\Plugins\ContentList;

use Ponticlaro\Bebop;

class ContentListForm {

	private $id;

	private $elements;

	public function __construct($id, array $elements = array())
	{
		if (!is_string($id))
			throw new \Exception("Form ID must be a string");
		
		$this->id = $id;
		$this->elements = Bebop::Collection($elements);
	}

	public function getId()
	{
		return $this->id;
	}

	public function addElement($id, $template)
	{
		if (!is_string($id) || !is_string($template))
			throw new \Exception("Form Element ID and Template must both be strings");

		$this->elements->set($id, $template);

		return $this;
	}

	public function addElements(array $elements)
	{
		foreach ($elements as $id => $template) {
			
			$this->addElement($id, $template);
		}

		return $this;
	}

	public function replaceElement($id, $template)
	{
		$this->addElement($id, $template);

		return $this;
	}

	public function hasElement($id)
	{
		return is_string($id) ? $this->elements->hasKey($id) : false;
	}

	public function isEmpty()
	{
		return $this->elements->count() > 0 ? false : true;
	}

	public function removeElement($id)
	{
		if (!is_string($id))
			throw new \Exception("Form Element ID must be a string");

		$this->elements->remove($id);

		return $this;
	}

	public function removeElements(array $ids)
	{
		foreach ($elements as $id => $template) {
			
			$this->removeElement($id);
		}

		return $this;
	}

	public function clearElements()
	{
		$this->elements->clear();
		
		return $this;	
	}

	public function getElement($id)
	{
		if (!is_string($id))
			throw new \Exception("Form Element ID must be a string");

		return $this->elements->get($id);
	}

	public function getAllElements()
	{
		return $this->elements->getAll();
	}
}