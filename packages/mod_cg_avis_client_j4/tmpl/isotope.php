<?php 
/**
* CG Avis Client - Joomla Module 
* Version			: 1.1.2
* Package			: Joomla 3.x.x
* copyright 		: Copyright (C) 2017 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* From              : OT Testimonies  version 1.0, OmegaTheme Extensions - http://omegatheme.com
* Updated on        : July, 2018
*/

// no direct access
defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Text;
use ConseilGouz\Module\CGAvisClient\Site\Helper\CGPagination;

HTMLHelper::_('jquery.framework');

if($params->get('add_pagination')!=0){ // gestion de la pagination
	$total = CGAvisClientHelper::getTotal($params);
	$pagination = new CGPagination($total, $limitstart, $limit,'',null,$params);
}
// récupération des paramètres du module
$iso_layout = $params->get('iso_layout', 'fitRows');
$iso_nbcol = $params->get('iso_nbcol',2);
$background = $params->get("backgroundcolor","#eee");
$buttoncolor = $params->get("buttoncolor","#eee");
$defaultdisplay = $params->get('defaultdisplay_article', 'date_desc');
$displaysortinfo = $params->get('displaysortinfo', 'show');
$displayfilter =  $params->get('displayfilter','hide');
$displaysort =  $params->get('displaysort','show');  
$displaysearch = $params->get('displaysearch','show');
$intro_maxwidth = $params->get('intro_maxwidth','0');
$cat_list =  $params->get('categories');	
$cls_spavisclient = $params->get('cls_spavisclient','');

$button_bootstrap = "btn btn-sm ";   // classe des boutons
$col_bootstrap_sort = "col-md-4 col-xs-5";    // largeur de la colonne des boutons de tri

// libellés affichés
$libreverse=Text::_('MOD_CGAVISCLIENT_ISOLIBREVERSE');
$liball = Text::_('MOD_CGAVISCLIENT_ISOLIBALL');
$libdate = Text::_('MOD_CGAVISCLIENT_ISOLIBDATE');
$libcategory = Text::_('MOD_CGAVISCLIENT_ISOLIBCAT');
$libvote= Text::_('MOD_AVISCLIENT_ISOLIBVOTES');
$libcreated=Text::_('MOD_CGAVISCLIENT_ISOLIBCREATED'); 
$libdateformat = Text::_('MOD_CGAVISCLIENT_ISODATEFORMAT'); // format d'affichage des dates au format php d/m/Y H:i  = format français avec heure/minutes
$libsearch = "Recherche";

// génération du CSS en fonction du type d'affichage et du nb de colonnes
echo '<style type="text/css">';
if (($iso_layout == "masonry") || ($iso_layout == "fitRows")|| ($iso_layout == "packery")) {
	echo ' .isotope_item {width: '.((100 / $iso_nbcol)-2).'% } ';
	echo ' @media only screen and (max-width: 768px) {.isotope_item {width:100%} }';
} else if ($iso_layout == "vertical") {
	echo ' .isotope_item {width: 100%; }';
}
echo '.isotope_item {background:'.$background.' !important;}';
echo '.isotope_button-group .btn {background-color:'.$buttoncolor.';}';
echo '</style>';

$plusmenu = $params->get('plusmenu');
$menu = Factory::getApplication()->getMenu()->getItem($plusmenu);
$plusroute = Route::_($menu->link);

?>
<div class="isotope-div row"  style ="height:3em;" >
<?php if ($displaysort == "show")  { ?>
<div class="<?php echo $col_bootstrap_sort; ?> isotope_button-group sort-by-button-group " style = "width:30%;float:left;margin-left:15px;">
<?php
$checked = " is-checked ";
if ($params->get('btndate','true') == "true") {
	$sens = "-";
	$sens = $defaultdisplay=="date_desc"? "+": $sens;
	echo '<button class="'.$button_bootstrap.' iso_button_date '.$checked.$cls_spavisclient.'" data-sort-value="date,cat,vote" data-sens="'.$sens.'" title="'.$libreverse.'">'.$libdate.'</button>';
	$checked = "";
}
if ($params->get('btnrating','true') == "true") {
	$sens = "-";
	$sens = $defaultdisplay=="vote_desc"? "+": $sens;
	echo '<button class="'.$button_bootstrap.' iso_button_cat  '.$checked.$cls_spavisclient.'" data-sort-value="vote,date,cat" data-sens="'.$sens.'" title="'.$libreverse.'">'.$libvote.'</button>';
	$checked = "";
}
if ($params->get('btncat','true') == "true") {
	$sens = "-";
	$sens = $defaultdisplay=="cat_desc"? "+": $sens;
	echo '<button class="'.$button_bootstrap.' iso_button_cat  '.$checked.$cls_spavisclient.'" data-sort-value="cat,vote,date" data-sens="'.$sens.'" title="'.$libreverse.'">'.$libcategory.'</button>';
	$checked = "";
}
?>
</div>
<?php } ?>
<?php 
if ($displaysearch == "show") {  ?>
	<div class="<?php echo $col_bootstrap_sort; ?> center" style = "width:30%;float:left;margin-left:15px;">
	<input type="text" class="quicksearch" style="margin-left: -15%" placeholder="<?php echo $libsearch;?>" />
	</div>
<?php }

if($params->get('add_cgavisclient')!=0){?>   
<div class="iso_propose_1 col-md-4 col-xs-5 center" style = "width:30%;float:left;margin-left:15px;">
    <?php 
	$lacat="";
	if (count($cat_list) == 1) // une seule catégorie 
    { $lacat = "&cat=".$cat_list[0]; }
	?>
			<a href="<?php echo Route::_('index.php?option=com_cgavisclient'.$lacat); ?>" class="btn 
			<?php if ($cls_cgavisclient != '') : echo ' '.$cls_spavisclient;endif;?>
			"><?php echo Text::_('MOD_CGAVISCLIENT_ISO_PROPOSE');?></a>
			</button>
</div>
<?php }
if ($displayfilter == "1") {
	echo '<div class="'.$col_bootstrap_filter.'  isotope_button-group filter-button-group" data-filter-group="cat">';
	echo '<button class="'.$button_bootstrap.$cls_spavisclient.'  iso_button_cat_tout is-checked" data-sort-value="0" />'.$liball.'</button>';
	foreach ($categories as $filter) {
		$aff = $cats_lib[$filter]; 
		$aff_alias = $cats_alias[$filter];
		if (!is_null($aff)) {
			echo '<button class="'.$button_bootstrap.$cls_spavisclient.' '.$aff_alias.'" data-sort-value="'.$aff_alias.'" />'.$aff.'</button>';
		}
	}
	echo '</div>';

}

?>
</div>
<div id="cg_avisclient_<?php echo $module->id; ?>" class="cg_avisclient<?php echo $moduleclass_sfx; ?>">
    <div class="isotope_grid">
        <?php foreach ($lists as $key=>$item) : ?>
		<?php	
 
			$cat = CGAvisClientHelper::getCategory($item->category);
			echo '<div class="isotope_item iso_cat_'.$cat[0]->alias.' '.$cat[0]->alias.'"  data-cat="'.$cat[0]->alias.'" data-vote="'.$item->rating.'" data-date="'.$item->created.'">';
			?>
			<div class="sp_tcontent1">
                <div class="sp_title">
				<i class="ic-quote"></i>
				<?php 
				$comment = $item->comment;
				if ($intro_maxwidth > 0)  {
            		$trunc = CGAvisClientHelper::truncate($comment,$intro_maxwidth);
					if (substr($trunc,strlen($trunc) - 3,3) == "...") {
						$comment = '<div class="intr_panel" id="intro'.$item->id.'">'.$trunc;
						$comment = str_replace('</p>...','...</p>',$comment);
						$comment .= '<i class="ic-quote-2"></i>';
						$comment .= '<button id="'.$item->id.'" class="btn btnsuite">Lire la suite...</button></div>';
						$comment .= '<div class="acc_panel" id="panel'.$item->id.'" style="display:none">';
						$comment .= $item->comment."<i class='ic-quote-2'></i></div>";
					} else { // ajout du quote final
						$comment .= '<i class="ic-quote-2"></i>';
					}
				} else { // ajout du quote final
					$comment .= '<i class="ic-quote-2"></i>';
				}
				echo $comment; ?>
				
		        </div>                     
            </div>
			<?php 
			$aff = $params->get('aff');
			?>
            <div class="sp_info1">
                <div class="sp_aditional1 row" style="display: inherit">
				<?php 
				$libdateformat = Text::_('MOD_CGAVISCLIENT_DATEFORMAT'); 
				$stars = '</div><div class="sp_ratting col-xs-12 col-sm-5"';
				$stars .=' style = "width:30%;float:right;" ';
				$stars .= ">";
				for ($j = 0; $j < $item->rating; $j++) { 
					$stars .= '<i class="ic-featured" style="color:gold"></i> ';
				} 
				$stars .= '</div> ';
				$deb = '<div class="sp_name col-xs-12 col-sm-7" ';
				$deb .=' style = "width:65%;float:left;margin-left:15px;" ';
				$deb .= '>';
				$perso = $params->get('perso');
				$arr_css= array("{name}"=>$item->name,"{first}"=>$item->firstname,"{cat}"=>$cat[0]->title,"{date}"=>$libcreated.HTMLHelper::_('date', $item->created, $libdateformat), "{stars}" =>$stars, "{zip}" => $item->zipcode, "{city}" => $item->city);
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
	<?php if($params->get('add_pagination')!=0){?> 
	<div class="isotope-pagination">	
	<?php echo $pagination->getPagesLinks($params); ?>
    </div>
	<?php } ?>
	<?php if($params->get('add_cgavisclient')!=0){?>   
<p class="iso_propose_2 center" style="margin-top:10px;display: inherit; margin-left:2em;">
			<a href="<?php echo JRoute::_('index.php?option=com_cgavisclient'.$lacat) ?>" class="btn 
			<?php if ($cls_cgavisclient != '') : echo ' '.$cls_spavisclient;endif;?>
			"><?php echo Text::_('MOD_CGAVISCLIENT_ISO_PROPOSE');?></a>
			</button>
</p>
<?php }?>
	<?php if($params->get('plus_cgavisclient')!=0){?>   
<p class="iso-plus center" style="margin-top:10px;display: inherit; margin-left:2em;">
			<a href="<?php echo $plusroute; ?>" class="btn 
			<?php if ($cls_cgavisclient != '') : echo ' '.$cls_cgavisclient;endif;?>
			"><?php echo Text::_('MOD_CGAVISCLIENT_ISO_SEEMORE');?></a>
			</button>
</p>
<?php }?>
	
<script>
var qsRegex;
var filters = ['*'];
var $grid = jQuery('.isotope_grid').imagesLoaded( 
   function() {
	$grid.isotope({ 
     itemSelector: '.isotope_item',
     percentPosition: true,
  <?php 
	echo "layoutMode:'".$iso_layout."',"; 
	?>
  getSortData: {
	date: '[data-date]',
    cat: '[data-cat]',	
	vote: '[data-vote] parseInt'
  },
<?php

  if ($defaultdisplay == "date_asc")  echo "sortBy: [ 'date','cat','vote'], sortAscending: true";
  if ($defaultdisplay == "date_desc")  echo "sortBy: [ 'date','cat','vote'], sortAscending: false";
  if ($defaultdisplay == "vote_asc")   echo "sortBy: [ 'vote','cat','date'], sortAscending: true";
  if ($defaultdisplay == "vote_desc")   echo "sortBy: [ 'vote','cat','date'], sortAscending: false";
  if ($defaultdisplay == "cat_asc")   echo "sortBy: [ 'cat','vote','date'], sortAscending: true";
  if ($defaultdisplay == "cat_desc")   echo "sortBy: [ 'cat', 'vote','date'], sortAscending: false";
?>
	,
  filter: function() {
    var $this = jQuery(this);
    var searchResult = qsRegex ? $this.text().match( qsRegex ) : true;
	var laclasse = $this.attr('class');
	var lescles = laclasse.split(" ");
	var buttonResult = false;
	if (filters.indexOf('*') != -1) { buttonResult = true};
	for (var i in lescles) {
		if (filters.indexOf(lescles[i]) != -1) {
			buttonResult = true;
		}
	}
    return searchResult && buttonResult;
  }
 });
 });

// bind sort button click
jQuery('.sort-by-button-group').on( 'click', 'button', function() {
 var sortValue = jQuery(this).attr('data-sort-value'),
 sens = jQuery(this).attr('data-sens');
  sortValue = sortValue.split(',');
  if (sens == "+") {
	jQuery(this).attr("data-sens","-");
    asc = true;
	} else {
  	jQuery(this).attr("data-sens","+");
	asc = false;
  }
  $grid.isotope({ 
	sortBy: sortValue, 
	sortAscending: asc,
  });
});

jQuery('.sort-by-button-group').each( function( i, buttonGroup ) {
  var $buttonGroup = jQuery( buttonGroup );
  $buttonGroup.on( 'click', 'button', function() {
    $buttonGroup.find('.is-checked').removeClass('is-checked');
    jQuery( this ).addClass('is-checked');
  });
});

// use value of search field to filter
var $quicksearch = jQuery('.quicksearch').keyup( debounce( function() {
  qsRegex = new RegExp( $quicksearch.val(), 'gi' );
  $grid.isotope();
}) );

// debounce so filtering doesn't happen every millisecond
function debounce( fn, threshold ) {
  var timeout;
  return function debounced() {
    if ( timeout ) {
      clearTimeout( timeout );
    }
    function delayed() {
      fn();
      timeout = null;
    }
    timeout = setTimeout( delayed, threshold || 100 );
  }  
  }
<?php if ($displayfilter == "1") { ?>
jQuery('.filter-button-group').on( 'click', 'button', function() {
  var sortValue = jQuery(this).attr('data-sort-value');
  if (sortValue == 0) {
   filters = ['*'];
  } else
  { 
   filters = [sortValue];
   }
   $grid.isotope(); 
});

jQuery('.filter-button-group').each( function( i, buttonGroup ) {
  var $buttonGroup = jQuery( buttonGroup );
  $buttonGroup.on( 'click', 'button', function() {
    $buttonGroup.find('.is-checked').removeClass('is-checked');
    jQuery( this ).addClass('is-checked');
  });
});
<?php } ?>

jQuery('button.btnsuite').click( function() {
				var id = jQuery(this).attr('id');
				panel = 'panel'+id;
				intro = 'intro'+id;
				jQuery('#'+intro).toggle(400);
				jQuery('#'+panel).toggle(400);
				jQuery(this).toggleClass('active');
				// mise à jour de l'affichage isotope
				$grid.isotope('layout');
				setTimeout( function() {
					$grid.isotope('layout');
					}, 1000 );

			});
</script>	
