//\/////
//\  overLIB Hide Form Plugin
//\
//\  Uses an iframe shim to mask system controls for IE v5.5 or higher as suggested in
//\  http://dotnetjunkies.com/weblog/jking/posts/488.aspx
//\  This file requires overLIB 4.10 or later.
//\
//\  overLIB 4.05 - You may not remove or change this notice.
//\  Copyright Erik Bosrup 1998-2004. All rights reserved.
//\  Contributors are listed on the homepage.
//\  See http://www.bosrup.com/web/overlib/ for details.
//   $Revision$                $Date$
//\/////
//\mini
if (typeof olInfo == 'undefined' || typeof olInfo.meets == 'undefined' || !olInfo.meets(4.10)) alert('overLIB 4.10 or later is required for the HideForm Plugin.');
else {

// Function which generates the popup with an IFRAME shim
function generatePopUp(content) {
	if(!olIe4||olOp||!olIe55||(typeof o3_shadow != 'undefined' && o3_shadow)||(typeof o3_bubble != 'undefined' && o3_bubble)) return;

	var wd,ht,txt, zIdx =  0;

	wd =  parseInt(o3_width);
	ht =  over.offsetHeight;
	txt =  backDropSource(wd,ht,zIdx++);
	txt += '<div style="position: absolute; top: 0; left: 0; width: '+ wd+'px; z-index: ' + zIdx + ';">' + content + '</div>';
	layerWrite(txt);
}

// Code for the IFRAME which is used in other places
function backDropSource(width, height, Z) {
	return '<iframe frameborder="0" scrolling="no" src="javascript:false;" width="' + width + '" height="' + height + '" style="z-index: ' + Z + '; filter: Beta(Style=0,Opacity=0);"></iframe>';
}

// Hides SELECT boxes that will be under the popup
// Checking Gecko version number to try to include other browsers based on the Gecko engine
function hideSelectBox() {
	if(olNs4 || olOp || olIe55) return;
	var px, py, pw, ph, sx, sw, sy, sh, selEl, v;

	if(olIe4) v = 0;
	else {
		v = navigator.userAgent.match(/Gecko\/(\d{8})/i);
		if(!v) return;   // return if no string match
		v = parseInt(v[1]);
	}

	if (v < 20030624) {  // versions less than June 24, 2003 were earlier Netscape browsers
		px = parseInt(over.style.left);
		py = parseInt(over.style.top);
		pw = o3_width;
		ph = (o3_aboveheight ? parseInt(o3_aboveheight) : over.offsetHeight);
		selEl = (olIe4) ? o3_frame.document.all.tags("SELECT") : o3_frame.document.getElementsByTagName("SELECT");
		for (var i=0; i<selEl.length; i++) {
			if(!olIe4 && selEl[i].size < 2) continue;  // Not IE and SELECT size is 1 or not specified
			sx = pageLocation(selEl[i],'Left');
			sy = pageLocation(selEl[i],'Top');
			sw = selEl[i].offsetWidth;
			sh = selEl[i].offsetHeight;
			if((px+pw) < sx || px > (sx+sw) || (py+ph) < sy || py > (sy+sh)) continue;
			selEl[i].isHidden = 1;
			selEl[i].style.visibility = 'hidden';
		}
	}
}

// Shows previously hidden SELECT Boxes
function showSelectBox() {
	if(olNs4 || olOp || olIe55) return;
	var selEl, v;

	if(olIe4) v = 0;
	else {
		v = navigator.userAgent.match(/Gecko\/(\d{8})/i);
		if(!v) return; 
		v = parseInt(v[1]);
	}

	if(v < 20030624) {
		selEl = (olIe4) ? o3_frame.document.all.tags("SELECT") : o3_frame.document.getElementsByTagName("SELECT");
		for (var i=0; i<selEl.length; i++) {
			if(typeof selEl[i].isHidden !=  'undefined' && selEl[i].isHidden) {
				selEl[i].isHidden = 0;
				selEl[i].style.visibility = 'visible';
			}
		}
	}
}

// function gets the total offset properties of an element
// this same function occurs in overlib_mark.js.
function pageLocation(o,t){
	var x = 0

	while(o.offsetParent){
		x += o['offset'+t]
		o = o.offsetParent
	}

	x += o['offset'+t]

	return x
}

// reset mouse move routine for NS7 but not NS7.1,Opera, or IE5.5+
// It also bypasses Netscape 6 since the modifications mess up the display of popups
// and don't work anyways.
// Modify mouse move routine while loading so that hideSelectBox()
// can be called from the correct location
if (!(olNs4 || olOp || olIe55 || navigator.userAgent.indexOf('Netscape6') != -1)) {
	var MMStr = olMouseMove.toString();
	var strRe = /(if\s*\(o3_allowmove\s*==\s*1.*\)\s*)/;
	var f = MMStr.match(strRe);

	if (f) {
		var ls = MMStr.search(strRe);
		ls += f[1].length;
		var le = MMStr.substring(ls).search(/[;|}]\n/);
		MMStr = MMStr.substring(0,ls) + ' { runHook("placeLayer",FREPLACE); if(olHideForm) hideSelectBox(); ' + MMStr.substring(ls + (le != -1 ? le+3 : 0));
		document.writeln('<script type="text/javascript">\n<!--\n' + MMStr + '\n//-->\n</' + 'script>');
	}

	f = capExtent.onmousemove.toString().match(/function[ ]+(\w*)\(/);
	if (f&&f[1] != 'anonymous') capExtent.onmousemove = olMouseMove;
}


////////
// PLUGIN REGISTRATIONS
////////
registerHook("createPopup",generatePopUp,FAFTER);
registerHook("hideObject",showSelectBox,FAFTER);
olHideForm=1;
}
