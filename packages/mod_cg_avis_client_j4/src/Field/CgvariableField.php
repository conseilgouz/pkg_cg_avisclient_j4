<?php
/**
* CG Avis Client - Joomla Module 
* Package			: Joomla 4.x/5.x
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* From              : OT Testimonies  version 1.0, OmegaTheme Extensions - http://omegatheme.com
*/

namespace ConseilGouz\Module\CGAvisClient\Site\Field;

defined('_JEXEC') or die;
use Joomla\CMS\Form\Field\TextField;

class CgvariableField extends TextField
{
    public $type = 'Cgvariable';

    /**
     * Name of the layout being used to render the field
     *
     * @var    string
     * @since  3.7
     */
    protected $layout = 'cgvariable';

    /**
     * Unit
     *
     * @var    string
     */

    protected $unit = "";
    /* module's information */
    public $_ext = "mod";
    public $_type = "cg";
    public $_name = "memo";

    protected function getLayoutPaths()
    {
        $paths = parent::getLayoutPaths();
        $paths[] = dirname(__DIR__).'/../layouts';
        return $paths;

    }

    /**
     * Method to get the field input markup.
     *
     * @return  string  The field input markup.
     *
     * @since   3.2
     */
    protected function getInput()
    {
        return $this->getRenderer($this->layout)->render($this->collectLayoutData());
    }
    /**
     * Method to get the data to be passed to the layout for rendering.
     * The data is cached in memory.
     *
     * @return  array
     *
     * @since 5.1.0
     */
    protected function collectLayoutData(): array
    {
        if ($this->layoutData) {
            return $this->layoutData;
        }

        $this->layoutData = $this->getLayoutData();
        return $this->layoutData;
    }

}
