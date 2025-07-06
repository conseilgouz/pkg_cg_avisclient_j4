<?php
/**
* CG Avis Client - Joomla Module 
* Package			: Joomla 4.x/5.x
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* From              : OT Testimonies  version 1.0, OmegaTheme Extensions - http://omegatheme.com
*/
namespace ConseilGouz\Component\CGAvisClient\Administrator\Controller;

\defined('_JEXEC') or die;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Utilities\ArrayHelper;

class ItemController extends FormController
{
	protected $text_prefix = 'COM_CGAVISCLIENT_ITEM';

	protected function allowAdd($data = array())
	{
		// Initialise variables.
		$user		= Factory::getApplication()->getIdentity();
		
		$categoryId = ArrayHelper::getValue($data, 'catid', $this->input->getInt('filter_category_id'), 'int');
		$allow		= null;

		if ($categoryId) {
			// If the category has been passed in the URL check it.
			$allow	= $user->authorise('core.create', $this->option.'.category.'.$categoryId);
		}

		if ($allow === null) {
			// In the absense of better information, revert to the component permissions.
			return parent::allowAdd($data);
		} else {
			return $allow;
		}
	}

	protected function allowEdit($data = array(), $key = 'id')
	{
		// Initialise variables.
		$user		= Factory::getApplication()->getIdentity();
		$recordId	= (int) isset($data[$key]) ? $data[$key] : 0;
		$categoryId = 0;

		if ($recordId) {
			$categoryId = (int) $this->getModel()->getItem($recordId)->catid;
		}

		if ($categoryId) {
			// The category has been set. Check the category permissions.
			return $user->authorise('core.edit', $this->option.'.category.'.$categoryId);
		} else {
			// Since there is no asset tracking, revert to the component permissions.
			return parent::allowEdit($data, $key);
		}
	}

    public function save($key = null, $urlVar = null)
    {       
        // Check for request forgeries.
        Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

        // Initialise variables.
        $app = Factory::getApplication();
        
        $input = $app->input;
        
        $model= $this->getModel('item'); 
        $data = $input->getVar('jform', array(), 'post', 'array');
        $task = $this->getTask();
        $context = 'com_cgavisclient.edit.item';
        $recordId = $input->getInt('id');
        
        $jinput = Factory::getApplication()->input;
        $files = $jinput->files->get('jform');
        $file = $files['avatar']; 
          
        if (!$this->checkEditId($context, $recordId))
        {
            // Somehow the person just went to the form and saved it - we don't allow that.
            $this->setError(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $recordId));
            $this->setMessage($this->getError(), 'error');
            $this->setRedirect(Route::_('index.php?option=com_cgavisclient&view=items' . $this->getRedirectToListAppend(), false));

            return false;
        }

        // Populate the row id from the session.
        $data['id'] = $recordId;

        // The save2copy task needs to be handled slightly differently.
        if ($task == 'save2copy')
        {
            // Check-in the original row.
            if ($model->checkin($data['id']) === false)
            {
                // Check-in failed, go back to the item and display a notice.
                $this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()), 'warning');
                return false;
            }

            // Reset the ID and then treat the request as for Apply.
            $data['id'] = 0;
            $data['associations'] = array();
            $task = 'apply';
        }
        // Check for validation errors.
        if ($data === false)
        {
            // Get the validation messages.
            $errors = $model->getErrors();

            // Push up to three validation messages out to the user.
            for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
            {
                if ($errors[$i] instanceof Exception)
                {
                    $app->enqueueMessage($errors[$i]->getMessage(), 'warning');
                }
                else
                {
                    $app->enqueueMessage($errors[$i], 'warning');
                }
            }

            // Save the data in the session.
            $app->setUserState('com_cgavisclient.edit.item.data', $data);

            // Redirect back to the edit screen.
            $this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false));

            return false;
        }

        // Attempt to save the data.
        if (!$model->save($data))
        {
            // Save the data in the session.
            $app->setUserState('com_cgavisclient.edit.item.data', $data);

            // Redirect back to the edit screen.
            $this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
            $this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false));

            return false;
        }

        // Save succeeded, check-in the row.
        if ($model->checkin($data['id']) === false)
        {
            // Check-in failed, go back to the row and display a notice.
            $this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()), 'warning');
            $this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false));

            return false;
        }

        $this->setMessage(Text::_('Save sucess!'));

        // Redirect the user and adjust session state based on the chosen task.
        switch ($task)
        {
            case 'apply':
                // Set the row data in the session.
                $recordId = $model->getState($this->context . '.id');
                $this->holdEditId($context, $recordId);
                $app->setUserState('com_cgavisclient.edit.item.data', null);

                // Redirect back to the edit screen.
                $this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false));
                break;

            case 'save2new':
                // Clear the row id and data in the session.
                $this->releaseEditId($context, $recordId);
                $app->setUserState('com_cgavisclient.edit.item.data', null);

                // Redirect back to the edit screen.
                $this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend(), false));
                break;

            default:
                // Clear the row id and data in the session.
                $this->releaseEditId($context, $recordId);
                $app->setUserState('com_cgavisclient.edit.item.data', null);

                // Redirect to the list screen.
                $this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend(), false));
                break;
        }
    }                    

}