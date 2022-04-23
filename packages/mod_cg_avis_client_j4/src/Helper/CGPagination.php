<?php
/**
* CG Avis Client - Joomla Module 
* Version			: 2.0.0
* Package			: Joomla 4.x.x
* copyright 		: Copyright (C) 2021 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* From              : OT Testimonies  version 1.0, OmegaTheme Extensions - http://omegatheme.com
*/
namespace ConseilGouz\Module\CGAvisClient\Site\Helper;
defined('JPATH_PLATFORM') or die;

use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Log\Log;

class CGPagination extends Pagination
{
protected $params;
	
	public function __construct($total, $limitstart, $limit, $prefix = '', JApplicationCms $app = null,$param = null)
	{
		$this->params = $param;
		parent::__construct($total, $limitstart, $limit, $prefix , $app);
	}
	
	
	/**
	 * Create the html for a list footer
	 *
	 * @param   array  $list  Pagination list data structure.
	 *
	 * @return  string  HTML for a list start, previous, next,end
	 *
	 * @since   1.5
	 */
	protected function _list_render($list)
	{
		return LayoutHelper::render('pagination.isotope', array('list' => $list),JPATH_SITE . '/modules/mod_cgavisclient/layouts');
	}
	
	/**
	 * Create and return the pagination page list string, ie. Previous, Next, 1 2 3 ... x.
	 *
	 * @return  string  Pagination page list string.
	 *
	 * @since   1.5
	 */
	public function getPagesLinks()
	{
		// Build the page navigation list.
		$data = $this->_buildDataObject();

		$list           = array();
		$list['prefix'] = $this->prefix;
        $list['params'] = $this->params; // SP Pagination
		
		$itemOverride = false;
		$listOverride = false;

		$chromePath = JPATH_THEMES . '/' . $this->app->getTemplate() . '/html/pagination.php';

		if (file_exists($chromePath))
		{
			include_once $chromePath;

			/*
			 * @deprecated Item rendering should use a layout
			 */
			if (function_exists('pagination_item_active') && function_exists('pagination_item_inactive'))
			{
				Log::add(
					'pagination_item_active and pagination_item_inactive are deprecated. Use the layout joomla.pagination.link instead.',
					Log::WARNING,
					'deprecated'
				);

				$itemOverride = true;
			}

			/*
			 * @deprecated The list rendering is now a layout.
			 * @see JPagination::_list_render()
			 */
			if (function_exists('pagination_list_render'))
			{
				Log::add('pagination_list_render is deprecated. Use the layout joomla.pagination.list instead.', Log::WARNING, 'deprecated');
				$listOverride = true;
			}
		}

		// Build the select list
		if ($data->all->base !== null)
		{
			$list['all']['active'] = true;
			$list['all']['data']   = $itemOverride ? pagination_item_active($data->all) : $this->_item_active($data->all);
		}
		else
		{
			$list['all']['active'] = false;
			$list['all']['data']   = $itemOverride ? pagination_item_inactive($data->all) : $this->_item_inactive($data->all);
		}

		if ($data->start->base !== null)
		{
			$list['start']['active'] = true;
			$list['start']['data']   = $itemOverride ? pagination_item_active($data->start) : $this->_item_active($data->start);
		}
		else
		{
			$list['start']['active'] = false;
			$list['start']['data']   = $itemOverride ? pagination_item_inactive($data->start) : $this->_item_inactive($data->start);
		}

		if ($data->previous->base !== null)
		{
			$list['previous']['active'] = true;
			$list['previous']['data']   = $itemOverride ? pagination_item_active($data->previous) : $this->_item_active($data->previous);
		}
		else
		{
			$list['previous']['active'] = false;
			$list['previous']['data']   = $itemOverride ? pagination_item_inactive($data->previous) : $this->_item_inactive($data->previous);
		}

		// Make sure it exists
		$list['pages'] = array();

		foreach ($data->pages as $i => $page)
		{
			if ($page->base !== null)
			{
				$list['pages'][$i]['active'] = true;
				$list['pages'][$i]['data']   = $itemOverride ? pagination_item_active($page) : $this->_item_active($page);
			}
			else
			{
				$list['pages'][$i]['active'] = false;
				$list['pages'][$i]['data']   = $itemOverride ? pagination_item_inactive($page) : $this->_item_inactive($page);
			}
		}

		if ($data->next->base !== null)
		{
			$list['next']['active'] = true;
			$list['next']['data']   = $itemOverride ? pagination_item_active($data->next) : $this->_item_active($data->next);
		}
		else
		{
			$list['next']['active'] = false;
			$list['next']['data']   = $itemOverride ? pagination_item_inactive($data->next) : $this->_item_inactive($data->next);
		}

		if ($data->end->base !== null)
		{
			$list['end']['active'] = true;
			$list['end']['data']   = $itemOverride ? pagination_item_active($data->end) : $this->_item_active($data->end);
		}
		else
		{
			$list['end']['active'] = false;
			$list['end']['data']   = $itemOverride ? pagination_item_inactive($data->end) : $this->_item_inactive($data->end);
		}

		if ($this->total > $this->limit)
		{
			return $listOverride ? pagination_list_render($list) : $this->_list_render($list);
		}
		else
		{
			return '';
		}
	}	
	
	
}
