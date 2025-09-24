<?php

/**
* CG Avis Scroll - Joomla Module
* Package			: Joomla 4.x/5.x/6.x
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*/

namespace ConseilGouz\Module\CGAvisScroll\Site\Field;

\defined('_JEXEC') or die;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Form\Field\RadioField;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Associations;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;

/**
 * Form Field class for the Avis Client.
 * get global value from CG Avis Client Component
 * Provides radio button inputs
 *
 * @since  1.7.0
 */
class RadioavisField extends RadioField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.7.0
     */
    protected $type = 'Radioavis';

    /**
     * Method to get the field options.
     *
     * @return  object[]  The field option objects.
     *
     * @since   3.7.0
     */
    protected function getOptions()
    {
        $header    = $this->header;
        $fieldname = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname);
        $options   = [];
        // Add header.
        if (!empty($header)) {
            $header_title = Text::_($header);
            $options[]    = HTMLHelper::_('select.option', '', $header_title);
        }

        foreach ($this->element->xpath('option') as $option) {
            // Filter requirements
            $requires = explode(',', (string) $option['requires']);

            // Requires multilanguage
            if (\in_array('multilanguage', $requires) && !Multilanguage::isEnabled()) {
                continue;
            }

            // Requires associations
            if (\in_array('associations', $requires) && !Associations::isEnabled()) {
                continue;
            }

            // Requires adminlanguage
            if (\in_array('adminlanguage', $requires) && !ModuleHelper::isAdminMultilang()) {
                continue;
            }

            // Requires vote plugin
            if (\in_array('vote', $requires) && !PluginHelper::isEnabled('content', 'vote')) {
                continue;
            }

            // Requires record hits
            if (\in_array('hits', $requires) && !ComponentHelper::getParams('com_content')->get('record_hits', 1)) {
                continue;
            }

            // Requires workflow
            if (\in_array('workflow', $requires) && !ComponentHelper::getParams('com_content')->get('workflow_enabled')) {
                continue;
            }

            $value = (string) $option['value'];
            $text  = trim((string) $option) != '' ? trim((string) $option) : $value;

            $disabled = (string) $option['disabled'];
            $disabled = ($disabled === 'true' || $disabled === 'disabled' || $disabled === '1');
            $disabled = $disabled || ($this->readonly && $value != $this->value);

            $checked = (string) $option['checked'];
            $checked = ($checked === 'true' || $checked === 'checked' || $checked === '1');

            $selected = (string) $option['selected'];
            $selected = ($selected === 'true' || $selected === 'selected' || $selected === '1');

            $tmp = [
                    'value'    => $value,
                    'text'     => Text::alt($text, $fieldname),
                    'disable'  => $disabled,
                    'class'    => (string) $option['class'],
                    'selected' => ($checked || $selected),
                    'checked'  => ($checked || $selected),
            ];

            // Set some event handler attributes. But really, should be using unobtrusive js.
            $tmp['onclick']  = (string) $option['onclick'];
            $tmp['onchange'] = (string) $option['onchange'];

            if ((string) $option['showon']) {
                $encodedConditions = json_encode(
                    FormHelper::parseShowOnConditions((string) $option['showon'], $this->formControl, $this->group)
                );

                $tmp['optionattr'] = " data-showon='" . $encodedConditions . "'";
            }

            // Add the option object to the result set.
            $options[] = (object) $tmp;
        }

        if ($this->element['useglobal']) {
            $tmp        = new \stdClass();
            $tmp->value = '';
            $tmp->text  = Text::_('JGLOBAL_USE_GLOBAL');
            $component  = 'com_cgavisclient';

            $params = ComponentHelper::getParams($component);
            $value  = $params->get($this->fieldname);

            // Try with global configuration
            if (\is_null($value)) {
                $value = Factory::getApplication()->get($this->fieldname);
            }

            // Try with menu configuration
            if (\is_null($value) && Factory::getApplication()->getInput()->getCmd('option') === 'com_menus') {
                $value = ComponentHelper::getParams('com_menus')->get($this->fieldname);
            }

            if (!\is_null($value)) {
                $value = (string) $value;

                foreach ($options as $option) {
                    if ($option->value === $value) {
                        $value           = $option->text;
                        $tmp->optionattr = ['data-global-value' => $option->value];

                        break;
                    }
                }

                $tmp->text = Text::sprintf('JGLOBAL_USE_GLOBAL_VALUE', $value);
            }

            array_unshift($options, $tmp);
        }

        return $options;
    }

}
