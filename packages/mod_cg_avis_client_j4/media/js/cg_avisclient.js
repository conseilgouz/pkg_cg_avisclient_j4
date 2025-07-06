/**
* CG Avis Client - Joomla Module 
* Package			: Joomla 4.x/5.x
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
* From              : OT Testimonies  version 1.0, OmegaTheme Extensions - http://omegatheme.com
*/
document.addEventListener('DOMContentLoaded', function() {
	if (typeof Joomla === 'undefined' || typeof Joomla.getOptions === 'undefined') {
		console.log('CG Avis Client : Joomla /Joomla.getOptions  undefined');
	} else {
		 options = Joomla.getOptions('mod_cg_avisclient');
	}
	var cgavisqsRegex;
	var cgavisfilters = ['*'];
	var cgavisgrid = document.querySelector('.cgavisiso_grid');
    
    var cgavisiso = new Isotope(cgavisgrid,{ 
				itemSelector: '.cgavisiso_item',
				percentPosition: true,
				layoutMode:options.layout, 
				getSortData: {
					date: '[data-date]',
					cat: '[data-cat]',	
					vote: '[data-vote] parseInt'
				},
				sortBy: options.sortby,
				sortAscending: options.asc,
                isJQueryFiltering : false,
				filter: function(itemElem) {
                    if (!itemElem) return true;
					var searchResult = cgavisqsRegex ? itemElem.textContent.match( cgavisqsRegex ) : true;
					var laclasse = itemElem.getAttribute('class');
					var lescles = laclasse.split(" ");
					var buttonResult = false;
					if (cgavisfilters.indexOf('*') != -1) { buttonResult = true};
					for (var i in lescles) {
						if (cgavisfilters.indexOf(lescles[i]) != -1) {
							buttonResult = true;
						}
					}
					return searchResult && buttonResult;
				}
	});
    imagesLoaded(cgavisgrid, function() {
        cgavisiso.arrange();
	});
// bind sort button click
	var cgasortbybutton = document.querySelectorAll('.cgavisiso-div .sort-by-button-group button');
	for (var i=0; i< cgasortbybutton.length;i++) {
		['click', 'touchstart'].forEach(type => {
			cgasortbybutton[i].addEventListener(type,e => {
                let sortValue = e.srcElement.getAttribute('data-sort-value');
                sens = e.srcElement.getAttribute('data-sens');
                sortValue = sortValue.split(',');
                if (sens == "+") {
                    e.srcElement.setAttribute("data-sens","-");
                    asc = true;
                } else {
                    e.srcElement.setAttribute("data-sens","+");
                    asc = false;
                }
                cgavisiso.options.sortBy = sortValue, 
                cgavisiso.options.sortAscending = asc;
                cgavisiso.arrange();
            });
        });
    }
	var cgasortbybuttons = document.querySelectorAll('.cgavisiso-div .sort-by-button-group button');
	for (var i=0; i< cgasortbybuttons.length;i++) {
		['click', 'touchstart'].forEach(type => {
			cgasortbybuttons[i].addEventListener(type,e => {
				for (var j=0; j< cgasortbybuttons.length;j++) {
					cgasortbybuttons[j].classList.remove('is-checked');
				}
				e.srcElement.classList.add('is-checked');
			});
		})
	}
    
// use value of search field to filter
	var cgavisquicksearch = document.querySelector('.cgavisiso-div .quicksearch');
 	if (cgavisquicksearch) {
		cgavisquicksearch.addEventListener('keyup',e => {
            cgavisquicksearch = document.querySelector('.quicksearch');
			cgavisqsRegex = new RegExp(cgavisquicksearch.value, 'gi' );
            cgavisiso.arrange();
		});
	}
	var cgafilterbybutton = document.querySelectorAll('.cgavisiso-div .filter-button-group');
	for (var i=0; i< cgafilterbybutton.length;i++) {
		['click', 'touchstart'].forEach(type => {
			cgafilterbybutton[i].addEventListener(type,e => {
                let sortValue = e.srcElement.getAttribute('data-sort-value');
                if (sortValue == 0) {
                    cgavisfilters = ['*'];
                } else  {
                    cgavisfilters = [sortValue];
                }
                cgavisiso.arrange();
            })
        })
    }
	var cgafilterbybuttons = document.querySelectorAll('.cgavisiso-div .filter-button-group button');
	for (var i=0; i< cgafilterbybuttons.length;i++) {
		['click', 'touchstart'].forEach(type => {
			cgafilterbybuttons[i].addEventListener(type,e => {
				for (var j=0; j< cgafilterbybuttons.length;j++) {
					cgafilterbybuttons[j].classList.remove('is-checked');
				}
				e.srcElement.classList.add('is-checked');
			});
		})
	}    
	var suite = document.querySelectorAll('.cg_avisclient .btn.btnsuite');
	for (var i=0; i< suite.length;i++) {
		['click', 'touchstart'].forEach(type => {
			suite[i].addEventListener(type,e => {
                let id = e.currentTarget.getAttribute('id');
                panel = '#panel'+id;
                intro = '#intro'+id;
                document.querySelector(intro).classList.remove('show');
                document.querySelector(panel).classList.add('show');
                e.currentTarget.style.display = 'none';
		// mise à jour de l'affichage isotope
                cgavisiso.layout();
                setTimeout( function() {
                    cgavisiso.layout();
                    }, 1000 );
            });
        });
    }
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
})