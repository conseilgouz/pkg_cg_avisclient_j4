/**
* CG Avis Scroll - Joomla Module 
* Package			: Joomla 3.10.x - 4.x - 5.x
* copyright 		: Copyright (C) 2025 ConseilGouz. All rights reserved.
* license    		: https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
*/
var cgavisscroll = [];

document.addEventListener('DOMContentLoaded', function() {
    mains = document.querySelectorAll('.cg_avis_scroll');
    for(var i=0; i<mains.length; i++) {
        myid = mains[i].getAttribute("data");
        if (typeof Joomla === 'undefined' || typeof Joomla.getOptions === 'undefined') {
            console.error('Joomla.getOptions not found!\nThe Joomla core.js file is not being loaded.');
            return false;
        }
        ascroll = "#cg_avis_scroll_"+myid+" ";
        cgscroll_options = Joomla.getOptions('mod_cg_avis_scroll_'+myid);
        if (typeof cgscroll_options === 'undefined' ) {return false}
        cgavisscroll[myid] = new CGAvisScroll(myid,ascroll,cgscroll_options);
        cgavisscroll[myid].go_avis_scroll(myid);
    }
});
function CGAvisScroll(myid,me,options) {
	this.options = options;
	this.myid = myid;
	this.me = me;
	this.container = document.querySelector(this.me + '#sfdmarqueecontainer');
    this.container.scrollBehavior = 'smooth';
	this.cross_marquee = document.querySelector(this.me + '#vmarquee');
	this.items_ul_0 = document.querySelector(this.me + 'ul.cg-scroll-items-0');
	this.items_ul_1 = document.querySelector(this.me + 'ul.cg-scroll-items-1');
	this.container.style.height = this.options.height+"px";

	this.pauseit=this.options.pause;
	this.delay=parseInt(this.options.delay);

    if (this.options.speed > 4) {
        this.speed = this.options.speed - 4;
        this.slowdown = 0;
    } else { 
        this.slowdown = 4 - this.options.speed;
        this.speed = 1;
    }
    
    ico = document.querySelector(this.me +"#toDirection")
    if (ico) ico.style.display = 'block';
	this.me_up = document.querySelector(this.me + ".icon-dir-up");
	this.me_down = document.querySelector(this.me + ".icon-dir-down");
	this.me_left = document.querySelector(this.me + ".icon-dir-left");
	this.me_right = document.querySelector(this.me + ".icon-dir-right");
	
	items = document.querySelectorAll(me + 'ul li');
	$total_width = 0;
	for(var i=0; i<items.length; i++) {
		if (this.options.direction == 1) { // vertical scroll
			items[i].style.width = "auto";
		} else { // horizontal scroll
			items[i].style.float = "left";
			items[i].style.height = "100%";
			// items[i].style.listStyleType = "circle";
		    if (this.options.width == 0) {
				items[i].style.width = "auto";
				items[i].style.margin = "12px";
			} else {
				items[i].style.width = this.options.width+"px";
				items[i].style.margin = "12px";
			}
			$total_width += items[i].clientWidth + 24 + 1; // add margin + rounded value
		}
	}
	if (this.options.direction != 1) { // horizontal scroll
		$total_width = $total_width / 2; // on a doublé les articles
		this.cross_marquee.style.float = "left";
		this.cross_marquee.style.width = $total_width+"px";
		this.cross_marquee.style.height =  this.options.height+"%";
		this.items_ul_0.style.width = $total_width + "px"; 
		this.items_ul_1.style.width = $total_width + "px"; 
	}
}
CGAvisScroll.prototype.go_avis_scroll = function (myid) {
	$this = cgavisscroll[myid];

    $this.active = false;
    $this.currentX = 0;
    $this.currentY = 0;
    $this.initialX = 0;
    $this.initialY = 0;
    $this.xOffset = 0;
    $this.yOffset = 0;
    
	var suite = document.querySelectorAll('.cg_avis_scroll .btn.btnsuite');
	for (var i=0; i< suite.length;i++) {
		['click', 'touchstart'].forEach(type => {
			suite[i].addEventListener(type,e => {
                let id = e.currentTarget.getAttribute('id');
                panel = '#scpanel'+id;
                intro = '#scintro'+id;
                intros = document.querySelectorAll(intro);
                for (var s=0; s< intros.length;s++) {
                    intros[s].classList.remove('show');
                }
                panels = document.querySelectorAll(panel);
                for (var s=0; s< panels.length;s++) {
                    panels[s].classList.add('show');
                    if ($this.options.direction == '0') {
                        panels[s].style.overflow = "auto";
                        nameHeight = (panels[s].parentNode.querySelector('.cg_name').offsetHeight) + 2;
                        panels[s].style.height = ($this.options.height - nameHeight - 40)+'px';
                    }
                }
                e.currentTarget.style.display = 'none';
            });
        });
    }
    if ($this.options.scrolltype == 'lines') {
         cgavisscroll[myid].go_avis_lines(myid);
    } else { // blocks
        cgavisscroll[myid].go_avis_blocks(myid);
    }
}
CGAvisScroll.prototype.go_avis_lines = function (myid) {

	$this = cgavisscroll[myid];
    // drag move 
    $this.container.addEventListener("touchstart", $this.dragStart, false);
    $this.container.addEventListener("touchmove", $this.drag, false);
    document.addEventListener("touchend", $this.dragEnd, false);
    document.addEventListener("touchcancel", $this.dragEnd, false);

    $this.container.addEventListener("mousedown", $this.dragStart, false);
    $this.container.addEventListener("mousemove", $this.drag, false);
    document.addEventListener("mouseup", $this.dragEnd, false);
    document.addEventListener("mouseleave", $this.dragEnd, false);
    // drag move end
	if ($this.options.direction == 1) {
        translate = "translateY"; // up/down
        height = parseInt($this.items_ul_0.clientHeight);
        if ($this.items_ul_1.clientHeight != height) $this.items_ul_1.style.height = height+'px'; // adjust 2nd ul height
        duration = (height * (1/$this.speed)) * (50 + (15 * $this.slowdown));
    } else {
        translate = "translateX"; // left/right
        width = parseInt($this.items_ul_0.clientWidth);
        duration = (width * (1/$this.speed)) * (50 + (15 * $this.slowdown));
    }
    direction = "normal";
    $this.effect0 = new KeyframeEffect(
       $this.items_ul_0, // element to animate
        [
            { transform: translate+"(0%)" }, // keyframe
            { transform: translate+"(-100%)" }, // keyframe
        ],
        { direction:direction,duration: duration,iterations : 9999,delay:0}, // keyframe options
    );
    $this.effect1 = new KeyframeEffect(
        $this.items_ul_1, // element to animate
        [
            { transform: translate+"(100%)" }, // keyframe
            { transform: translate+"(0%)" }, // keyframe
        ],
        { direction:direction,duration: duration,iterations : 9999,delay:0}, // keyframe options
    );
    $this.animation0 = new Animation(
        $this.effect0,
        document.timeline,
    );
    $this.animation1 = new Animation(
        $this.effect1,
        document.timeline,
    );
    setTimeout((me) => {
        me.animation1.play();
        me.animation0.play();
    },$this.delay,$this); 
    
    if ($this.pauseit == "1") { // enable pause on mouse over ?
        $this.container.addEventListener('mouseover',function() {
            id = this.getAttribute('data');
            cgavisscroll[id].animation0.pause();
            cgavisscroll[id].animation1.pause();
        })
        $this.container.addEventListener('mouseout',function() {
            id = this.getAttribute('data');
            cgavisscroll[id].animation0.play();
            cgavisscroll[id].animation1.play();
        })
	}
	if ($this.me_up) {
		$this.me_up.style.display = "none";
		$this.me_up.addEventListener("click",function() {
			id = this.parentNode.parentNode.getAttribute('data');
			$this = cgavisscroll[id];
            cgavisscroll[id].animation0.reverse();
            cgavisscroll[id].animation1.reverse();
			$this.me_up.style.display = "none";
			$this.me_down.style.display = "block";
			return false;
		});	
		$this.me_down.addEventListener("click",function() {
			id = this.parentNode.parentNode.getAttribute('data');
			$this = cgavisscroll[id];
            cgavisscroll[id].animation0.reverse();
            cgavisscroll[id].animation1.reverse();
			$this.me_down.style.display = "none";
			$this.me_up.style.display = "block";
			return false;
		});
	}
	if ($this.me_left) {
		$this.me_left.style.display = "none";
		$this.me_left.addEventListener("click",function() {
			id = this.parentNode.parentNode.getAttribute('data');
			$this = cgavisscroll[id];
            cgavisscroll[id].animation0.reverse();
            cgavisscroll[id].animation1.reverse();
			$this.me_left.style.display = "none";
			$this.me_right.style.display = "block";
			return false;
		});
		$this.me_right.addEventListener('click', function() {
			id = this.parentNode.parentNode.getAttribute('data');
			$this = cgavisscroll[id];
            cgavisscroll[id].animation0.reverse();
            cgavisscroll[id].animation1.reverse();
			$this.me_left.style.display = "block";
			$this.me_right.style.display = "none";
			return false;
		});
	}
	if ($this.options.direction == 1) {
		if ($this.me_down) $this.me_down.style.display = "block";
	} else {
        if ($this.me_right) $this.me_right.style.display = "block";
	}
    this.animation0.onfinish = e => {
        e.currentTarget.play(); // restart it
    };
    this.animation1.onfinish = e => {
        e.currentTarget.play(); // restart it
    };
}
CGAvisScroll.prototype.go_avis_blocks = function (myid) {
	$this = cgavisscroll[myid];
    $this.currentLi = 0;
    $this.currentPos = 0;
	if ($this.me_up) {
        $this.items_ul_1.style.position = 'relative';
        $this.me_up.parentNode.style.left = "80%";
		$this.me_up.addEventListener("click",function() {
			id = this.parentNode.parentNode.getAttribute('data');
			$this = cgavisscroll[id];
            items = document.querySelectorAll($this.me + 'ul li');
            let elementheight = 0;
            if ($this.currentLi <= 0) { // end of list : restart to top position
                $this.currentLi = 0; 
                elementheight = parseInt(items[$this.currentLi].getBoundingClientRect().height)
                $this.currentPos = parseInt($this.items_ul_0.clientHeight) + elementheight;
                $this.currentLi = (items.length/2)+1; 
                elementheight = items[$this.currentLi].getBoundingClientRect().height;
                items[$this.currentLi].parentElement.parentElement.style.transition = "transform 0s";
                $this.currentPos -= elementheight;
                items[$this.currentLi].parentElement.parentElement.style.transform = 'translateY(-'+$this.currentPos+'px)';
                $this.currentLi -= 1;
            } 
            elementheight = items[$this.currentLi].getBoundingClientRect().height ;
            items[$this.currentLi].parentElement.parentElement.style.transition = "transform 1s";
            $this.currentPos -= elementheight;
            items[$this.currentLi].parentElement.parentElement.style.transform = 'translateY(-'+$this.currentPos+'px)';
            $this.currentLi -= 1;
            return false;
		});	
		$this.me_down.addEventListener("click",function() {
			id = this.parentNode.parentNode.getAttribute('data');
			$this = cgavisscroll[id];
            items = document.querySelectorAll($this.me + 'ul li');
            let elementheight = 0;
            if ($this.currentLi >= (items.length/2) ) { // end of list : restart to top position
                $this.currentLi = 0; 
                $this.currentPos = 0;
                items[$this.currentLi].parentElement.parentElement.style.transition = "transform 0s";
                items[$this.currentLi].parentElement.parentElement.style.transform = 'translateY(-'+$this.currentPos+'px)';
            }
            elementheight = items[$this.currentLi].getBoundingClientRect().height;
            items[$this.currentLi].parentElement.parentElement.style.transition = "transform 1s";
            $this.currentPos += elementheight;
            items[$this.currentLi].parentElement.parentElement.style.transform = 'translateY(-'+$this.currentPos+'px)';
            $this.currentLi += 1;
		});
	}
	if ($this.me_left) {
        $this.items_ul_1.style.position = 'absolute';
        $this.items_ul_1.style.left = $this.items_ul_1.getBoundingClientRect().width+'px';
		// $this.me_left.style.display = "none";
		$this.me_right.addEventListener('click', function(e) {
			id = this.parentNode.parentNode.getAttribute('data');
			$this = cgavisscroll[id];
            items = document.querySelectorAll($this.me + 'ul li');
            let elementwidth = 0;
            if ($this.currentLi >= (items.length/2) || $this.currentLi < 0) { // end of list : restart to top position
                $this.currentLi = 0; 
                $this.currentPos = 0;
                items[$this.currentLi].parentElement.parentElement.style.transition = "transform 0s";
                items[$this.currentLi].parentElement.parentElement.style.transform = 'translateX(-'+$this.currentPos+'px)';
            }
            elementwidth = parseInt(items[$this.currentLi].style.marginLeft) + items[$this.currentLi].getBoundingClientRect().width + parseInt(items[$this.currentLi].style.marginRight);
            items[$this.currentLi].parentElement.parentElement.style.transition = "transform 1s";
            $this.currentPos += elementwidth;
            items[$this.currentLi].parentElement.parentElement.style.transform = 'translateX(-'+$this.currentPos+'px)';
            $this.currentLi += 1;
            return false;
		});
		$this.me_left.addEventListener('click', function(e) {
			id = this.parentNode.parentNode.getAttribute('data');
			$this = cgavisscroll[id];
            items = document.querySelectorAll($this.me + 'ul li');
            let elementwidth = 0;
            if ($this.currentLi <= 0) { // end of list : restart to top position
                $this.currentLi = 0; 
                elementwidth = parseInt(items[$this.currentLi].style.marginLeft) + items[$this.currentLi].getBoundingClientRect().width + parseInt(items[$this.currentLi].style.marginRight)
                $this.currentPos = parseInt($this.items_ul_0.style.width) + elementwidth;
                $this.currentLi = (items.length/2)+1; 
                elementwidth = parseInt(items[$this.currentLi].style.marginLeft) + items[$this.currentLi].getBoundingClientRect().width + parseInt(items[$this.currentLi].style.marginRight);
                items[$this.currentLi].parentElement.parentElement.style.transition = "transform 0s";
                $this.currentPos -= elementwidth;
                items[$this.currentLi].parentElement.parentElement.style.transform = 'translateX(-'+$this.currentPos+'px)';
                $this.currentLi -= 1;
            } 
            elementwidth = parseInt(items[$this.currentLi].style.marginLeft) + items[$this.currentLi].getBoundingClientRect().width + parseInt(items[$this.currentLi].style.marginRight);
            items[$this.currentLi].parentElement.parentElement.style.transition = "transform 1s";
            $this.currentPos -= elementwidth;
            items[$this.currentLi].parentElement.parentElement.style.transform = 'translateX(-'+$this.currentPos+'px)';
            $this.currentLi -= 1;
            return false;
		});
	}
}

CGAvisScroll.prototype.dragStart = function(e) {
    id = this.getAttribute('data');
    $this = cgavisscroll[id];
    if ($this.active) // already active : ignore 
        return;
    if (e.type === "touchstart") {
        $this.initialX = e.touches[0].clientX - $this.xOffset;
        $this.initialY = e.touches[0].clientY - $this.yOffset;
    } else {
        $this.initialX = e.clientX - $this.xOffset;
        $this.initialY = e.clientY - $this.yOffset;
    }
    e.preventDefault();
    if ((e.target.localName == 'li') || (e.target.id == 'vmarquee') ||
        (e.currentTarget.id === $this.container.id)) {
        $this.active = true;
    }
}

CGAvisScroll.prototype.dragEnd = function(e) {
      $this.initialX = $this.currentX;
      $this.initialY = $this.currentY;
      $this.active = false;
}

CGAvisScroll.prototype.drag = function(e) {
      id = this.getAttribute('data');
      $this = cgavisscroll[id];
      if ($this.active) {
      
        e.preventDefault();
      
        if (e.type === "touchmove") {
          $this.currentX = e.touches[0].clientX - $this.initialX;
          $this.currentY = e.touches[0].clientY - $this.initialY;
        } else {
          $this.currentX = e.clientX - $this.initialX;
          $this.currentY = e.clientY - $this.initialY;
        }

        $this.xOffset = $this.currentX;
        $this.yOffset = $this.currentY;
        cross_marquee = this.querySelector('#vmarquee');
        $this.setTranslate($this,$this.currentX, $this.currentY, $this.cross_marquee);
      }
}
CGAvisScroll.prototype.setTranslate = function($this,xPos, yPos, el) {
      if ($this.options.direction == 1)    
        el.style.transform = "translateY("+ yPos + "px)";
      else 
        el.style.transform = "translateX(" + xPos + "px)";
}


