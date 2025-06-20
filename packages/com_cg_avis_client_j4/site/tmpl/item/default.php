<?php
/**
* CG Avis Client - Joomla Module
* Version			: 2.0.2
* Package			: Joomla 4.x.x
* copyright 		: Copyright (C) 2021 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* From              : OT Testimonies  version 1.0, OmegaTheme Extensions - http://omegatheme.com
*/

// no direct access
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Router\Route;
use ConseilGouz\Component\CGAvisClient\Site\Controller\ItemController;

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate')
;

defined('_JEXEC') or die;
$params = ComponentHelper::getParams('com_cgavisclient');
?>

<div class="cgavisclient-form row">
    <div class="col-xs-12 col-sm-4 " style="float:left">
        <h3><?php echo Text::_('COM_CGAVISCLIENT_FORM'); ?></h3>
        <div class="form-content">
            <?php echo $params->get('form_content'); ?>
        </div>
    </div>
    <div class="col-xs-12 col-sm-8">
        <form id="cgavisclient-form" action="<?php echo Route::_('index.php'); ?>" method="post" class="form-validate form-horizontal" enctype="multipart/form-data">          
            <div class="control-group form-group">
                <div class="control-label col-md-3 col-sm-12" >
                    <?php echo $this->form->getLabel('category'); ?> 
                </div>
                <div class="controls col-md-9 col-sm-12">
                    <?php echo $this->form->getInput('category'); ?>
                </div> 
            </div>
            <div class="control-group form-group">
                <div class="control-label col-md-3 col-sm-12">
                    <?php echo $this->form->getLabel('firstname'); ?> 
                </div>
                <div class="controls col-md-3 col-sm-12" style="float:right">
                    <?php echo $this->form->getInput('firstname'); ?>
                </div> 
                <div class="control-label col-md-2 col-sm-12">
                    <?php echo $this->form->getLabel('name'); ?> 
                </div>
                <div class="controls col-md-3 col-sm-12" style="width:8em;">
                    <?php echo $this->form->getInput('name'); ?>
                </div> 
                <div class="controls col-md-1 col-sm-12" style="float:right">
				&nbsp;
				</div>
            </div>
            <div class="control-group form-group">
                <div class="control-label col-md-3 col-sm-12">
                    <?php echo $this->form->getLabel('email'); ?> 
                </div>
                <div class="controls col-md-9 col-sm-12">
                    <?php echo $this->form->getInput('email'); ?>
                </div> 
            </div>                               
            <div class="control-group form-group">
                <div class="control-label col-md-3 col-sm-12">
                    <?php echo $this->form->getLabel('zipcode'); ?> 
                </div>
                <div class="controls col-md-2 col-sm-12">
                    <?php echo $this->form->getInput('zipcode'); ?>
                </div> 
                <div class="control-label col-md-2 col-sm-12 " style="width:8em;">
                    <?php echo $this->form->getLabel('city'); ?> 
                </div>
                <div class="controls col-md-4 col-sm-12" style="float:right">
                    <?php echo $this->form->getInput('city'); ?>
                </div> 
            </div>
            <div class="control-group form-group">
                <div class="control-label col-md-3 col-sm-12">
                    <?php echo $this->form->getLabel('rating'); ?> 
                </div>
                <div class="controls  col-md-9 col-sm-12 stars">
                    <?php  echo $this->form->getInput('rating'); ?>
				</div>
            </div>                               
            <div class="control-group form-group">
                <div class="control-label col-md-3 col-sm-12">
                    <?php echo $this->form->getLabel('comment'); ?> 
                </div>
                <div class="controls col-md-9 col-sm-12">
                    <?php echo $this->form->getInput('comment'); ?>
                </div> 
            </div>  
			<?php if ($params->get('captcha') != '' || $params->get('captcha') != 0) {
			    PluginHelper::importPlugin('captcha', $params['captcha']);
			    $captcha_name = $params['captcha'];
			    $captcha_id = 'dynamic_'.$params['captcha'].'_1';
			    $laclasse = " class='g-".$params['captcha']." required fg-c8 fg-cs12' ";
			    Factory::getApplication()->triggerEvent('onInit', array($captcha_id));
			    $arr =	Factory::getApplication()->triggerEvent('onDisplay', array($captcha_name,$captcha_id,$laclasse));
			    echo '<div id="form-resa-captcha" class="control-group row">';
			    if ($params['captcha'] == "recaptcha") {
			        echo "<label for='".$captcha_id."' class='fg-c4 fg-cs12'>Captcha</label>";
			    }
			    echo $arr[0];
			    echo '</div>';
			}?>
            <div class="control-group form-group">
                <div class="control-label col-sm-3">
                </div>
                <div class="controls col-sm-9">
                    <button class="btn btn-primary validate" type="submit" onclick="Joomla.submitbutton('item.save')"><?php echo Text::_('COM_CGAVISCLIENT_SEND'); ?></button>                    
                </div> 
            </div>                           
                <input type="hidden" name="option" value="com_cgavisclient" />
                <input type="hidden" name="task" value="save" />
                <input type="hidden" name="return" value="<?php echo $this->return_page; ?>" />
                <input type="hidden" name="id" value="" />
                <?php echo HTMLHelper::_('form.token'); ?>
        </form>    
    </div>
</div>