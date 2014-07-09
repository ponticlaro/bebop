<?php 

namespace Ponticlaro\Bebop\Db;

use Ponticlaro\Bebop;
use Ponticlaro\Bebop\Db;
use Ponticlaro\Bebop\Db\Query\Arg;
use Ponticlaro\Bebop\Db\Query\ArgFactory;

class Query {

	private $query_results = array();

	private $query_meta;

	private $args;

	private $current_arg;

	public function __construct()
	{
		$this->args = Bebop::Collection();
	}

	public function __call($name, $args)
	{
		$name = strtolower($name);

		// Check current arg for method
		if (!is_null($this->current_arg) && method_exists($this->current_arg, $name)) {
			
			call_user_method_array($name, $this->current_arg, $args);
		}

		// Check if current arg is a parent and if its current child have the method
		elseif (!is_null($this->current_arg) && 
			    $this->current_arg->isParent() && 
			    method_exists($this->current_arg->getCurrentChild(), $name)) {

			call_user_method_array($name, $this->current_arg->getCurrentChild(), $args);
		}

		// Check if we should add a new child to an already existing parent arg
		elseif (!is_null($this->current_arg) && 
			    $this->current_arg->isParent() &&
			    ArgFactory::getInstanceId($this->current_arg) == $name) {

			call_user_method_array('addChild', $this->current_arg, $args);
		}

		// Check if a parent arg is already instantiated for the target method $name
		elseif ($this->args->hasKey($name)) {

			$arg = $this->args->get($name);

			call_user_method_array('addChild', $arg, $args);

			$this->current_arg = $arg;
		}

		// Check for manufacturable argument class
		elseif (ArgFactory::canManufacture($name)) {

			// Save current arg if any
			if (!is_null($this->current_arg)) {

				// Store unique arg with a key
				if ($this->current_arg->isParent()) {
					
					$this->args->set(ArgFactory::getInstanceId($this->current_arg), $this->current_arg);
				}

				// Push non-unique arg to args collection
				else {

					$this->args->push($this->current_arg);
				}
			}

			// Create new arg
			$arg = ArgFactory::create($name, $args);

			// If it is a parent arg, save it immediatelly
			if ($arg->isParent())
				$this->args->set($name, $this->current_arg);

			// Store new arg as current
			$this->current_arg = $arg;
		}

		return $this;
	}

	public function metaKey($key)
	{
		if (is_string($key)) {
			
			$arg = new Arg();
			$arg->setKey('meta_key')->setValue($key);

			$this->args->push($arg);
		}

		return $this;
	}

	public function metaValue($value)
	{	
		if ($value) {

			$arg = new Arg();
			$arg->setKey('meta_value')->setValue($value);

			$this->args->push($arg);
		}

		return $this;
	}

	public function page($page)
	{
		if (is_numeric($page)) {

			$arg = new Arg();
			$arg->setKey('page')->setValue($page);

			$this->args->push($arg);
		}

		return $this;
	}

	public function limit($limit)
	{
		if (is_numeric($limit)) {

			$arg = new Arg();
			$arg->setKey('posts_per_page')->setValue($limit);

			$this->args->push($arg);
		}

		return $this;
	}

	public function offset($offset)
	{
		if (is_numeric($offset)) {

			$arg = new Arg();
			$arg->setKey('offset')->setValue($offset);

			$this->args->push($arg);
		}

		return $this;
	}

	public function order($by, $direction = 'DESC')
	{
		if (is_string($by) && is_string($direction)) {

			$arg = new Arg();
			$arg->setKey('orderby')->setValue($by);

			$this->args->push($arg);

			$arg = new Arg();
			$arg->setKey('order')->setValue($direction);

			$this->args->push($arg);
		}

		return $this;
	}

	public function ignoreSticky($ignore)
	{
		if (is_bool($ignore)) {

			$arg = new Arg();
			$arg->setKey('ignore_sticky_posts')->setValue($ignore);

			$this->args->push($arg);
		}

		return $this;
	}

	public function getArgs()
	{	
		$args = array();

		// Save current arg if any
		if (!is_null($this->current_arg)) {

			if ($this->current_arg->isParent()) {
				
				$this->args->set(ArgFactory::getInstanceId($this->current_arg), $this->current_arg);
			}

			else{

				$this->args->push($this->current_arg);
			}
			
			$this->current_arg = null;
		}

		foreach ($this->args->get() as $arg) {

			if ($arg->getValue())
				$args[$arg->getKey()] = $arg->getValue();
		}

		return $args;
	}

	public function getMeta()
	{
		return $this->query_meta;
	}

	public function findAll()
	{
		$data = Db::queryPosts($this->getArgs(), array('with_meta' => true));

		if (isset($data['items']))
			$this->query_results = $data['items'];
		
		if (isset($data['meta']))
			$this->query_meta = $data['meta'];

		return $this->query_results;
	}
}