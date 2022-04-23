<?php
/**
* CG Avis Client - Joomla Module 
* Version			: 2.0.0
* Package			: Joomla 4.x.x
* copyright 		: Copyright (C) 2021 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* From              : OT Testimonies  version 1.0, OmegaTheme Extensions - http://omegatheme.com
*/

defined('JPATH_BASE') or die;

$list = $displayData['list'];
$pages = $list['pages'];

?>

<div class="pagination pagination-toolbar clearfix isotope" style="text-align: center;">

	<?php if (!empty($pages)) : ?>
		<ul class="pagination-list">
			<?php
			$params = $list['params'];
			$cls = ($params->get('cls_cgavisclient','') != '') ? $params->get('cls_cgavisclient','') : '';
			if ($list['previous']['active']) {
				$icon = 'icon-step-backward icon-previous';
				$btn = str_replace("hasTooltip pagenav","hasTooltip pagenav btn ".$cls,$list['previous']['data']);
				echo $btn;
			}
				?>
			<?php
			if ($list['next']['active']) {
				$icon = 'icon-step-forward icon-next';
				$btn = str_replace("hasTooltip pagenav","hasTooltip pagenav btn ".$cls,$list['next']['data']);
				echo $btn;
			} ?>
		</ul>
	<?php endif; ?>
</div>
