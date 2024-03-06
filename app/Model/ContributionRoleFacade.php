<?php
namespace App\Model;

use Nette;

/*
contributor_id  Primary	int(11)			No	None		AUTO_INCREMENT
	2	contributor_role	varchar(50)	utf8_unicode_ci		No	None
*/
final class ContributionRoleFacade
{
	public function __construct(private Nette\Database\Explorer $database,) {
	}
	public function getAll()
	{
		return $this->database->table('contribution_role');
	}
	public function getRole(int $id)
	{
		return $this->getAll()->where('contributor_id', $id);
	}
	public function getId(string $role)
	{
		return $this->getAll()->where('contributor_role', $role);
	}
	public function getActive()
	{
		return $this->database->table('contribution_role')->where('address_active', date('Y-m-d'));
	}
}