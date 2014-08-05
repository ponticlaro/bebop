<?php

namespace Ponticlaro\Bebop\AdminPage;

use Ponticlaro\Bebop;

class Tab {

	protected $id;

	protected $title;

	protected $function;

	public function __construct($title, $function)
	{
		$this->setTitle($title);
		$this->setFunction($function);
	}

	public function setId($id)
	{
		if (is_string($id))
			$this->id = Bebop::util('slugify', $id, array('separator' => '-'));

		return $this;
	}

	public function getId()
	{
		return $this->id;
	}

	public function setTitle($title)
	{
		if (is_string($title))
			$this->title = $title;

		if (!$this->id)
			$this->setId($title);

		return $this;
	}

	public function getTitle()
	{
		return $this->title;
	}

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
}