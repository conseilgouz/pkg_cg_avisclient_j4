/**
* CG Avis Client - Joomla Module 
* Version			: 2.0.0
* Package			: Joomla 4.x.x
* copyright 		: Copyright (C) 2021 ConseilGouz. All rights reserved.
* license    		: http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
* From              : OT Testimonies  version 1.0, OmegaTheme Extensions - http://omegatheme.com
*/
jQuery(document).ready(function($) {

	if (typeof Joomla === 'undefined' || typeof Joomla.getOptions === 'undefined') {
		console.log('CG Avis Client : Joomla /Joomla.getOptions  undefined');
	} else {
		 options = Joomla.getOptions('mod_cg_avisclient');
	}
	var qsRegex;
	var filters = ['*'];
	var $grid = jQuery('.isotope_grid').imagesLoaded( 
		function() {
			$grid.isotope({ 
				itemSelector: '.isotope_item',
				percentPosition: true,
				layoutMode:options.layout, 
				getSortData: {
					date: '[data-date]',
					cat: '[data-cat]',	
					vote: '[data-vote] parseInt'
				},
				sortBy: options.sortby,
				sortAscending: options.asc,
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
	jQuery('.filter-button-group').on( 'click', 'button', function() {
		var sortValue = jQuery(this).attr('data-sort-value');
		if (sortValue == 0) {
			filters = ['*'];
		} else  { 
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
})