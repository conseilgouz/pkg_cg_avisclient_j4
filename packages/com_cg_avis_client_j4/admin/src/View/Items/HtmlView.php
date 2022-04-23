<?php
/**
* CG Avis Client - Joomla Module 
* Version			: 2.0.2
* Package			: Joomla 4.x.x
* copyright 		: Copyright (C) 2021 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* From              : OT Testimonies  version 1.0, OmegaTheme Extensions - http://omegatheme.com
*/
namespace ConseilGouz\Component\CGAvisClient\Administrator\View\Items;
// No direct access
defined('_JEXEC') or die;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
	protected $categories;
	protected $items;
	protected $pagination;
	protected $state;
	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// Initialise variables.
		$this->categories	= $this->get('CategoryOrders');
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		
		$this->addToolbar();

		parent::display($tpl);
	}

	protected function addToolbar()
	{
		$canDo = ContentHelper::getActions('com_cgavisclient');
		$user		= Factory::getUser();
		ToolBarHelper::title(Text::_('COM_CGAVISCLIENT_MANAGER_ITEMS'), 'item.png');

		if ($canDo->get('core.create') || (count($user->getAuthorisedCategories('com_cgavisclient', 'core.create'))) > 0 ) {
			ToolBarHelper::addNew('item.add');
		}

		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own'))) {
			ToolBarHelper::editList('item.edit');
		}

		if ($canDo->get('core.edit.state')) {
			ToolBarHelper::divider();
			ToolBarHelper::publish('items.publish', 'JTOOLBAR_PUBLISH', true);
			ToolBarHelper::unpublish('items.unpublish', 'JTOOLBAR_UNPUBLISH', true);
			ToolBarHelper::divider();
			//JToolBarHelper::archiveList('items.archive');
			//JToolBarHelper::checkin('items.checkin');
		}

		if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete')) {
			ToolBarHelper::deleteList('', 'items.delete','JTOOLBAR_EMPTY_TRASH');			
		}
		else if ($canDo->get('core.edit.state')) {
			ToolBarHelper::trash('items.trash');
		} 

		if ($canDo->get('core.admin')) {
			ToolBarHelper::divider();
			ToolBarHelper::preferences('com_cgavisclient');			
		}
	}
}
