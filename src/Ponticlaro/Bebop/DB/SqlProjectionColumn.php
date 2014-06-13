<?php 

namespace Ponticlaro\Bebop\Db;

class SqlProjectionColumn {

	protected $table = '';

	protected $column_name;

	protected $column_alias;

	public function __construct($name, $alias = null)
	{
		$this->column_name  = $name;
		$this->column_alias = $alias;
	}

	public function setColumnName($name)
	{
		if (!is_string($name)) return $this;

		$this->column_name = $name;

		return $this;
	}

	public function setColumnAlias($alias)
	{
		if (!is_string($alias)) return $this;

		$this->column_alias = $alias;

		return $this;
	}

	public function setTable($table)
	{
		if (!is_string($table)) return $this;

		$this->table = $table;

		return $this;
	}

	public function getTable()
	{
		return $this->table;
	}

	public function getSql()
	{
		$sql = '';

		if ($this->table) $sql .= "$table.";

		$sql .= $this->column_name;

		if ($this->column_alias) $sql .= " AS $this->column_alias";

		return $sql;
	}

	public function __toString()
	{
		return $this->getSql();
	}
}