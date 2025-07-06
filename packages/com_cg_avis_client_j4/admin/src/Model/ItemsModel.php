<?php
/**
* CG Avis Client - Joomla Module 
* Package			: Joomla 4.x/5.x
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* From              : OT Testimonies  version 1.0, OmegaTheme Extensions - http://omegatheme.com
*/
namespace ConseilGouz\Component\CGAvisClient\Administrator\Model;
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseInterface;
use Joomla\Utilities\ArrayHelper;

class ItemsModel extends ListModel
{
	public function __construct($config = array(), ?MVCFactoryInterface $factory = null)
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 't.id',
				'category', 't.category',
				'title', 'c.title',
                'state', 't.state',
				'featured', 't.featured',
				'firstname', 't.firstname', 
				'name', 't.name', 
				'email', 't.email', 
				'zipcode', 't.zipcode', 
				'city', 't.city', 
				'comment', 't.comment', 
				'state', 't.state', 
				'ordering', 't.ordering'
			);
		}

		parent::__construct($config);
	}
	protected function getListQuery()
	{
		// Initialise variables.
		$db		= $this->getDatabase();
		$query	= $db->createQuery();

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				't.id, t.category,c.title, t.state, t.featured, t.checked_out AS checked_out, t.checked_out_time AS checked_out_time, 
				t.publish_up, t.publish_down, t.ordering, t.created  
				, t.name, t.firstname,t.email, t.zipcode, t.city, t.comment, t.state, t.ordering'
			)
		);
		$query->from('`#__cgavisclient` as t');
		$query->join('INNER','#__categories as c on c.id = t.category');
		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('t.state = '.(int) $published);
		} else if ($published === '') {
			$query->where('(t.state IN (0, 1))');
		}
		// Filter by search
		$search = $this->getState('filter.search');
		if (!empty($search))
		{							
			$searchLike = $db->Quote('%'.$db->escape($search, true).'%');
			$search = $db->Quote($db->escape($search, true));
			$query->where('(t.name = '.$search.' OR t.email = '.$search.' OR t.comment = '.$search.')');
		} //end search
		
		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');		
		$query->order($db->escape($orderCol.' '.$orderDirn));
		return $query;
	}
	public function getTable($type = 'Items', $prefix = 'cgavisclientTable', $config = array())
	{
	    $db	= Factory::getContainer()->get(DatabaseInterface::class);
	    return new \ConseilGouz\Component\CGAvisClient\Administrator\Table\ItemTable($db);
	}
	
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = Factory::getApplication('administrator');

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
							$this->setState('filter.search', $search);
		$state = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'string');
							$this->setState('filter.state', $state);
		// List state information.
		parent::populateState('t.id', 'DESC');
	}
}