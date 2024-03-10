<?php
namespace App\Model;

use Nette;

/*
document_id  Primary	int(11)			No	None		AUTO_INCREMENT
	2	document_name	varchar(100)	utf8mb4_general_ci		No
	3	document_path	varchar(100)	utf8mb4_general_ci		No
	4	document_host	varchar(100)	utf8mb4_general_ci		No
	5	document_url	varchar(255)	utf8mb4_general_ci		No
	6	document_note	text	utf8mb4_general_ci		No	''
	7	document_active	datetime*/
final class DocumentFacade
{
	public function __construct(private readonly Nette\Database\Explorer $database,) {
	}
	public function getAll()
	{
		return $this->database->table('document');
	}
	public function getActive()
	{
		return $this->database->table('document')->where('address_active', date('Y-m-d'));
	}
}