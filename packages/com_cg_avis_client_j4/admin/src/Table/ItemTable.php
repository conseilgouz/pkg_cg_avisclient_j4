<?php
/**
* CG Avis Client - Joomla Module 
* Version			: 2.0.2
* Package			: Joomla 4.x.x
* copyright 		: Copyright (C) 2021 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* From              : OT Testimonies  version 1.0, OmegaTheme Extensions - http://omegatheme.com
*/
namespace ConseilGouz\Component\CGAvisClient\Administrator\Table;
\defined('_JEXEC') or die;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Versioning\VersionableTableInterface;
use Joomla\Database\DatabaseDriver;

// No direct access.
defined('_JEXEC') or die;

class ItemTable extends Table
{
	function __construct(&$_db)
	{
		parent::__construct('#__cgavisclient', 'id', $_db);
		$this->created = Factory::getDate()->toSql();
	}

	function check()
	{
		// Check the publish down date is not earlier than publish up.
		if (intval($this->publish_down) > 0 && $this->publish_down < $this->publish_up) {
			// Swap the dates.
			$temp = $this->publish_up;
			$this->publish_up = $this->publish_down;
			$this->publish_down = $temp;
		}
		if ($this->state < 0) {
			// Set ordering to 0 if state is archived or trashed
			$this->ordering = 0;
		} else if (empty($this->ordering)) {
			// Set ordering to last if ordering was 0
			$this->ordering = self::getNextOrder(' state>=0');
		}
		return true;
	}
	function store($updateNulls = false)
	{
		$date	= Factory::getDate();
		$user	= Factory::getUser();
		if (empty($this->id))
		{
			if (!intval($this->created)) {
				$this->created = $date->toSql();
			}
			if (empty($this->created_by)) {
				$this->created_by = $user->get('id');
			}
			parent::store($updateNulls);
		}
		else
		{
			$oldrow = Table::getInstance('ItemTable','ConseilGouz\\Component\\CGAvisClient\Administrator\\Table\\', array('dbo' => $db));
			if (!$oldrow->load($this->id) && $oldrow->getError())
			{
				$this->setError($oldrow->getError());
			}
			parent::store($updateNulls);
			if ($oldrow->state>=0 && ($this->state < 0 ))
			{
				$this->reorder(' state>=0');
			}
		}
		return count($this->getErrors())==0;
	}
	public function publish($pks = null, $state = 1, $userId = 0)
	{
		$k = $this->_tbl_key;
		ArrayHelper::toInteger($pks);
		$userId = (int) $userId;
		$state  = (int) $state;
		if (empty($pks))
		{
			if ($this->$k) {
				$pks = array($this->$k);
			}
			else {
				$this->setError(JText::_('JLIB_DATABASE_ERROR_NO_ROWS_SELECTED'));
				return false;
			}
		}
		$table = Table::getInstance('ItemTable','ConseilGouz\\Component\\CGAvisClient\Administrator\\Table\\', array('dbo' => $db));
		foreach ($pks as $pk)
		{
			if(!$table->load($pk))
			{
				$this->setError($table->getError());
			}
			if($table->checked_out==0 || $table->checked_out==$userId)
			{
				$table->state = $state;
				$table->checked_out=0;
				$table->checked_out_time='0000-00-00 00:00:00'; // Joomla 4 : date nulle
				$table->check();
				if (!$table->store())
				{
					$this->setError($table->getError());
				}
			}
		}
		return count($this->getErrors())==0;
	}
}