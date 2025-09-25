<?php 
/**
* CG Avis Client - Joomla Module 
* Package			: Joomla 4.x/5.x/6.x
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* From              : OT Testimonies  version 1.0, OmegaTheme Extensions - http://omegatheme.com
*/

// no direct access
defined('_JEXEC') or die;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Router\Route;
use ConseilGouz\Module\CGAvisClient\Site\Helper\CGAvisClientHelper;

$class_sfx = htmlspecialchars($params->get('class_sfx',''));
$limit = htmlspecialchars($params->get('count'));
$app = Factory::getApplication();
$limitstart = $app->getInput()->get('limitstart', 0, 'uint');

$sf_icon = $params->get('rating_icon', 0);
if (!$sf_icon) { // config icon
    $component  = 'com_cgavisclient';
    $comparams = ComponentHelper::getParams($component);
    $sf_icon  = $comparams->get('rating_icon','fa-star');
} else { // other icon
    $sf_icon = $params->get('rating_icon_mod','fa-star');
}

// Add style and css to header
$doc = Factory::getDocument();
$modulefield	= 'media/mod_cg_avisclient/';

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->registerAndUseStyle('avis',$modulefield.'/css/cg_avisclient.css');
$wa->registerAndUseStyle('isotope',$modulefield."css/isotope.css");
$wa->registerAndUseScript('isotope',$modulefield."js/isotope.min.js");
$wa->registerAndUseScript('packery',$modulefield.'js/packery-mode.min.js');
$wa->registerAndUseScript('imagesloaded',$modulefield."js/imagesloaded.min.js");
if ($params->get('css','')) {
    $wa->addInlineStyle($params->get('css')); 
}
if ($params->get('fontawesome', '')) {
    $wa->registerAndUseStyle('fontawesome','media/system/css/joomla-fontawesome.css');
}
$cats_lib = array();
$cats_alias = array();
$categories = $params->get('categories');
if (is_null($categories)) {
	$res = CGAvisClientHelper:: getAllCategories();
	$categories = array();
	foreach ($res as $catid) {
			$categories[] = $catid->id;
	}
}
foreach ($categories as $catid) {
	$res = CGAvisClientHelper::getCategory($catid);
	$cats_lib[$catid]= $res[0]->title;
	$cats_alias[$catid] = $res[0]->alias;
}

$lists = CGAvisClientHelper::getList($params,$limitstart,$limit);

$defaultdisplay = $params->get('defaultdisplay_article', 'date_desc');

$sortBy = "";
if ($defaultdisplay == "date_asc")  {$sortBy = "[ 'date','cat','vote']";$asc = "true";}
if ($defaultdisplay == "date_desc") {$sortBy ="[ 'date','cat','vote']";$asc = "false";}
if ($defaultdisplay == "vote_asc")  {$sortBy = "[ 'vote','cat','date']";$asc ="true";}
if ($defaultdisplay == "vote_desc") {$sortBy ="[ 'vote','cat','date']";$asc ="false";}
if ($defaultdisplay == "cat_asc")   {$sortBy ="[ 'cat','vote','date']";$asc ="true";}
if ($defaultdisplay == "cat_desc")  {$sortBy ="[ 'cat', 'vote','date']";$asc ="false";}

$document 		= Factory::getDocument();

$document->addScriptOptions('mod_cg_avisclient', 
							array('layout' => $params->get('iso_layout', 'fitRows'),
									'sortby' => $sortBy,'asc'=>$asc)
							);

$wa->registerAndUseScript('avis',$modulefield."js/cg_avisclient.js");

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx',''));

// récupération des paramètres du module
$iso_layout = $params->get('iso_layout', 'fitRows');
$iso_nbcol = $params->get('iso_nbcol',2);
$background = $params->get("backgroundcolor","#eee");
$background = $params->get("backgroundcolor","#eee");
$backgroundvar = $params->get("backgroundcolorvar","--bs-grey");
$buttoncolor = $params->get("buttoncolor","#eee");
$buttoncolorvar = $params->get("buttoncolorvar","--bs-grey");
$defaultdisplay = $params->get('defaultdisplay_article', 'date_desc');
$displaysortinfo = $params->get('displaysortinfo', 'show');
$displayfilter =  $params->get('displayfilter','hide');
$displaysort =  $params->get('displaysort','show');  
$displaysearch = $params->get('displaysearch','show');
$intro_maxwidth = $params->get('intro_maxwidth','0');
$cat_list =  $params->get('categories');	
$cls_cgavisclient = $params->get('cls_cgavisclient','');

$button_bootstrap = "btn btn-sm ";   // classe des boutons
$col_bootstrap_sort = "col-4";    // largeur de la colonne des boutons de tri
$col_bootstrap_filter = "col-4";    // largeur de la colonne des boutons de tri

// libellés affichés
$libreverse=Text::_('MOD_CGAVISCLIENT_ISOLIBREVERSE');
$liball = Text::_('MOD_CGAVISCLIENT_ISOLIBALL');
$libdate = Text::_('MOD_CGAVISCLIENT_ISOLIBDATE');
$libcategory = Text::_('MOD_CGAVISCLIENT_ISOLIBCAT');
$libvote= Text::_('MOD_CGAVISCLIENT_ISOLIBVOTES');
$libcreated=Text::_('MOD_CGAVISCLIENT_ISOLIBCREATED'); 
$libdateformat = Text::_('MOD_CGAVISCLIENT_ISODATEFORMAT'); // format d'affichage des dates au format php d/m/Y H:i  = format français avec heure/minutes
$libsearch = "Recherche";

// génération du CSS en fonction du type d'affichage et du nb de colonnes
$style = "/* CSS module avis client */";
if (($iso_layout == "masonry") || ($iso_layout == "fitRows")|| ($iso_layout == "packery")) {
	$style .= ' .cgavisiso_item {width: '.((100 / $iso_nbcol)-2).'% } ';
	$style .= ' @media only screen and (max-width: 768px) {.cgavisiso_item {width:100%} }';
} else if ($iso_layout == "vertical") {
	$style .= ' .cgavisiso_item {width: 100%; }';
}
if ($params->get('bckg-color','pick') == 'pick') {
    $style .= '.cgavisiso_item {background:'.$background.' !important;}';
} else {
    $style .= '.cgavisiso_item {background:var('.$backgroundvar.') !important;}';
}
if ($params->get('btn-color','pick') == 'pick') {
    $style .= '.cgavisiso_button-group .btn {background-color:'.$buttoncolor.' !important;}';
} else {
    $style .= '.cgavisiso_button-group .btn {background-color:var('.$buttoncolorvar.') !important;}';
}
$wa->addInlineStyle($style);

$plusmenu = $params->get('plusmenu');
$menu = Factory::getApplication()->getMenu()->getItem($plusmenu);
$plusroute = Route::_($menu->link);

?>
<div class="cgavisiso-div row"  style ="height:auto;" >
<?php if ($displaysort == "show")  { ?>
<div class="<?php echo $col_bootstrap_sort; ?> cgavisiso_button-group sort-by-button-group ">
<?php
$checked = " is-checked ";
if ($params->get('btndate','true') == "true") {
	$sens = "-";
	$sens = $defaultdisplay=="date_desc"? "+": $sens;
	echo '<button class="'.$button_bootstrap.' iso_button_date '.$checked.$cls_cgavisclient.'" data-sort-value="date,cat,vote" data-sens="'.$sens.'" title="'.$libreverse.'">'.$libdate.'</button>';
	$checked = "";
}
if ($params->get('btnrating','true') == "true") {
	$sens = "-";
	$sens = $defaultdisplay=="vote_desc"? "+": $sens;
	echo '<button class="'.$button_bootstrap.' iso_button_cat  '.$checked.$cls_cgavisclient.'" data-sort-value="vote,date,cat" data-sens="'.$sens.'" title="'.$libreverse.'">'.$libvote.'</button>';
	$checked = "";
}
if ($params->get('btncat','true') == "true") {
	$sens = "-";
	$sens = $defaultdisplay=="cat_desc"? "+": $sens;
	echo '<button class="'.$button_bootstrap.' iso_button_cat  '.$checked.$cls_cgavisclient.'" data-sort-value="cat,vote,date" data-sens="'.$sens.'" title="'.$libreverse.'">'.$libcategory.'</button>';
	$checked = "";
}
?>
</div>
<?php } ?>
<?php 
if ($displaysearch == "show") {  ?>
	<div class="<?php echo $col_bootstrap_sort; ?> center" >
	<input type="text" class="quicksearch"  placeholder="<?php echo $libsearch;?>" />
	</div>
<?php }

if ($displayfilter == "1") {
	echo '<div class="'.$col_bootstrap_filter.'  cgavisiso_button-group filter-button-group" data-filter-group="cat">';
	echo '<button class="'.$button_bootstrap.$cls_cgavisclient.'  iso_button_cat_tout is-checked" data-sort-value="0">'.$liball.'</button>';
	foreach ($categories as $filter) {
		$aff = $cats_lib[$filter]; 
		$aff_alias = $cats_alias[$filter];
		if (!is_null($aff)) {
			echo '<button class="'.$button_bootstrap.$cls_cgavisclient.' '.$aff_alias.'" data-sort-value="'.$aff_alias.'">'.$aff.'</button>';
		}
	}
	echo '</div>';

}
if($params->get('add_cgavisclient')!=0){?>   
<div class="iso_propose_1 col-4" style = "margin-left:auto;margin-right:auto">
    <?php 
	$lacat="";
	if ($cat_list && (count($cat_list) == 1)) // une seule catégorie 
    { $lacat = "&cat=".$cat_list[0]; }
	?>
			<a href="<?php echo Route::_('index.php?option=com_cgavisclient&view=item'.$lacat); ?>" class="btn 
			<?php if ($cls_cgavisclient != '') : echo ' '.$cls_cgavisclient;endif;?>
			"><?php echo Text::_('MOD_CGAVISCLIENT_ISO_PROPOSE');?></a>
			</button>
</div>
<?php }
?>

</div>
<div id="cgavisiso_<?php echo $module->id; ?>" class="cg_avisclient<?php echo $moduleclass_sfx; ?>">
    <div class="cgavisiso_grid">
        <?php foreach ($lists as $key=>$item) : ?>
		<?php	
 
			$cat = CGAvisClientHelper::getCategory($item->category);
			echo '<div class="cgavisiso_item iso_cat_'.$cat[0]->alias.' '.$cat[0]->alias.'"  data-cat="'.$cat[0]->alias.'" data-vote="'.$item->rating.'" data-date="'.$item->created.'">';
			?>
			<div class="cg_tcontent1">
                <div class="cg_title">
				<?php 
				$comment = $item->comment;
				if ($intro_maxwidth > 0)  {
            		$trunc = CGAvisClientHelper::truncate($comment,$intro_maxwidth);
                    HTMLHelper::_('bootstrap.collapse', '.selector', []);
					if (substr($trunc,strlen($trunc) - 3,3) == "...") {
						$comment = '<div class="intr_panel collapse show" id="intro'.$item->id.'">'.$trunc;
						$comment = str_replace('</p>...','...</p>',$comment);
						$comment .= '<button id="'.$item->id.'" class="btn btnsuite">Lire la suite...</button></div>';
						$comment .= '<div class="acc_panel collapse" id="panel'.$item->id.'">';
						$comment .= $item->comment."<i class='fa fa-quote-right'></i></div>";
					} else {
                        $comment .= '<i class="fa fa-quote-right"></i>';
                    }
				} else {
                    $comment .= '<i class="fa fa-quote-right"></i>';
                }
				echo '<i class="fa fa-quote-left"></i>'.$comment; ?>
		        </div>                     
            </div>
			<?php 
			$aff = $params->get('aff');
			?>
            <div class="cg_info1">
                <div class="cg_aditional1 row" style="display: inherit">
				<?php 
				$libdateformat = Text::_('MOD_CGAVISCLIENT_DATEFORMAT'); 
				$stars = '</div><div class="cg_ratting col-5"';
				$stars .=' style = "float:right;margin-top:-1em" ';
				$stars .= ">";
				for ($j = 0; $j < $item->rating; $j++) { 
					$stars .= '<i class="fa '.$sf_icon.'" style="color:gold"></i> ';
				} 
				$stars .= '</div> ';
				$deb = '<div class="cg_name col-7" ';
				$deb .=' style = "padding:0;" ';
				$deb .= '>';
				$perso = $params->get('perso');
				$arr_css= array("{name}"=>$item->name,"{first}"=>$item->firstname,"{cat}"=>$cat[0]->title,"{date}"=>HTMLHelper::_('date', $item->created, $libdateformat), "{stars}" =>$stars, "{zip}" => $item->zipcode, "{city}" => $item->city);
				foreach ($arr_css as $key => $val) {
					$perso = str_replace($key,$val,$perso);
				}
				echo $deb.$perso.'</div>';
				?>
				</div>
			</div>
            <?php endforeach; ?>
		</div>
    </div>
    <div class="row">
	<?php if($params->get('add_cgavisclient')!=0){?>   
<div class="iso_propose_2 col-5 center" style="margin-left:auto;margin-right:auto">
			<a href="<?php echo Route::_('index.php?option=com_cgavisclient&view=item'.$lacat) ?>" class="btn 
			<?php if ($cls_cgavisclient != '') : echo ' '.$cls_cgavisclient;endif;?>
			"><?php echo Text::_('MOD_CGAVISCLIENT_ISO_PROPOSE');?></a>
			</button>
</div>
<?php }?>
	<?php if($params->get('plus_cgavisclient')!=0){?>   
<div class="iso-plus center col-5" style="margin-top:10px;display: inherit; margin-left:2em;">
			<a href="<?php echo $plusroute; ?>" class="btn 
			<?php if ($cls_cgavisclient != '') : echo ' '.$cls_cgavisclient;endif;?>
			"><?php echo Text::_('MOD_CGAVISCLIENT_ISO_SEEMORE');?></a>
			</button>
</div>
<?php }?>
    </div>
