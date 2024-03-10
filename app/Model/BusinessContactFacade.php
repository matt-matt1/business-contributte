<?php
namespace App\Model;

use Nette;

/*
 * contact_id  Primary	int(11)			No	None		AUTO_INCREMENT
2	business_id	int(11)			Yes	NULL
3	user_id	int(11)			Yes	NULL
4	contact_first	varchar(100)	utf8_unicode_ci		No	None
5	contact_last	varchar(100)	utf8_unicode_ci		No	None
6	contact_type_id  Index	int(11)			No	None
7	contact_number	varchar(50)
 */
final class BusinessContactFacade
{
	public function __construct(private readonly Nette\Database\Explorer $database,) {
//		parent::__construct();
	}
	public function getAll()
	{
		return $this->database->table('contact');
	}
	public function getActive()
	{
		return $this->database->table('contact')->where('contact_active', date('Y-m-d'));
	}
/*	public function allText()
	{
		return $this->database->table('contact')->fetch()
	}*/
}