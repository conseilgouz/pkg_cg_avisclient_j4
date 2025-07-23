<?php
/**
* CG Avis Scroll - Joomla Module
* Package			: Joomla 4.x/5.x
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*/
// no direct access

namespace ConseilGouz\Module\CGAvisScroll\Site\Helper;

defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Database\DatabaseInterface;

class CGAvisScrollHelper
{
    public static function getList($params)
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query  = $db->getQuery(true);
        $where = "state = 1";
        if ($params->get('show_featured')) {
            $where .= " AND featured = 1";
        }
        $categories = $params->get('categories', array());
        if (sizeof($categories)) {
            $categories = implode(',', $categories);
            $where .= ' AND category IN ('.$categories.')';
        }

        $order = "id desc";
        $query->select('*')
         ->from('#__cgavisclient')
         ->where($where)
         ->order($order);

        $db->setQuery($query);

        $result = $db->loadObjectList();
        if ($params->get('sf_type') == "random") {
            shuffle($result);
        }
        if ($params->get('sf_limit')) {
            $result = array_slice($result, 0, (int)$params->get('sf_limit'));
        }

        return $result;
    }
    public static function getTotal($params)
    {
        if (empty($_total)) {
            $db = Factory::getContainer()->get(DatabaseInterface::class);
            $query  = $db->getQuery(true);
            $where = "state = 1";
            if ($params->get('show_featured')) {
                $where .= " AND featured = 1";
            }
            $query->select('count(*)')
             ->from('#__cgavisclient')
             ->where($where)
             ->order('id desc');
            $db->setQuery($query);
            $_total = $db->loadResult();
        }
        return $_total;
    }
    public static function getCategory($id)
    {
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);
        // Construct the query
        $query->select('title as title, alias as alias ')
            ->from('#__categories')
            ->where('id = '.$id)
        ;
        $db->setQuery($query);
        return $db->loadObjectList();

    }
    public static function getAllCategories()
    {
        $app  = Factory::getApplication();
        $lang = $app->getLanguageFilter();
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        $query = $db->getQuery(true);

        // Construct the query
        $query->select('distinct cat.id')
            ->from('#__categories as cat ')
            ->where('extension like "com_cgavisclient" AND cat.published = 1 AND (cat.language like "'.$lang.'" or cat.language like "*") and cat.access = 1')
        ;
        // Setup the query
        $db->setQuery($query);
        return $db->loadObjectList();
    }

    public static function truncate($html, $maxLength = 0)
    {
        $baseLength = strlen($html);
        $ptString = HTMLHelper::_('string.truncate', $html, $maxLength, $noSplit = true, $allowHtml = false);
        for ($maxLength; $maxLength < $baseLength;) {
            $htmlString = HTMLHelper::_('string.truncate', $html, $maxLength, $noSplit = true, $allowHtml = true);
            $htmlStringToPtString = HTMLHelper::_('string.truncate', $htmlString, $maxLength, $noSplit = true, $allowHtml = false);
            if ($ptString == $htmlStringToPtString) {
                return $htmlString;
            }
            $diffLength = strlen($ptString) - strlen($htmlStringToPtString);
            $maxLength += $diffLength;
            if ($baseLength <= $maxLength || $diffLength <= 0) {
                return $htmlString;
            }
        }
        return $html;
    }
    public static function showDirection($num_sf, $sf_direction)
    {
        echo '<span id="toDirection">';
        if ($sf_direction == 1) {
            echo '<span class="icon-dir-down fas"></span>';
            echo '<span id="toDirectionText"> </span>';
            echo '<span class="icon-dir-up fas"></span>';
            echo '<span id="toDirText"> </span>';
        } else {
            echo '<span class="icon-dir-left fas"></span>';
            echo '<span id="toDirectionText"> </span>';
            echo '<span class="icon-dir-right fas"></span>';
            echo '<span id="toDirText"> </span>';
        }
        echo '</span>';

    }
}
