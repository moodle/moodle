//\/////
//\  overLIB CSS Style Plugin
//\  This file requires overLIB 4.10 or later.
//\
//\  overLIB 4.05 - You may not remove or change this notice.
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
if (typeof olInfo == 'undefined' || typeof olInfo.meets == 'undefined' || !olInfo.meets(4.10)) alert('overLIB 4.10 or later is required for the CSS Style Plugin.');
else {
registerCommands('cssstyle,padunit,heightunit,widthunit,textsizeunit,textdecoration,textstyle,textweight,captionsizeunit,captiondecoration,captionstyle,captionweight,closesizeunit,closedecoration,closestyle,closeweight');


////////
// DEFAULT CONFIGURATION
// Settings you want everywhere are set here. All of this can also be
// changed on your html page or through an overLIB call.
////////
if (typeof ol_padunit=='undefined') var ol_padunit="px";
if (typeof ol_heightunit=='undefined') var ol_heightunit="px";
if (typeof ol_widthunit=='undefined') var ol_widthunit="px";
if (typeof ol_textsizeunit=='undefined') var ol_textsizeunit="px";
if (typeof ol_textdecoration=='undefined') var ol_textdecoration="none";
if (typeof ol_textstyle=='undefined') var ol_textstyle="normal";
if (typeof ol_textweight=='undefined') var ol_textweight="normal";
if (typeof ol_captionsizeunit=='undefined') var ol_captionsizeunit="px";
if (typeof ol_captiondecoration=='undefined') var ol_captiondecoration="none";
if (typeof ol_captionstyle=='undefined') var ol_captionstyle="normal";
if (typeof ol_captionweight=='undefined') var ol_captionweight="bold";
if (typeof ol_closesizeunit=='undefined') var ol_closesizeunit="px";
if (typeof ol_closedecoration=='undefined') var ol_closedecoration="none";
if (typeof ol_closestyle=='undefined') var ol_closestyle="normal";
if (typeof ol_closeweight=='undefined') var ol_closeweight="normal";

////////
// END OF CONFIGURATION
// Don't change anything below this line, all configuration is above.
////////



////////
// INIT
////////
// Runtime variables init. Don't change for config!
var o3_padunit="px";
var o3_heightunit="px";
var o3_widthunit="px";
var o3_textsizeunit="px";
var o3_textdecoration="";
var o3_textstyle="";
var o3_textweight="";
var o3_captionsizeunit="px";
var o3_captiondecoration="";
var o3_captionstyle="";
var o3_captionweight="";
var o3_closesizeunit="px";
var o3_closedecoration="";
var o3_closestyle="";
var o3_closeweight="";


////////
// PLUGIN FUNCTIONS
////////

// Function which sets runtime variables to their default values
function setCSSStyleVariables() {
	o3_padunit=ol_padunit;
	o3_heightunit=ol_heightunit;
	o3_widthunit=ol_widthunit;
	o3_textsizeunit=ol_textsizeunit;
	o3_textdecoration=ol_textdecoration;
	o3_textstyle=ol_textstyle;
	o3_textweight=ol_textweight;
	o3_captionsizeunit=ol_captionsizeunit;
	o3_captiondecoration=ol_captiondecoration;
	o3_captionstyle=ol_captionstyle;
	o3_captionweight=ol_captionweight;
	o3_closesizeunit=ol_closesizeunit;
	o3_closedecoration=ol_closedecoration;
	o3_closestyle=ol_closestyle;
	o3_closeweight=ol_closeweight;
}

// Parses CSS Style commands.
function parseCSSStyleExtras(pf, i, ar) {
	var k = i;
	
	if (k < ar.length) {
		if (ar[k]==CSSSTYLE) { eval(pf+'css='+ar[k]); return k; }
		if (ar[k]==PADUNIT) { eval(pf+'padunit="'+ar[++k]+'"'); return k; }
		if (ar[k]==HEIGHTUNIT) { eval(pf+'heightunit="'+ar[++k]+'"'); return k; }
		if (ar[k]==WIDTHUNIT) { eval(pf+'widthunit="'+ar[++k]+'"'); return k; }
		if (ar[k]==TEXTSIZEUNIT) { eval(pf+'textsizeunit="'+ar[++k]+'"'); return k; }
		if (ar[k]==TEXTDECORATION) { eval(pf+'textdecoration="'+ar[++k]+'"'); return k; }
		if (ar[k]==TEXTSTYLE) { eval(pf+'textstyle="'+ar[++k]+'"'); return k; }
		if (ar[k]==TEXTWEIGHT) { eval(pf+'textweight="'+ar[++k]+'"'); return k; }
		if (ar[k]==CAPTIONSIZEUNIT) { eval(pf+'captionsizeunit="'+ar[++k]+'"'); return k; }
		if (ar[k]==CAPTIONDECORATION) { eval(pf+'captiondecoration="'+ar[++k]+'"'); return k; }
		if (ar[k]==CAPTIONSTYLE) { eval(pf+'captionstyle="'+ar[++k]+'"'); return k; }
		if (ar[k]==CAPTIONWEIGHT) { eval(pf+'captionweight="'+ar[++k]+'"'); return k; }
		if (ar[k]==CLOSESIZEUNIT) { eval(pf+'closesizeunit="'+ar[++k]+'"'); return k; }
		if (ar[k]==CLOSEDECORATION) { eval(pf+'closedecoration="'+ar[++k]+'"'); return k; }
		if (ar[k]==CLOSESTYLE) { eval(pf+'closestyle="'+ar[++k]+'"'); return k; }
		if (ar[k]==CLOSEWEIGHT) { eval(pf+'closeweight="'+ar[++k]+'"'); return k; }
	}
	
	return -1;
}

////////
// LAYER GENERATION FUNCTIONS
////////

// Makes simple table without caption
function ol_content_simple_cssstyle(text) {
	txt = '<table width="'+o3_width+ '" border="0" cellpadding="'+o3_border+'" cellspacing="0" style="background-color: '+o3_bgcolor+'; height: '+o3_height+o3_heightunit+';"><tr><td><table width="100%" border="0" cellpadding="' + o3_cellpad + '" cellspacing="0" style="color: '+o3_fgcolor+'; background-color: '+o3_fgcolor+'; height: '+o3_height+o3_heightunit+';"><tr><td valign="TOP"><font style="font-family: '+o3_textfont+'; color: '+o3_textcolor+'; font-size: '+o3_textsize+o3_textsizeunit+'; text-decoration: '+o3_textdecoration+'; font-weight: '+o3_textweight+'; font-style:'+o3_textstyle+'">'+text+'</font></td></tr></table></td></tr></table>';
	set_background("");
	
	return txt;
}

// Makes table with caption and optional close link
function ol_content_caption_cssstyle(text, title, close) {
	var nameId;
	closing = "";
	closeevent = "onMouseOver";
	
	if (o3_closeclick == 1) closeevent= (o3_closetitle ? "title='" + o3_closetitle +"'" : "") + " onClick";

	if (o3_capicon!="") {
		nameId=' hspace=\"5\"'+' align=\"middle\" alt=\"\"';
		if (typeof o3_dragimg != 'undefined' && o3_dragimg) nameId = ' hspace=\"5\"'+' name=\"'+o3_dragimg+'\" id=\"'+o3_dragimg+'\" align=\"middle\" alt=\"Drag Enabled\" title=\"Drag Enabled\"';
		o3_capicon = '<img src=\"'+o3_capicon+'\"'+nameId+' />';
	}
	
	if (close != "") {
		closing = '<td align="RIGHT"><a href="javascript:return '+fnRef+'cClick();" '+closeevent+'="return '+fnRef+'cClick();" style="color: '+o3_closecolor+'; font-family: '+o3_closefont+'; font-size: '+o3_closesize+o3_closesizeunit+'; text-decoration: '+o3_closedecoration+'; font-weight: '+o3_closeweight+'; font-style:'+o3_closestyle+';">'+close+'</a></td>';
	}
	
	txt = '<table width="'+o3_width+ '" border="0" cellpadding="'+o3_border+'" cellspacing="0" style="background-color: '+o3_bgcolor+'; background-image: url('+o3_bgbackground+'); height: '+o3_height+o3_heightunit+';"><tr><td><table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td><font style="font-family: '+o3_captionfont+'; color: '+o3_capcolor+'; font-size: '+o3_captionsize+o3_captionsizeunit+'; font-weight: '+o3_captionweight+'; font-style: '+o3_captionstyle+'; text-decoration: '+o3_captiondecoration+';">'+o3_capicon+title+'</font></td>'+closing+'</tr></table><table width="100%" border="0" cellpadding="' + o3_cellpad + '" cellspacing="0" style="color: '+o3_fgcolor+'; background-color: '+o3_fgcolor+'; height: '+o3_height+o3_heightunit+';"><tr><td valign="TOP"><font style="font-family: '+o3_textfont+'; color: '+o3_textcolor+'; font-size: '+o3_textsize+o3_textsizeunit+'; text-decoration: '+o3_textdecoration+'; font-weight: '+o3_textweight+'; font-style:'+o3_textstyle+'">'+text+'</font></td></tr></table></td></tr></table>';
	set_background("");

	return txt;
}

// Sets the background picture, padding and lots more. :)
function ol_content_background_cssstyle(text, picture, hasfullhtml) {
	if (hasfullhtml) {
		txt = text;
	} else {
		var pU, hU, wU;
		pU = (o3_padunit == '%' ? '%' : '');
		hU = (o3_heightunit == '%' ? '%' : '');
		wU = (o3_widthunit == '%' ? '%' : '');
		txt = '<table width="'+o3_width+wu+'" border="0" cellpadding="0" cellspacing="0" height="'+o3_height+hu+'"><tr><td colspan="3" height="'+o3_padyt+pu+'"></td></tr><tr><td width="'+o3_padxl+pu+'"></td><td valign="TOP" width="'+(o3_width-o3_padxl-o3_padxr)+pu+'"><font style="font-family: '+o3_textfont+'; color: '+o3_textcolor+'; font-size: '+o3_textsize+o3_textsizeunit+';">'+text+'</font></td><td width="'+o3_padxr+pu+'"></td></tr><tr><td colspan="3" height="'+o3_padyb+pu+'"></td></tr></table>';
	}

	set_background(picture);

	return txt;
}

////////
// PLUGIN REGISTRATIONS
////////
registerRunTimeFunction(setCSSStyleVariables);
registerCmdLineFunction(parseCSSStyleExtras);
registerHook("ol_content_simple", ol_content_simple_cssstyle, FALTERNATE, CSSSTYLE);
registerHook("ol_content_caption", ol_content_caption_cssstyle, FALTERNATE, CSSSTYLE);
registerHook("ol_content_background", ol_content_background_cssstyle, FALTERNATE, CSSSTYLE);
}