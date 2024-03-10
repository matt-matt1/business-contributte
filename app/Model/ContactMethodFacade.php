<?php
namespace App\Model;

use Nette;

/*
type_id  Primary	int(11)			No	None		AUTO_INCREMENT
	2	name	varchar(50)	utf8_unicode_ci		No	None
*/
final class ContactMethodFacade
{
	public function __construct(private readonly Nette\Database\Explorer $database,) {
	}
	public function getAll()
	{
		return $this->database->table('contact_method');
	}
	public function getName(int $id)
	{
		return $this->getAll()->where('type_id', $id);
	}
	public function getId(string $name)
	{
		return $this->getAll()->where('name', $name);
	}
	public function getActive()
	{
		return $this->database->table('contact_method')->where('address_active', date('Y-m-d'));
	}
}