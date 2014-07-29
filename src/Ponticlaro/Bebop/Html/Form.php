<?php 

namespace Ponticlaro\Bebop\Html;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\Html;

class Form {

	protected $el;

	protected $children;

	protected $data;

	public function __construct()
	{
		$this->el       = Html::form();
		$this->children = Bebop::Collection();
		$this->data     = Bebop::Collection();
	}

	public static function create()
	{
		return new self;
	}

	public function replaceData(array $data)
	{
		$this->data->clear()->set($data);

		return $this;
	}

	public function addData(array $data)
	{
		$this->data->set($data);

		return $this;
	}

	public function getData()
	{
		return $this->data->get();
	}

	public function getDataObject()
	{
		return $this->data;
	}

	public function render()
	{	
		echo $this->el->getOpeningTag();

		foreach ($this->children->getAll() as $child) {

			$name_attr = $child->getAttr('name');

			if ($name_attr) {

				$value = $this->data->get($name_attr);
				
				if ($value) {

					if (!$child->allowsMultipleValues() && is_array($value)) {
						
						$child->setValue($value[0]);
					}

					else {

						$child->setValue($value);
					}
				}
			}
			
			$child->render();
		}

		echo $this->el->getClosingTag();
	}

	public function __call($name, $args)
	{
		$el = call_user_func_array(array('Ponticlaro\Bebop\Html', $name), $args);

		$this->children->push($el);

		return $this;
	} 
}