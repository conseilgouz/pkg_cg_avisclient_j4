<?php
/**
* CG Avis Client - Joomla Module 
* Package			: Joomla 4.x/5.x
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* From              : OT Testimonies  version 1.0, OmegaTheme Extensions - http://omegatheme.com
*/
namespace ConseilGouz\Component\CGAvisClient\Administrator\View\Item;
// No direct access
defined('_JEXEC') or die;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;


class HtmlView extends BaseHtmlView {
	protected $form;
	protected $item;
	protected $state;

	/**
	 * Display the view
	 */
	public function display($tpl = null)
	{
		// Initialiase variables.
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			\Error::raiseError(500, implode("\n", $errors));
			return false;
		}
		// Load the submenu.
		$this->addToolbar();
// 		$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		$user		= Factory::getApplication()->getIdentity();
		$userId		= $user->id;
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
        $canDo = ContentHelper::getActions('com_cgavisclient');

		ToolBarHelper::title($isNew ? Text::_('COM_CGAVISCLIENT_MANAGER_ITEM_NEW') : Text::_('COM_CGAVISCLIENT_MANAGER_ITEM_EDIT'), '#xs#.png');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit') || count($user->getAuthorisedCategories('com_cgavisclient', 'core.create')) > 0)) {
			ToolBarHelper::apply('item.apply');
			ToolBarHelper::save('item.save');

			if ($canDo->get('core.create')) {
				ToolBarHelper::save2new('item.save2new');
			}
		}

		if (empty($this->item->id))  {
			ToolBarHelper::cancel('item.cancel');
		}
		else {
			ToolBarHelper::cancel('item.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
