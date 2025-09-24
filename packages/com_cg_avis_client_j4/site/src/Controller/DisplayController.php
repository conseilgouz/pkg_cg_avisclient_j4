<?php
/**
* CG Avis Client
* Package			: Joomla 4.x/5.x/6.x
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* From              : OT Testimonies  version 1.0, OmegaTheme Extensions - http://omegatheme.com
*/
namespace ConseilGouz\Component\CGAvisClient\Site\Controller;
\defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Language\Text;

class DisplayController extends BaseController {

    public function display($cachable = false, $urlparams = false) {

        $view = Factory::getApplication()->getInput()->getCmd('view', 'item');
        Factory::getApplication()->getInput()->set('view', $view);

        parent::display($cachable, $urlparams);

        return $this;
    }
}
