<?php
namespace App\Model;

use Nette;

/*
journal_id  Primary	int(11)			No	None		AUTO_INCREMENT
	2	user_id  Index	int(11)			Yes	NULL
	3	business_id  Index	int(11)			Yes	NULL
	4	document_id  Index	int(11)			Yes	NULL
	5	contact_id  Index	int(11)			Yes	NULL
	6	address_id	int(11)			Yes	NULL
	7	date	datetime			No	None
	8	action_id  Index	int(11)			No	None
*/
final class JournalFacade extends CRUDFacade
{
	public const TABLE = 'journal';
	public function __construct(private Nette\Database\Explorer $database,) {
//		parent::__construct();
	}
	public function getAll()
	{
		return $this->database->table(strtolower($this::TABLE));
	}
	public function getUser(int $id)
	{
		return $this->getAll()->where('user_id', $id);
	}
	public function getBusiness(int $id)
	{
		return $this->getAll()->where('business_id', $id);
	}
	public function getDocument(int $id)
	{
		return $this->getAll()->where('ocument_id', $id);
	}
	public function getContact(int $id)
	{
		return $this->getAll()->where('contact_id', $id);
	}
	public function getAddress(int $id)
	{
		return $this->getAll()->where('address_id', $id);
	}
	public function getAction(int $id)
	{
		return $this->getAll()->where('action_id', $id);
	}
	public function getActive()
	{
		return $this->database->table(strtolower($this::TABLE))->where('address_active', date('Y-m-d'));
	}

	public function insertHere($data)
	{
		return $this->database->table(strtolower($this::TABLE))->insert($data);
//		return parent::insert($data); // TODO: Change the autogenerated stub
	}
}