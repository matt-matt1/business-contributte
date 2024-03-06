<?php
namespace App\Model;

use Nette;
use Nette\ComponentModel\IComponent;
use Nette\ComponentModel\IContainer;
use Ublaboo\DataGrid\DataGrid;

/*
 * business_id  Primary	int(11)			No	None		AUTO_INCREMENT
2	business_name	varchar(50)	utf8_unicode_ci		No	None
3	business_email	varchar(50)	utf8_unicode_ci		No	None
4	business_note	text	utf8_unicode_ci		No	None
5	business_isactive	tinyint(4)			No	0	1=yes
6	business_source	varchar(100)	utf8_unicode_ci		No	None
7	business_website	varchar(50)	utf8_unicode_ci		No	None
8	business_active	datetime
 */
final class BusinessFacade extends CRUDFacade /*implements \Nette\ComponentModel\IContainer*/
{
	const TABLE = 'business';

	public function __construct(
		private Nette\Database\Explorer $database,
		private JournalFacade $jnl,
	) {
//		parent::__construct();
	}
	public function getAll()
	{
		return $this->database->table(strtolower($this::TABLE));
	}
	public function getActive()
	{
		return $this->database->table(strtolower($this::TABLE))->where('business_active', date('Y-m-d'));
	}

	/**
	 * @return string|null
	 */
/*	function getName(): ?string
	{
		// TODO: Implement getName() method.
	}
*/
	/**
	 * @return IContainer|null
	 */
/*	function getParent(): ?IContainer
	{
		// TODO: Implement getParent() method.
	}
*/
	/**
	 * @param IContainer|null $parent
	 * @param string|null $name
	 * @return $this
	 */
/*	function setParent(?IContainer $parent, ?string $name = null)
	{
		// TODO: Implement setParent() method.
	}
*/
	/**
	 * @param IComponent $component
	 * @param string|null $name
	 * @return $this
	 */
/*	function addComponent(IComponent $component, ?string $name)
	{
		// TODO: Implement addComponent() method.
	}
*/
	/**
	 * @param IComponent $component
	 * @return void
	 */
/*	function removeComponent(IComponent $component): void
	{
		// TODO: Implement removeComponent() method.
	}
*/
	/**
	 * @param string $name
	 * @return IComponent|null
	 */
/*	function getComponent(string $name): ?IComponent
	{
		// TODO: Implement getComponent() method.
	}
*/
	/**
	 * @return \Iterator
	 */
/*	function getComponents(): \Iterator
	{
		// TODO: Implement getComponents() method.
	}
*/
	/**
	 * Inserts a new business (using the data from an object) and returns the business_id
	 * @param $data
	 * @return mixed
	 * @throws Nette\Neon\Exception
	 */
	public function insertObject($data)
	{
//		if (!property_exists($data, 'requestToken'))
//			throw new Nette\Neon\Exception(_('Form has wrong token (in BusinessFacade::insertObject)'));
//		throw new Nette\Neon\Exception(sprintf(_('Form: %s'), print_r($data, true)));
		if (is_object($data)) {
			$array = array();
			$unknown = array();
			foreach ($data as $k => $v) {
				switch ($k) {
					case 'requestToken':
						break;
					case 'name':
					case 'business_name':
						$array['business_name'] = $data->{$k};
						break;
					case 'email':
					case 'business_email':
						$array['business_email'] = $data->{$k};
						break;
					case 'note':
					case 'business_note':
						$array['business_note'] = $data->{$k};
						break;
					case 'source':
					case 'business_source':
						$array['business_source'] = $data->{$k};
						break;
					case 'active':
					case 'business_active':
						$array['business_active'] = $data->{$k};
						break;
					case 'website':
					case 'business_website':
						$array['business_website'] = $data->{$k};
						break;
					default:
						$unknown[] = $k;
				}
			}
			if (!empty($unknown))
				throw new Nette\Neon\Exception(sprintf(_('Cannot place "%s" in BusinessFacade::insertObject'),
					implode(',', $unknown)));
		} else
			$array = $data;
		if (!isset($array['business_active']))
			$array['business_active'] = date('Y-m-d H:i:s');
		$this->database->table('business')->insert($array);

/*		$qry = sprintf('SELECT business_id FROM %s WHERE ? = ?', strtolower($this::TABLE));
		$subs = array();
		foreach ($array as $k => $v) {
			if ($k !== array_key_first($array)) {
				$qry .= ' AND ? = ?';
			}
			$subs[] = $k;
			$subs[] = $v;
		}
		$rec = $this->database->query($qry, $subs);
		$id = $rec['business_id'];*/
//		$id = $this->database->table(strtolower($this::TABLE))->select('business_id')->where($array)->fetchAll();
//		throw new Nette\Neon\Exception(sprintf(_('Form: %s'), print_r($data, true)));
		//$row = $this->getAll()->where($array)->getSql();
//		$row = $this->getAll()->where($array)->fetchAll();
		$id = $this->getAll()->where($array)->fetchAll();
//		throw new Nette\Neon\Exception(sprintf(_('Form: %s'), implode(',', $row)));
//		$id = $row['business_id'];
//		$rec = $this->database->table(strtolower($this::TABLE))->where($array)->fetchAll();
//		throw new Nette\Neon\Exception(sprintf(_('Added "%s" in BusinessFacade::insertObject'), $id));

		$jnlData = array();
		if (is_object($data))
			$business_id = (property_exists($data, 'business_id')) ? $data->business_id : $id;
		else
			$business_id = isset($data['business_id']) ? $data['business_id'] : $id;
		$jnlData['business_id'] = $business_id;
		// if a record already exists for this user then add the record as 'edited', otherwise as 'createed'
		$action = count($this->jnl->getAll()->where('business_id', $business_id)) > 1 ? 2 : 3;
		// add the record for now - this time
		$jnlData['date'] = date('Y-m-d H:i:s');
		if (is_object($data) && property_exists($data, 'action_id'))
			$jnlData['action_id'] = $data->action_id;
		elseif (is_array($data) && isset($data['action_id']))
			$jnlData['action_id'] = $data['action_id'];
		else
			$jnlData['action_id'] = $action;
//		$this->jnl->insert($jnlData);
		$obj = new \stdClass();
//		$obj = new \ArrayIterator();
		foreach ($jnlData as $k => $v)
			$obj->{$k} = $v;
		$this->jnl->insertHere($obj);
	}
}