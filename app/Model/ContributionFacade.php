<?php
namespace App\Model;

use Nette;

/*
contribution_id  Primary	int(11)			No	None		AUTO_INCREMENT
	2	document_id	int(11)			No	None
	3	user_id	int(11)			No	None
	4	action_id	int(11)
*/
final class ContributionFacade
{
	public function __construct(private readonly Nette\Database\Explorer $database,) {
	}
	public function getAll()
	{
		return $this->database->table('contribution');
	}
	public function getDocumentl(int $id)
	{
		return $this->getAll()->where('ocument_id', $id);
	}
	public function getUser(int $id)
	{
		return $this->getAll()->where('user_id', $id);
	}
	public function getAction(int $id)
	{
		return $this->getAll()->where('action_id', $id);
	}
	public function getActive()
	{
		return $this->database->table('contribution')->where('address_active', date('Y-m-d'));
	}
}