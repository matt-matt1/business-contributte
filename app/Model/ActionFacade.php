<?php
namespace App\Model;

use Nette;

/*
action_id  Primary	int(11)			No	None		AUTO_INCREMENT
	2	verb	varchar(21)	utf8mb4_general_ci		No	None
*/
final class ActionFacade
{
	public function __construct(private readonly Nette\Database\Explorer $database,) {
	}
	public function getAll()
	{
		return $this->database->table('action');
	}
	public function getVerb(int $id)
	{
		return $this->getAll()->where('action_id', $id);
	}
	public function getId(string $verb)
	{
		return $this->getAll()->where('verb', $verb);
	}
	public function getActive()
	{
		return $this->database->table('action')->where('address_active', date('Y-m-d'));
	}
}