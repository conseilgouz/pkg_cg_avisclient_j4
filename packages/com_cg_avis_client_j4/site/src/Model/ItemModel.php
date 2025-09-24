<?php
/**
* CG Avis Client
* Package			: Joomla 4.x/5.x/6.x
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* From              : OT Testimonies  version 1.0, OmegaTheme Extensions - http://omegatheme.com
*/

namespace ConseilGouz\Component\CGAvisClient\Site\Model;

// No direct access
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\DatabaseInterface;

class ItemModel extends ListModel
{
    public function getListQuery()
    {
        $db	= Factory::getContainer()->get(DatabaseInterface::class);
        $query  = $db->getQuery(true);
        $query->select(
            't.id , t.category, c.title, t.name, t.firstname, t.zipcode, t.comment, t.rating, t.city'
        );
        $query->from('`#__cgavisclient` as t')
              ->join('INNER', '#__categories as c on c.id = t.category')
              ->where('state=1');
        // Add the list ordering clause.
        $query->order($db->escape($this->getState('list.ordering', 't.ordering')) . ' ' . $db->escape($this->getState('list.direction', 'ASC')));
        return $query;
    }
    public function getForm($data = array(), $loadData = false)
    {
        // Get the form.
        $form = $this->loadForm('com_cgavisclient.item', 'item', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        if ($this->getState('item.id')) {
            // Existing record. Can only edit in selected categories.
            $form->setFieldAttribute('#FIELD_CATEGORY_ID#', 'action', 'core.edit');
        } else {
            // New record. Can only create in selected categories.
            $form->setFieldAttribute('#FIELD_CATEGORY_ID#', 'action', 'core.create');
        }
        return $form;
    }
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState('com_cgavisclient.edit.item.data', array());
        if (empty($data)) {
            $data = '';
        }
        return $data;
    }
    public function save($table, $data, $id = 0)
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        if ($id) {
            $data->id = $id;
            $db->updateObject($table, $data, 'id', false);
            return $id;
        } else {
            if (!$db->insertObject($table, $data, 'id')) {
                echo $db->stderr();
                die();
            }
            return $data->id;
        }
    }
    protected function populateState($ordering = null, $direction = null)
    {
        $app = Factory::getApplication();
        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'uint');
        $this->setState('list.limit', $limit);
        $limitstart = $app->getInput()->get('limitstart', 0, 'uint');
        $this->setState('list.start', $limitstart);
    }
}
