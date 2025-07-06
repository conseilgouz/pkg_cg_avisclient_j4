<?php
/**
* CG Avis Client - Joomla Module 
* Package			: Joomla 4.x/5.x
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* From              : OT Testimonies  version 1.0, OmegaTheme Extensions - http://omegatheme.com
*/
// no direct access
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Button\FeaturedButton;

// HTMLHelper::_('behavior.tooltip');
HTMLHelper::_('behavior.multiselect');

$user		= Factory::getUser();
$userId		= $user->get('id');
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', 'com_cgavisclient.category');
$saveOrder	= $listOrder=='ordering'; 
?>
<form action="<?php echo Route::_('index.php?option=com_cgavisclient&view=items'); ?>" method="post" name="adminForm" id="adminForm">
	<?php if (!empty( $this->sidebar)) : ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">
	<?php else : ?>
	<div id="j-main-container">
	<?php endif; ?>

	<div id="filter-bar" class="btn-toolbar">
		<div class="filter-search btn-group pull-left">
			<label for="filter_search" class="element-invisible"><?php echo Text::_('JSEARCH_FILTER_LABEL'); ?></label>  
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo Text::_('COM_CGAVISCLIENT_SEARCH_IN_TITLE'); ?>" />
        </div>
        <div class="btn-group pull-left">            
			<button type="submit" class="btn hasTooltip"><?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" class="btn hasTooltip" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="btn-group pull-right hidden-phone">
			
					<select name="filter_state" class="inputbox" onchange="this.form.submit()">
						<option value=""><?php echo Text::_('JOPTION_SELECT_PUBLISHED');?></option>
						<?php echo HTMLHelper::_('select.options', HTMLHelper::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true);?>
					</select>
		</div>
	</div>
	<div class="clr"> </div>

    <?php if (empty($this->items)) : ?>
        <div class="alert alert-no-items">
            <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
        </div>
    <?php else : ?>   
    <table class="table table-striped" id="articleList">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Text::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th width="1%" class="nowrap">
					<?php echo HTMLHelper::_('grid.sort', 'JGRID_HEADING_ID', 't.id', $listDirn, $listOrder); ?>
				</th>
                <th width="1%" class="nowrap">
                    <?php echo HTMLHelper::_('grid.sort', 'Categorie', 'c.title', $listDirn, $listOrder); ?>
                </th>                
                <th width="1%" class="nowrap">
                    <?php echo HTMLHelper::_('grid.sort', 'En vedette', 't.featured', $listDirn, $listOrder); ?>
                </th>                
				<th>
					<?php echo HTMLHelper::_('grid.sort',  'Commentaire', 't.comment', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo HTMLHelper::_('grid.sort',  'Nom', 't.name', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo HTMLHelper::_('grid.sort',  'COM_CGAVISCLIENT_HEADING_FIRSTNAME', 't.firstname', $listDirn, $listOrder); ?>
				</th>
				<th>
                    <?php echo HTMLHelper::_('grid.sort',  'JDATE', 't.created', $listDirn, $listOrder); ?>
                </th>                
				
				<th width="5%">
					<?php echo HTMLHelper::_('grid.sort', 'JSTATUS', 'state', $listDirn, $listOrder); ?>
				</th>
				
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="13">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
			$ordering	= ($listOrder == 'ordering');
			$canCreate	= $user->authorise('core.create');
			$canEdit	= $user->authorise('core.edit');
			$canCheckin	= $user->authorise('core.manage',		'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
			$canChange	= $user->authorise('core.edit.state') && $canCheckin;
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
				</td>
				<td class="center">
                    <a href="<?php echo Route::_('index.php?option=com_cgavisclient&task=item.edit&id='.(int) $item->id); ?>">
                    <?php echo $this->escape($item->id); ?>                     
					</a>
				</td>
				<td class="center">
                    <?php echo $this->escape($item->title); ?>                     
				</td>
                <td class="center">
                    <div class="btn-group">
								<?php
									$options = [
										'task_prefix' => 'items.',
										'disabled' =>  !$canChange,
										'id' => 'featured-' . $item->id
									];

									echo (new FeaturedButton)
									->render((int) $item->featured, $i, $options );
								?>
                    </div>
                </td>                
				<td>
				<a href="<?php echo Route::_('index.php?option=com_cgavisclient&task=item.edit&id='.(int) $item->id); ?>">
					<?php 
						$comment = strip_tags($item->comment);
						$comment = HTMLHelper::_('string.truncate', $comment, 150, true, false);
						echo $comment; ?>
					</a>
				</td>
				<td>
					<?php echo $this->escape($item->name); ?>
				</td>
				<td>
					<?php echo $this->escape($item->firstname); ?>
				</td>
                <td>
                    <?php echo HTMLHelper::_('date', $item->created, Text::_('DATE_FORMAT_LC4')); ?> 
                </td>                
				<td class="center">
					<?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'items.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
    <?php endif; ?> 
	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
	</div>
</form>
