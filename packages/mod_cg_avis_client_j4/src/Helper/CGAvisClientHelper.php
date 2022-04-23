<?php
/**
* CG Avis Client - Joomla Module 
* Version			: 2.0.0
* Package			: Joomla 4.x.x
* copyright 		: Copyright (C) 2021 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* From              : OT Testimonies  version 1.0, OmegaTheme Extensions - http://omegatheme.com
*/

// no direct access
namespace ConseilGouz\Module\CGAvisClient\Site\Helper;
// no direct access
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;

class CGAvisClientHelper {
	var $_total = null;
    public static function getList($params,$limitstart,$limit)
	{
		$db = Factory::getDBO();    
        $query  = $db->getQuery(true);
		$where = "state = 1";
		if($params->get('show_featured')) $where .= " AND featured = 1";
        $query->select('*')
         ->from('#__cgavisclient')
         ->where($where)
         ->order('id desc');
        $db->setQuery($query, $limitstart, $limit);
        $result = $db->loadObjectList();
		return $result;
	}
	public static function getTotal($params) {
 	if (empty($_total)) {
		$db = Factory::getDBO();    
        $query  = $db->getQuery(true);
		$where = "state = 1";
		if($params->get('show_featured')) $where .= " AND featured = 1";
        $query->select('count(*)')
         ->from('#__cgavisclient')
         ->where($where)
         ->order('id desc');
        $db->setQuery($query);
 	    $_total = $db->loadResult();	
		}
 	return $_total;
	}
	public static function getCategory($id) {
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		// Construct the query
		$query->select('title as title, alias as alias ')
			->from('#__categories')
			->where('id = '.$id)
			;
		$db->setQuery($query);
		return $db->loadObjectList();

	}	
	static function getAllCategories() {
		$app  = Factory::getApplication();
		$lang = $app->getLanguageFilter();
		$db = Factory::getDbo();
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
		for ($maxLength; $maxLength < $baseLength;)
		{
			$htmlString = HTMLHelper::_('string.truncate', $html, $maxLength, $noSplit = true, $allowHtml = true);
			$htmlStringToPtString = HTMLHelper::_('string.truncate', $htmlString, $maxLength, $noSplit = true, $allowHtml = false);
			if ($ptString == $htmlStringToPtString)
			{
				return $htmlString;
			}
			$diffLength = strlen($ptString) - strlen($htmlStringToPtString);
			$maxLength += $diffLength;
			if ($baseLength <= $maxLength || $diffLength <= 0)
			{
				return $htmlString;
			}
		}
		return $html;
	}	
}