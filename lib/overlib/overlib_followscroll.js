//\/////
//\  overLIB Follow Scroll Plugin
//\  This file requires overLIB 4.10 or later.
//\
//\  overLIB 4.10 - You may not remove or change this notice.
//\  Copyright Erik Bosrup 1998-2004. All rights reserved.
//\  Contributors are listed on the homepage.
//\  See http://www.bosrup.com/web/overlib/ for details.
//   $Revision$                      $Date$
//\/////
//\mini

////////
// PRE-INIT
// Ignore these lines, configuration is below.
////////
if (typeof olInfo == 'undefined' || typeof olInfo.meets == 'undefined' || !olInfo.meets(4.10)) alert('overLIB 4.10 or later is required for the Follow Scroll Plugin.');
else {
registerCommands('followscroll,followscrollrefresh');


////////
// DEFAULT CONFIGURATION
// You don't have to change anything here if you don't want to. All of this can be
// changed on your html page or through an overLIB call.
////////
// Default value for scroll is not to scroll (0)
if (typeof ol_followscroll=='undefined') var ol_followscroll=0;
if (typeof ol_followscrollrefresh=='undefined') var ol_followscrollrefresh=100;

////////
// END OF CONFIGURATION
// Don't change anything below this line, all configuration is above.
////////







////////
// INIT
////////
// Runtime variables init. Don't change for config!
var o3_followscroll=0;
var o3_followscrollrefresh=100;


////////
// PLUGIN FUNCTIONS
////////
function setScrollVariables() {
	o3_followscroll=ol_followscroll;
	o3_followscrollrefresh=ol_followscrollrefresh;
}

// Parses Shadow and Scroll commands
function parseScrollExtras(pf,i,ar) {
	var k=i,v;
	if (k < ar.length) {
		if (ar[k]==FOLLOWSCROLL) { eval(pf +'followscroll=('+pf+'followscroll==0) ? 1 : 0'); return k; }
		if (ar[k]==FOLLOWSCROLLREFRESH) { eval(pf+'followscrollrefresh='+ar[++k]); return k; }
	}
	return -1;
}



// Function to support scroll feature (overloads default)
function scroll_placeLayer() {
	var placeX, placeY, widthFix = 0;
	
	// HORIZONTAL PLACEMENT
	if (o3_frame.innerWidth) { 
		widthFix=Math.ceil(1.2*(o3_frame.outerWidth - o3_frame.innerWidth));
    widthFix = (widthFix > 50) ? 20 : widthFix;
		iwidth=o3_frame.innerWidth;
	} else if (eval('o3_frame.'+docRoot)&&eval("typeof o3_frame."+docRoot+".clientWidth=='number'")&&eval('o3_frame.'+docRoot+'.clientWidth')) 
		iwidth=eval('o3_frame.'+docRoot+'.clientWidth');			

	// Horizontal scroll offset
	winoffset=(olIe4) ? eval('o3_frame.'+docRoot+'.scrollLeft') : o3_frame.pageXOffset;

	placeX = runHook('horizontalPlacement',FCHAIN,iwidth,winoffset,widthFix);
	
	// VERTICAL PLACEMENT
	if (o3_frame.innerHeight) iheight=o3_frame.innerHeight;
	else if (eval('o3_frame.'+docRoot)&&eval("typeof o3_frame."+docRoot+".clientHeight=='number'")&&eval('o3_frame.'+docRoot+'.clientHeight')) 
		iheight=eval('o3_frame.'+docRoot+'.clientHeight');			

	// Vertical scroll offset
	scrolloffset=(olIe4) ? eval('o3_frame.'+docRoot+'.scrollTop') : o3_frame.pageYOffset;

	placeY = runHook('verticalPlacement',FCHAIN,iheight,scrolloffset);

	// Actually move the object.
	repositionTo(over,placeX,placeY);
	
	if (o3_followscroll && o3_sticky && (o3_relx || o3_rely) && (typeof o3_draggable == 'undefined' || !o3_draggable)) {
		if (typeof over.scroller=='undefined' || over.scroller.canScroll) over.scroller = new Scroller(placeX-winoffset,placeY-scrolloffset,o3_followscrollrefresh);
	}
}



///////
// SUPPORT ROUTINES FOR SCROLL FEATURE
///////

// Scroller constructor
function Scroller(X,Y,refresh) {
	this.canScroll=0;
	this.refresh=refresh;
	this.x=X;
	this.y=Y;
	this.timer=setTimeout("repositionOver()",this.refresh);
}

// Removes the timer to stop replacing the layer.
function cancelScroll() {
	if (!o3_followscroll || typeof over.scroller == 'undefined') return;
	over.scroller.canScroll = 1;
	
	if (over.scroller.timer) {
		clearTimeout(over.scroller.timer);
		over.scroller.timer=null;
	}
}

// Find out how much we've scrolled.
	function getPageScrollY() {
	if (o3_frame.pageYOffset) return o3_frame.pageYOffset;
	if (eval(docRoot)) return eval('o3_frame.' + docRoot + '.scrollTop');
	return -1;
}
function getPageScrollX() {
	if (o3_frame.pageXOffset) return o3_frame.pageXOffset;
	if (eval(docRoot)) return eval('o3_frame.'+docRoot+'.scrollLeft');
	return -1;
}

// Find out where our layer is
function getLayerTop(layer) {
	if (layer.pageY) return layer.pageY;
	if (layer.style.top) return parseInt(layer.style.top);
	return -1;
}
function getLayerLeft(layer) {
	if (layer.pageX) return layer.pageX;
	if (layer.style.left) return parseInt(layer.style.left);
	return -1;
}

// Repositions the layer if needed
function repositionOver() {
	var X, Y, pgLeft, pgTop;
	pgTop = getPageScrollY();
	pgLeft = getPageScrollX();
	X = getLayerLeft(over)-pgLeft;
	Y = getLayerTop(over)-pgTop;
	
	if (X != over.scroller.x || Y != over.scroller.y) repositionTo(over, pgLeft+over.scroller.x, pgTop+over.scroller.y);
	over.scroller.timer = setTimeout("repositionOver()", over.scroller.refresh);
}

////////
// PLUGIN REGISTRATIONS
////////
registerRunTimeFunction(setScrollVariables);
registerCmdLineFunction(parseScrollExtras);
registerHook("hideObject",cancelScroll,FAFTER);
registerHook("placeLayer",scroll_placeLayer,FREPLACE);
if (olInfo.meets(4.10)) registerNoParameterCommands('followscroll');
}
