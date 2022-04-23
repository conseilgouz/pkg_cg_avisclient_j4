<?php
/**
* CG Avis Client - Joomla Module 
* Version			: 2.0.2
* Package			: Joomla 4.x.x
* copyright 		: Copyright (C) 2021 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* From              : OT Testimonies  version 1.0, OmegaTheme Extensions - http://omegatheme.com
*/
namespace ConseilGouz\Component\CGAvisClient\Administrator\Controller;

\defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;

class DisplayController extends BaseController {
	/**
	 * The default view.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $default_view = 'items';

	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe URL parameters and their variable types, for valid values see {@link \JFilterInput::clean()}.
	 *
	 * @return  static|boolean   This object to support chaining or false on failure.
	 *
	 * @since   3.1
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$view   = $this->input->get('view', 'items');
		$layout = $this->input->get('layout', 'default');
		$id     = $this->input->getInt('id');
		parent::display();

		return $this;
	}


}
