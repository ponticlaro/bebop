<?php 

namespace Ponticlaro\Bebop\Db;

use \Ponticlaro\Bebop;

class SqlProjection {
	
	/**
	 * Projection columns data
	 * 
	 * @var \Ponticlaro\Bebop\Common\Collection
	 */
	protected $columns;

	/**
	 * Optional table name or alias
	 * 
	 * @var string
	 */
	protected $table;

	/**
	 * Projection class
	 * 
	 * @var string
	 */
	protected $class;

	public function __construct($table = '')
	{
		$this->table   = $table;
		$this->columns = Bebop::Collection();
	}

	public function addColumn($name, $alias = null, $table = null)
	{
		$column = new SqlProjectionColumn($name, $alias);

		$table = $table ?: $this->table;

		if ($table) $column->setTable($table);

		$this->columns->push($column);

		return $this;
	}

	public function setClass($class)
	{
		if (!is_string($class) || !class_exists($class)) return $this;

		$this->class = $class;

		return $this;
	}

	public function getClass()
	{
		return $this->class;
	}

	public function hasClass()
	{
		return isset($this->class) ? true : false;
	}

	public function getSql()
	{
		$columns = $this->columns->get();

		if (!$columns) return $this->table ? "$this->table.*" : '*';

		$sql     = '';
		$counter = 0;

		foreach ($columns as $column) {
			
			$counter++;

			if ($counter !== 1) $sql .= ",";

			$sql .= $column->getSql();
		}

		return $sql;
	}

	public function __toString()
	{
		return $this->getSql();
	}
}