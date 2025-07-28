<?php
/**
* CG Avis Scroll - Joomla Module
* Package			: Joomla 4.x - 5.x
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*/
// no direct access
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use ConseilGouz\Module\CGAvisScroll\Site\Helper\CGAvisScrollHelper;

$document 		= Factory::getApplication()->getDocument();
$baseurl 		= URI::base();
$modulefield	= ''.URI::base(true).'/media/'.$module->module.'/';

//Get this module id
$nummod_sf		= $module->id;
$num_sf		= 'mod'.$nummod_sf;

$limit = $params->get('sf_limit', '0');
$app = Factory::getApplication();
$intro_maxwidth = $params->get('intro_maxwidth', '0');

$scrolltype  = $params->get('scrolltype', 'lines');
$sf_type = $params->get('sf_type', 'all');
$sf_height	= $params->get('sf_height', 200);
$sf_width   = $params->get('sf_width', 200);
if ($sf_type == 'FEED') {
    $rssitems   = $params->get('rssitems', 3);
} else {
    $rssitems   = $params->get('catitems', 3);
}
$sf_delay	= $params->get('sf_delay', 1);
$sf_speed	= $params->get('sf_speed', 2);
$sf_pause	= $params->get('sf_pause', 1);
$sf_direction = $params->get('direction', 0);
$sf_delay	= $sf_delay * 1000;
$sf_slowdown = $params->get('sf_extraslow', 0);

$cat_list =  $params->get('categories');

$libdateformat = Text::_('DATE_FORMAT_LC4'); // format d'affichage des dates au format php d/m/Y H:i  = format français avec heure/minutes

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->registerAndUseStyle('avisscroll', 'media/'.$module->module.'/css/avisscroll.css');

if ((bool)Factory::getApplication()->getConfig()->get('debug')) { // Mode debug
    $document->addScript($modulefield.'js/avisscroll.js');
} else {
    $wa->registerAndUseScript('avisscroll', 'media/'.$module->module.'/js/avisscroll.js');
}
if ($params->get('css', '')) {
    $wa->addInlineStyle($params->get('css', ''));
}
if ($params->get('fontawesome', '')) {
    $wa->registerAndUseStyle('fontawesome','media/system/css/joomla-fontawesome.css');
}
$lists = CGAvisScrollHelper::getList($params);
$count = 0;

$document->addScriptOptions(
    $module->module.'_'.$module->id,
    array('id' => $module->id,'scrolltype' => $scrolltype,'speed' => $sf_speed, 'pause' => $sf_pause, 'height' => $sf_height, 'width' => $sf_width, 'direction' => $sf_direction,'count' => $count,'delay' => $sf_delay,'slowdown' => $sf_slowdown)
);
$lang = Factory::getApplication()->getLanguage();

$sh_button = $params->get('rssupdn', 1);

echo '<div id="cg_avis_scroll_'.$module->id.'" class="cg_avis_scroll" data="'.$module->id.'">';
// Image handling
$iUrl	= isset($article->image) ? $article->image : null;
$iTitle = isset($article->imagetitle) ? $article->imagetitle : null;
if (($sh_button == 1) && ($module->showtitle == 1)) {
    CGAvisScrollHelper::showDirection($num_sf, $sf_direction);
}
?>

        <div style="text-align: left! important"  class="cg-scroll" data="<?php echo $module->id ?>">
<?php
if ($sh_button == 1) { // show up/down button
    CGAvisScrollHelper::showDirection($num_sf, $sf_direction);
}
?>
		</div> 
		<div id="sfdmarqueecontainer" data="<?php echo $module->id ?>" >
		<div id="vmarquee" style="position: absolute;">		
		<!-- Show items -->
<?php
for ($twice = 0; $twice < 2; $twice++) { // continuous scroll effect
    echo '<ul class="cg-scroll-items-'.$twice.'"';
    if ($sf_direction == 0) {
        echo 'style="width:'.(($sf_width * (sizeof($lists) + 1))).'px;"';
    }
    echo '>'; // end of ul
    foreach ($lists as $key => $item) {
        $cat = CGAvisScrollHelper::getCategory($item->category);
        ?>
			<li>
                <div class="cg_one" style="clear:both" data="<?php echo $module->id ?>">
                <i class="fa fa-quote-left" style="float:left"></i>
		<?php
        $stars = '</div><div class="cg_ratting col-5"';
        $stars .= ' style = "float:right;margin-top:-1.5em" ';
        $stars .= ">";
        for ($j = 0; $j < $item->rating; $j++) {
            $stars .= '<i class="fa fa-star" style="color:gold"></i> ';
        }
        $stars .= '</div> ';
        $deb = '';
        $perso = $params->get('perso', '');
        if ($perso) {
            $deb .= '<div class="cg_name " ';
            $deb .= ' style = "padding:0;" ';
            $deb .= '>';
            $arr_css = array("{name}" => $item->name,"{first}" => $item->firstname,"{cat}" => $cat[0]->title,"{date}" => HTMLHelper::_('date', $item->created, $libdateformat), "{stars}" => $stars, "{zip}" => $item->zipcode, "{city}" => $item->city);
            foreach ($arr_css as $key => $val) {
                $perso = str_replace($key, $val, $perso);
            }
            $perso = $deb.$perso.'</div>';
        }
        $comment = $item->comment;
        if ($intro_maxwidth > 0) {
            $trunc = CGAvisScrollHelper::truncate($comment, $intro_maxwidth);
            HTMLHelper::_('bootstrap.collapse', '.selector', []);
            if (substr($trunc, strlen($trunc) - 3, 3) == "...") {
                $comment = '<div class="cg_tcontent1 sc_intr_panel collapse show" id="scintro'.$item->id.'">'.$trunc;
                $comment = str_replace('</p>...', '...</p>', $comment);
                $comment .= '<button id="'.$item->id.'" class="btn btnsuite">Lire la suite...</button></div>';
                $comment .= '<div class="cg_tcontent2 sc_acc_panel collapse" id="scpanel'.$item->id.'">';
                $comment .= $item->comment."<i class='fa fa-quote-right' style='float:right'></i></div>";
            } else {
                $comment = '<div class="cg_tcontent ">'.$comment.'<i class="fa fa-quote-right" style="float:right"></i></div>';
            }
        } else { // avis complet
            $comment = '<div class="cg_tcontent ">'.$comment.'<i class="fa fa-quote-left" style="float:right"></i></div>';
        }
        echo $comment.$perso;
        ?>
				
		</li>
		<?php
    }
    echo '</ul>';
}
?>
		</div>
	</div>
	<?php if ($params->get('add_cgavisclient', 0) != 0) {
	    $lacat = "";
	    if ($cat_list && (count($cat_list) == 1)) {// une seule catégorie
	        $lacat = "&cat=".$cat_list[0];
	    }
	    ?>   
<div class="scroll_propose text-center m-1">
			<a href="<?php echo Route::_('index.php?option=com_cgavisclient&view=item'.$lacat) ?>" class="btn">
                <?php echo Text::_('MOD_CGAVISSCROLL_PROPOSE');?></a>
			</button>
</div>
<?php }?>
    
</div>
