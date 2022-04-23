<?php
/**
* Simple Avis Client - Joomla Module 
* Version			: 1.0.4
* Package			: Joomla 3.x.x
* copyright 		: Copyright (C) 2017 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* From              : OT Testimonies  version 1.0, OmegaTheme Extensions - http://omegatheme.com
* Updated on        : July, 2017
*/

// no direct access
defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use ConseilGouz\Module\CGAvisClient\Site\Helper\CGAvisClientHelper;

$class_sfx = htmlspecialchars($params->get('class_sfx'));
$limit = htmlspecialchars($params->get('count'));
$app = Factory::getApplication();
$limitstart = $app->input->get('limitstart', 0, 'uint');

// Add style and css to header
$doc = Factory::getDocument();
$baseurl 		= URI::base();
$modulefield	= ''.URI::base(true).'/media/mod_cgavisclient/';
$doc->addStyleSheet($modulefield.'/css/cgavisclient.css');

$doc->addStyleSheet($modulefield."css/isotope.css");
$doc->addScript($modulefield."js/isotope.min.js");
$doc->addScript($modulefield.'js/packery-mode.min.js');
$doc->addScript($modulefield."js/imagesloaded.min.js");
$doc->addStyleDeclaration($params->get('css')); 
$cats_lib = array();
$cats_alias = array();
$categories = $params->get('categories');
if (is_null($categories)) {
	$res = CGAvisClientHelper:: getAllCategories();
	$categories = array();
	foreach ($res as $catid) {
			$categories[] = $catid->id;
	}
}
foreach ($categories as $catid) {
	$res = CGAvisClientHelper::getCategory($catid);
	$cats_lib[$catid]= $res[0]->title;
	$cats_alias[$catid] = $res[0]->alias;
}

$lists = CGAvisClientHelper::getList($params,$limitstart,$limit);

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

require( ModuleHelper::getLayoutPath( 'mod_cgavisclient', $params->get('layout', 'isotope') ) );	
