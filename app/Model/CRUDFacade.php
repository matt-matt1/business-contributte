<?php

namespace App\Model;

use Nette;

abstract class CRUDFacade
{
	const TABLE = 'undefined';

	public function __construct(
		private Nette\Database\Explorer $database,
	)
	{
//		$this->database = $database;
	}
	public function insert(iterable $data)
	{
		return $this->database->table(strtolower($this::TABLE))->insert($data);
	}
//	public function insertObject($data)
//	{
//		$this->database->table(strtolower($this::TABLE))->insert($data);
//	}
	public function update(iterable $data)
	{
		$this->database->table(strtolower($this::TABLE))->update($data);
	}

	/**
	 * Returns the description (of fields/columns) for a specific database table
	 * ie.
	 * | Field        | Type         | Null | Key | Default             | Extra          |
	 * (or if $detailed is true:)
	 * | Field        | Type         | Collation          | Null | Key | Default             | Extra          | Privileges                      | Comment |
	 * @param string|null $table - name of database table - default is the calling class's table
	 * @param bool $detailed - whether to show a full description - default is false (standard description)
	 * @return Nette\Database\ResultSet
	 */
	public function getTableInfo(string $table=null, bool $detailed=false)
	{
		if (!isset($table))
//			$table = $this::TABLE;
//			$table = self::TABLE;
			$table = static::TABLE;
		$sql = sprintf('SHOW '. ($detailed ? 'FULL ': ''). 'COLUMNS FROM %s', strtolower($table));
//		return explode(' ', $sql);
		return $this->database->query($sql);
//		return $this->database->query('SHOW '. ($detailed ? 'FULL ': ''). 'COLUMNS FROM ?', $table);
	}
	public function getFields(string $table=null)
	{
		if (!isset($table))
			$table = $this::TABLE;
		$results = array();
//		$info = $this->getTableInfo($table);
//		foreach ($info as $v) {
//		foreach ($this->getTableInfo($table) as $v) {
		foreach (static::getTableInfo(strtolower($table)) as $v) {
			if (isset($v['Field']))
				$results[$v['Field']] = $v;
		}
		return $results;
	}
}