<?php
/**
* CG Avis Client - Joomla Module 
* Package			: Joomla 4.x/5.x/6.x
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* From              : OT Testimonies  version 1.0, OmegaTheme Extensions - http://omegatheme.com
*/
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;

extract($displayData);

// Including fallback code for HTML5 non supported browsers.
// HTMLHelper::_('jquery.framework');
HTMLHelper::_('script', 'system/html5fallback.js', array('version' => 'auto', 'relative' => true));

$params = ComponentHelper::getParams('com_cgavisclient');

$format = '<input class="star" type="radio" id="%1$s" name="%2$s" value="%3$s" %4$s />';
$alt    = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $name);

$document = Factory::getApplication()->getDocument(); 
$wa = $document->getWebAssetManager();

$color = $params->get('rating_color','gold');
if (str_starts_with($color,'--')) {// variable color
    $color = 'var('.$color.')';
}
$color_css = "input.star:checked ~ label.star:before {color:".$color."}";
$wa->addInlineStyle($color_css);
?>
<fieldset id="<?php echo $id; ?>" class="<?php echo trim($class . ' stars'); ?>"
	<?php echo $disabled ? 'disabled' : ''; ?>
	<?php echo $required ? 'required aria-required="true"' : ''; ?>
	<?php echo $autofocus ? 'autofocus' : ''; ?>>

	<?php if (!empty($options)) : ?>
		<?php foreach ($options as $i => $option) : ?>
			<?php
				// Initialize some option attributes.
				$checked  = ((string) $option->value === $value) ? 'checked="checked"' : '';
				$optionClass    = !empty($option->class) ? 'class="' . $option->class . '"' : '';
				$disabled = !empty($option->disable) || ($disabled && !$checked) ? 'disabled' : '';

				// Initialize some JavaScript option attributes.
				$onclick    = !empty($option->onclick) ? 'onclick="' . $option->onclick . '"' : '';
				$onchange   = !empty($option->onchange) ? 'onchange="' . $option->onchange . '"' : '';
				$oid        = $id . $i;
				$ovalue     = htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8');
				$attributes = array_filter(array($checked, $optionClass, $disabled, $onchange, $onclick));
			?>

			<?php if ($required) : ?>
				<?php $attributes[] = 'required aria-required="true"'; ?>
			<?php endif; ?>
			<?php echo sprintf($format, $oid, $name, $ovalue, implode(' ', $attributes)); ?>
			<label for="<?php echo $oid; ?>" <?php echo $optionClass; ?> class="fa <?php echo $params->get('rating_icon', 'fa-star');?> star">
				<?php  // echo $option->text; ?>
			</label>
		<?php endforeach; ?>
	<?php endif; ?>
</fieldset>
