<?php
/**
* CG Avis Client - Joomla Module 
* Package			: Joomla 4.x/5.x
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* From              : OT Testimonies  version 1.0, OmegaTheme Extensions - http://omegatheme.com
*/
namespace ConseilGouz\Component\CGAvisClient\Administrator\Model;
// No direct access.
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseInterface;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;

class ItemModel extends AdminModel
{
	protected $text_prefix = 'COM_CGAVISCLIENT_ITEM';
	public function getTable($type = 'Item', $prefix = 'cgavisclientTable', $config = array())
	{
	    $db	= Factory::getContainer()->get(DatabaseInterface::class);
	    return new \ConseilGouz\Component\CGAvisClient\Administrator\Table\ItemTable($db);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_cgavisclient.item', 'item', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		// Determine correct permissions to check.
		if ($this->getState('item.id')) {
			// Existing record. Can only edit in selected categories.
			$form->setFieldAttribute('#FIELD_CATEGORY_ID#', 'action', 'core.edit');
		} else {
			// New record. Can only create in selected categories.
			$form->setFieldAttribute('#FIELD_CATEGORY_ID#', 'action', 'core.create');
		}

		// Modify the form based on access controls.
		if (!$this->canEditState((object) $data)) {
			// Disable fields for display.
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('publish_up', 'disabled', 'true');
			$form->setFieldAttribute('publish_down', 'disabled', 'true');
            $form->setFieldAttribute('state', 'disabled', 'true');
			$form->setFieldAttribute('featured', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('publish_up', 'filter', 'unset');
			$form->setFieldAttribute('publish_down', 'filter', 'unset');
            $form->setFieldAttribute('state', 'filter', 'unset');
			$form->setFieldAttribute('featured', 'filter', 'unset');
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_cgavisclient.edit.item.data', array());

		if (empty($data))
			$data = $this->getItem();

		return $data;
	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param	object	A record object.
	 * @return	array	An array of conditions to add to add to ordering queries.
	 * @since	1.6
	 */
	protected function getReorderConditions($table)
	{
		$condition = array();
		
		$condition[] = 'state >= 0';
		return $condition;
	}
        
    public function featured($pks, $value = 0)
    {
        // Sanitize the ids.
        $pks = (array) $pks;
        ArrayHelper::toInteger($pks);

        try
        {
            $db = $this->getDatabase();
            $query = $db->createQuery()
                        ->update($db->quoteName('#__cgavisclient'))
                        ->set('featured = ' . (int) $value)
                        ->where('id IN (' . implode(',', $pks) . ')');
            $db->setQuery($query);
            $db->execute();
        }
        catch (Exception $e)
        {
            $this->setError($e->getMessage());
            return false;
        }

        $this->cleanCache();

        return true;
    }        
}
