<?php
/**
* CG Avis Scroll - Joomla Module
* Package			: Joomla 4.x - 5.x
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*/

defined('_JEXEC') or die;

extract($displayData);

/**
 * Layout variables
 * -----------------
 * @var   string   $autocomplete    Autocomplete attribute for the field.
 * @var   boolean  $autofocus       Is autofocus enabled?
 * @var   string   $class           Classes for the input.
 * @var   string   $description     Description of the field.
 * @var   boolean  $disabled        Is this field disabled?
 * @var   string   $group           Group the field belongs to. <fields> section in form XML.
 * @var   boolean  $hidden          Is this field hidden in the form?
 * @var   string   $hint            Placeholder for the field.
 * @var   string   $id              DOM id of the field.
 * @var   string   $label           Label of the field.
 * @var   string   $labelclass      Classes to apply to the label.
 * @var   boolean  $multiple        Does this field support multiple values?
 * @var   string   $name            Name of the input field.
 * @var   string   $onchange        Onchange attribute for the field.
 * @var   string   $onclick         Onclick attribute for the field.
 * @var   string   $pattern         Pattern (Reg Ex) of value of the form field.
 * @var   boolean  $readonly        Is this field read only?
 * @var   boolean  $repeat          Allows extensions to duplicate elements.
 * @var   boolean  $required        Is this field required?
 * @var   integer  $size            Size attribute of the input.
 * @var   boolean  $spellcheck      Spellcheck state for the form field.
 * @var   string   $validate        Validation rules to apply.
 * @var   string   $value           Value attribute of the field.
 * @var   array    $checkedOptions  Options that will be set as checked.
 * @var   boolean  $hasValue        Has this field a value assigned?
 * @var   array    $options         Options available for this field.
 * @var   array    $inputType       Options available for this field.
 * @var   string   $accept          File types that are accepted.
 * @var   string   $dataAttribute   Miscellaneous data attributes preprocessed for HTML output
 * @var   array    $dataAttributes  Miscellaneous data attribute for eg, data-*.
 */

// Initialize some field attributes.
$attributes = [
    $class ? 'class="form-cgrange ' . $class . '"' : 'class="form-cgrange"',
    !empty($description) ? 'aria-describedby="' . ($id ?: $name) . '-desc"' : '',
    $disabled ? 'disabled' : '',
    $readonly ? 'readonly' : '',
    !empty($onchange) ? 'onchange="' . $onchange . '"' : '',
    !empty($max) ? 'max="' . $max . '"' : '',
    !empty($step) ? 'step="' . $step . '"' : '',
    gettype($min) !== 'NULL' ? 'min="' . $min . '"' : '',
    $autofocus ? 'autofocus' : '',
    $dataAttribute,
];

$value = is_numeric($value) ? (float) $value : $min;
// CG Range : display current value after range
//             add class="limits" to display ranhge limits
?>
<div style="display:flex">
<input
    type="range"
    name="<?php echo $name; ?>"
    id="<?php echo $id; ?>"
    value="<?php echo htmlspecialchars($value, ENT_COMPAT, 'UTF-8'); ?>"
    <?php echo implode(' ', $attributes); ?>>
<span id="cgrange-label-<?php echo $id;?>" class="cgrange-label" data="<?php echo $id;?>" style="margin-left:1em"></span>
</div>