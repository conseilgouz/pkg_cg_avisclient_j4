<?php
/**
* CG Avis Client - Joomla Module 
* Version			: 2.0.2
* Package			: Joomla 4.x.x
* copyright 		: Copyright (C) 2021 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* From              : OT Testimonies  version 1.0, OmegaTheme Extensions - http://omegatheme.com
*/
use Joomla\CMS\HTML\HTMLHelper;
defined('JPATH_BASE') or die;

extract($displayData);

// Including fallback code for HTML5 non supported browsers.
HTMLHelper::_('jquery.framework');
HTMLHelper::_('script', 'system/html5fallback.js', array('version' => 'auto', 'relative' => true));

$format = '<input class="star" type="radio" id="%1$s" name="%2$s" value="%3$s" %4$s />';
$alt    = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $name);
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
			<label for="<?php echo $oid; ?>" <?php echo $optionClass; ?> class="star">
				<?php  // echo $option->text; ?>
			</label>
		<?php endforeach; ?>
	<?php endif; ?>
</fieldset>
