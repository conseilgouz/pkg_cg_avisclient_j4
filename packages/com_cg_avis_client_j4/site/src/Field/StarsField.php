<?php
/**
* CG Avis Client - Joomla Module 
* Package			: Joomla 4.x/5.x
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* From              : OT Testimonies  version 1.0, OmegaTheme Extensions - http://omegatheme.com
*/
namespace ConseilGouz\Component\CGAvisClient\Site\Field;
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\RadioField;
use Joomla\CMS\Layout\FileLayout;

class StarsField extends RadioField
{
	protected $type = 'Stars';
	protected function getInput()
	{
		$layout = new FileLayout('form.field.stars',JPATH_SITE . '/administrator/components/com_cgavisclient/layouts');
		return $layout->render($this->getLayoutData());
	}
	protected function getLayoutData()
	{
		$data = parent::getLayoutData();

		$extraData = array(
			'options' => $this->getOptions(),
			'value'   => (string) $this->value,
		);

		return array_merge($data, $extraData);
	}
}
