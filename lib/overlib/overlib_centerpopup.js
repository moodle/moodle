//\/////
//\  overLIB Center Popup Plugin
//\  This file requires overLIB 4.10 or later.
//\
//\  overLIB 4.10 - You may not remove or change this notice.
//\  Copyright Erik Bosrup 1998-2003. All rights reserved.
//\  Contributors are listed on the homepage.
//\  See http://www.bosrup.com/web/overlib/ for details.
//   $Revision$                $Date$
//
//\/////
//\mini
////////
// PRE-INIT
// Ignore these lines, configuration is below.
////////
if (typeof olInfo == 'undefined' || typeof olInfo.meets == 'undefined' || !olInfo.meets(4.10)) alert('overLIB 4.10 or later is required for the Center Popup Plugin.');
else {
registerCommands('centerpopup,centeroffset');
////////
// DEFAULT CONFIGURATION
// You don't have to change anything here if you don't want to. All of this can be
// changed on your html page or through an overLIB call.
////////
// Default value for centerpopup is to not center the popup
if (typeof ol_centerpopup == 'undefined') var ol_centerpopup = 0;
if (typeof ol_centeroffset == 'undefined') var ol_centeroffset = '0';
////////
// END OF CONFIGURATION
// Don't change anything below this line, all configuration is above.
////////
////////
// INIT
////////
// Runtime variables init. Don't change for config!
var o3_centerpopup = 0;
var o3_centeroffset = '0';
////////
// PLUGIN FUNCTIONS
////////
function setCenterPopupVariables() {
	o3_centerpopup = ol_centerpopup;
	o3_centeroffset = ol_centeroffset;
}
// Parses Shadow and Scroll commands
function parseCenterPopupExtras(pf,i,ar) {
	var k = i,v;

	if (k < ar.length) {
		if (ar[k] == CENTERPOPUP) { eval(pf + 'centerpopup = (' + pf + 'centerpopup == 0) ? 1 : 0'); return k; }
		if (ar[k] == CENTEROFFSET) { k = opt_MULTIPLEARGS(++k,ar,(pf + 'centeroffset')); return k; }
	}

	return -1;
}
// Function which positions popup in Center of screen
function centerPopupHorizontal(browserWidth, horizontalScrollAmount, widthFix) {
	if (!o3_centerpopup) return void(0);

	var vdisp = o3_centeroffset.split(',');
	var placeX, iwidth = browserWidth, winoffset = horizontalScrollAmount;
  var pWd = parseInt(o3_width);

	placeX = winoffset + Math.round((iwidth - widthFix - pWd)/2) + parseInt(vdisp[0]);
	if(typeof o3_followscroll != 'undefined' && o3_followscroll && o3_sticky) o3_relx = placeX;

	return placeX;
}
function centerPopupVertical(browserHeight,verticalScrollAmount) {
	if (!o3_centerpopup) return void(0);

	var placeY, iheight = browserHeight, scrolloffset = verticalScrollAmount;
	var vdisp = o3_centeroffset.split(',');
	var pHeight = (o3_aboveheight ? parseInt(o3_aboveheight) : (olNs4 ? over.clip.height : over.offsetHeight));

	placeY = scrolloffset + Math.round((iheight - pHeight)/2) + (vdisp.length > 1 ? parseInt(vdisp[1]) : 0);
	if(typeof o3_followscroll != 'undefined' && o3_followscroll && o3_sticky) o3_rely = placeY;

	return placeY;
}
////////
// PLUGIN REGISTRATIONS
////////
registerRunTimeFunction(setCenterPopupVariables);
registerCmdLineFunction(parseCenterPopupExtras);
registerHook('horizontalPlacement',centerPopupHorizontal,FCHAIN);
registerHook('verticalPlacement', centerPopupVertical, FCHAIN);
if(olInfo.meets(4.10)) registerNoParameterCommands('centerpopup');
}