<?php
/**
* CG Avis Client - Joomla Module 
* Package			: Joomla 4.x/5.x
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* From              : OT Testimonies  version 1.0, OmegaTheme Extensions - http://omegatheme.com
*/
namespace ConseilGouz\Component\CGAvisClient\Administrator\Controller;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Router\Route;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\String\StringHelper;
use Joomla\CMS\Session\Session;

class ItemsController extends AdminController
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_CGAVISCLIENT_ITEMS';

    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->registerTask('unfeatured',    'featured');
    }  
    
    public function featured()
    {
        // Check for request forgeries
        Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

        $user   = Factory::getApplication()->getIdentity();
        $ids    = $this->input->get('cid', array(), 'array');
        $values = array('featured' => 1, 'unfeatured' => 0);
        $task   = $this->getTask();
        $value  = ArrayHelper::getValue($values, $task, 0, 'int');

        // Access checks.
        foreach ($ids as $i => $id)
        {
            if (!$user->authorise('core.edit.state', 'com_cgavisclient.item.'.(int) $id))
            {
                // Prune items that you can't change.
                unset($ids[$i]);
                \JError::raiseNotice(403, Text::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
            }
        }

        if (empty($ids))
        {
            \JError::raiseWarning(500, Text::_('JERROR_NO_ITEMS_SELECTED'));
        }
        else
        {
            // Get the model.
            $model = $this->getModel();

            // Publish the items.
            if (!$model->featured($ids, $value))
            {
                \JError::raiseWarning(500, $model->getError());
            }
        }

        $this->setRedirect('index.php?option=com_cgavisclient&view=items');
    }      

	public function getModel($name = 'Item', $prefix = 'cgavisclientModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
}