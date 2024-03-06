<?php
namespace App\Model;

use Nette;

/*
	address_id  Primary	int(11)			No	None		AUTO_INCREMENT
2	business_id  Index	int(11)			No	0
3	user_id  Index	int(11)			No	0
4	street_address	varchar(50)	utf8_unicode_ci		No	None
5	line2	varchar(50)	utf8_unicode_ci		No
6	city	varchar(50)	utf8_unicode_ci		No
7	province	varchar(50)	utf8_unicode_ci		No	ONTARIO	state
8	post_code	varchar(10)	utf8_unicode_ci		No
9	address_active	datetime
*/
final class AddressFacade
{
	public function __construct(private Nette\Database\Explorer $database,) {
	}
	public function getAll()
	{
		return $this->database->table('address');
	}
	public function getActive()
	{
		return $this->database->table('address')->where('address_active', date('Y-m-d'));
	}
}