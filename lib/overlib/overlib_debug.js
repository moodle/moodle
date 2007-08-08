//\/////
//\  overLIB Debug Plugin
//\  This file requires overLIB 4.10 or later.
//\
//\  overLIB 4.05 - You may not remove or change this notice.
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
if (typeof olInfo == 'undefined' || typeof olInfo.meets == 'undefined' || !olInfo.meets(4.10)) alert('overLIB 4.10 or later is required for the Debug Plugin.');
else {
var olZindex;
registerCommands('allowdebug');
////////
// PLUGIN FUNCTIONS
////////
// Parses Debug Parameters
function parseDebugExtras(pf, i, ar) {
	var k =  i, v;

	if (k < ar.length) {
		if (ar[k] == ALLOWDEBUG) { v = ar[k + 1]; if(typeof v == 'string') {v = ar[++k]; if (pf != 'ol_') setCanShowParm(v);} return k; }
	}

	return -1;
}
// Debug main routine
function showProperties() {
	var args = showProperties.arguments, sho, shoObj, vis, lvl = 0, istrt = 0, theDiv = 'showProps', txt = '';

	if (args.length == 0) return;
	if (args.length % 2 && typeof args[0] == 'string') {
		istrt = 1;
		theDiv = args[0];
	}

	sho = createDivContainer(theDiv);

	if (olNs4) {
		shoObj = sho;
		txt += '<table cellpadding="1" cellspacing="0" border="0" bgcolor="#000000"><tr><td>';
	} else {
		with(sho.style) {
			backgroundColor = '#ffffcc';
			padding = '5px';
			border = '1px #000000 solid';
		}
		shoObj = sho.style;
	}

	lvl = getLayerLevel(theDiv);

	if(typeof sho.position == 'undefined') {
		sho.position = new Pagelocation(10 + lvl*20, 10, 1);
		if(typeof olZindex == 'undefined') olZindex = getDivZindex();
		shoObj.zIndex = olZindex + 1 + lvl;
	}

	txt += '<table cellpadding="5" border="0" cellspacing="0"' + (olNs4 ? ' bgcolor="#ffffcc"' : '') + '>';
	txt += '<tr><td><strong><A HREF="javascript:moveToBack(\'' + theDiv + '\');" title="Move to back">' + theDiv + '</A></strong></td><td align="RIGHT"><strong><a href="javascript:closeLayer(\'' + theDiv + '\');" TITLE="Close Layer' + (!olNs4 ? '" style="background-color: #CCCCCC; border:2px #333369 outset; padding: 2px;' : '') + '">X</a></strong></td></tr>';
	txt += '<tr><td style="text-decoration: underline;"><strong>Item</strong></td><td style="text-decoration: underline;"><strong>Value</strong></td></tr>';
	for (var i = istrt; i<args.length-1; i++) 
		txt += '<tr><td align="right"><strong>' + args[i] + ':&nbsp;</strong></td><td>' + args[++i] + '</td></tr>';
	txt += '</table>' + (olNs4 ? '</td></tr></table>' : '');

	if (olNs4) {
		sho.document.open();
		sho.document.write(txt);
		sho.document.close();
	} else {
		if(olIe5&&isMac) sho.innerHTML = '';
		sho.innerHTML = txt;
	}

	showAllVisibleLayers();
}
function getLayerLevel(lyr) {
	var i = 0;

	if (typeof document.popups == 'undefined') {
		document.popups = new Array(lyr);
	} else {
		var l = document.popups;
		for (var i = 0; i<l.length; i++) if (lyr == l[i]) break;
		if(i == l.length) l[l.length++] = lyr;
	}

	return i;
}
function getDivZindex(id) {
	var obj;

	if(id == '' || id == null) id = 'overDiv';

	obj = layerReference(id);
	obj = (olNs4 ? obj : obj.style);

	return obj.zIndex;
}
function setCanShowParm(debugID) {
	var lyr, pLyr;

	if(typeof debugID != 'string') return;

	pLyr = debugID.split(',');
	for(var i = 0; i<pLyr.length; i++) {
		lyr = layerReference(pLyr[i]);
		if(lyr != null && typeof lyr.position != 'undefined') lyr.position.canShow = 1;
	}
}
function Pagelocation(x, y, canShow) {
	this.x = x;
	this.y = y;
  this.canShow = (canShow == null) ? 0 : canShow;
}
function showAllVisibleLayers(){
	var lyr, lyrObj, l = document.popups;

	for (var i = 0; i<l.length; i++) {
		lyr = layerReference(l[i]);
		lyrObj = (olNs4 ? lyr : lyr.style);
    if(lyr.position.canShow) {
  		positionLayer(lyrObj, lyr.position.x, lyr.position.y);
  		lyrObj.visibility = 'visible';
    }
	}
}
function positionLayer(Obj, x, y) { // Obj is obj.style for IE/NS6+ but obj for NS4
	Obj.left = x + (olIe4 ? eval(docRoot + '.scrollLeft') : window.pageXOffset) + (olNs4 ? 0 : 'px');
	Obj.top = y + (olIe4 ? eval(docRoot + '.scrollTop') : window.pageYOffset) + (olNs4 ? 0 : 'px');
}
function closeLayer(lyrID) {
	var lyr = layerReference(lyrID);

  lyr.position.canShow = 0;
	lyr = (olNs4 ? lyr : lyr.style);
	lyr.visibility = 'hidden';
}
function moveToBack(layer) {
	var l = document.popups, lyr, obj, i, x = 10, y = 10, dx = 20, z = olZindex + 1;

	if(l.length == 1) return;

	lyr = layerReference(layer);
	lyr.position.x = x;
	lyr.position.y = y;
	obj = (olNs4 ? lyr : lyr.style);
	obj.zIndex = z;

	for (i = 0; i<l.length; i++) {
		if (layer == l[i]) continue;
		lyr = layerReference(l[i]);
    if(lyr.position.canShow == 0) continue;
		obj = (olNs4 ? lyr : lyr.style);
		obj.zIndex += 1;
		lyr.position.x += dx;
		lyr.position.y = y;
	}

	showAllVisibleLayers();
}
function rawTxt(txt) {
	if (typeof txt != 'string') return;
	return txt.replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;");
}
////////
// PLUGIN REGISTRATIONS
////////
registerCmdLineFunction(parseDebugExtras);
}