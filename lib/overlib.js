/*
 overlibmws.js core module - Copyright Foteos Macrides 2002-2004
   Initial: August 18, 2002 - Last Revised: May 17, 2004
 This module is subject to the same terms of usage as for Erik Bosrup's overLIB,
 although only a minority of the code and API now correspond with Erik's version.
 See the Change History and Command Reference for overlibmws via:

	http://www.macridesweb.com/oltest/

****
 overLIB -- You may not remove or change this notice.
 Copyright Erik Bosrup 1998-2004. All rights reserved.
 You can get Erik's version via: http://www.bosrup.com/web/overlib/
 Published under an open source license: http://www.bosrup.com/web/overlib/license.html

 Do not sell this as your own work. For details on copying or changing this script
 read the license agreement. Please give credit on sites that use overLIB and submit
 changes of the script so other people can use them as well.
*/

////////
// PRE-INIT -- Ignore these lines, configuration is below.
////////
var OLloaded=0,pmCnt=1,pMtr=new Array(),OLv,OLudf,OLrefXY;
var OLpct=new Array("83%","67%","83%","100%","117%","150%","200%","267%");
var OLbubblePI=0,OLcrossframePI=0,OLdebugPI=0,OLdraggablePI=0,OLexclusivePI=0,OLfilterPI=0;
var OLfunctionPI=0,OLhidePI=0,OLiframePI=0,OLovertwoPI=0,OLscrollPI=0,OLshadowPI=0;
if(typeof OLgateOK=='undefined')var OLgateOK=1;
OLregCmds(
 'inarray,caparray,caption,sticky,nofollow,background,noclose,mouseoff,right,left,center,'
+'offsetx,offsety,fgcolor,bgcolor,cgcolor,textcolor,capcolor,closecolor,width,wrap,wrapmax,'
+'height,border,base,status,autostatus,autostatuscap,snapx,snapy,fixx,fixy,relx,rely,midx,midy,'
+'ref,refc,refp,refx,refy,fgbackground,bgbackground,cgbackground,padx,pady,fullhtml,below,above,'
+'vcenter,capicon,textfont,captionfont,closefont,textsize,captionsize,closesize,timeout,delay,'
+'hauto,vauto,nojustx,nojusty,closetext,closeclick,closetitle,fgclass,bgclass,cgclass,capbelow,'
+'textpadding,textfontclass,captionpadding,captionfontclass,closefontclass,donothing');

////////
// DEFAULT CONFIGURATION -- See overlibConfig.txt for descriptions.
////////
if(typeof ol_fgcolor=='undefined')var ol_fgcolor="#CCCCFF";
if(typeof ol_bgcolor=='undefined')var ol_bgcolor="#333399";
if(typeof ol_cgcolor=='undefined')var ol_cgcolor="#333399";
if(typeof ol_textcolor=='undefined')var ol_textcolor="#000000";
if(typeof ol_capcolor=='undefined')var ol_capcolor="#FFFFFF";
if(typeof ol_closecolor=='undefined')var ol_closecolor="#EEEEFF";
if(typeof ol_textfont=='undefined')var ol_textfont="Verdana,Arial,Helvetica";
if(typeof ol_captionfont=='undefined')var ol_captionfont="Verdana,Arial,Helvetica";
if(typeof ol_closefont=='undefined')var ol_closefont="Verdana,Arial,Helvetica";
if(typeof ol_textsize=='undefined')var ol_textsize=1;
if(typeof ol_captionsize=='undefined')var ol_captionsize=1;
if(typeof ol_closesize=='undefined')var ol_closesize=1;
if(typeof ol_fgclass=='undefined')var ol_fgclass="";
if(typeof ol_bgclass=='undefined')var ol_bgclass="";
if(typeof ol_cgclass=='undefined')var ol_cgclass="";
if(typeof ol_textpadding=='undefined')var ol_textpadding=2;
if(typeof ol_textfontclass=='undefined')var ol_textfontclass="";
if(typeof ol_captionpadding=='undefined')var ol_captionpadding=2;
if(typeof ol_captionfontclass=='undefined')var ol_captionfontclass="";
if(typeof ol_closefontclass=='undefined')var ol_closefontclass="";
if(typeof ol_close=='undefined')var ol_close="Close";
if(typeof ol_closeclick=='undefined')var ol_closeclick=0;
if(typeof ol_closetitle=='undefined')var ol_closetitle="Click to Close";
if(typeof ol_text=='undefined')var ol_text="Default Text";
if(typeof ol_cap=='undefined')var ol_cap="";
if(typeof ol_capbelow=='undefined')var ol_capbelow=0;
if(typeof ol_background=='undefined')var ol_background="";
if(typeof ol_width=='undefined')var ol_width=200;
if(typeof ol_wrap=='undefined')var ol_wrap=0;
if(typeof ol_wrapmax=='undefined')var ol_wrapmax=0;
if(typeof ol_height=='undefined')var ol_height= -1;
if(typeof ol_border=='undefined')var ol_border=1;
if(typeof ol_base=='undefined')var ol_base=0;
if(typeof ol_offsetx=='undefined')var ol_offsetx=10;
if(typeof ol_offsety=='undefined')var ol_offsety=10;
if(typeof ol_sticky=='undefined')var ol_sticky=0;
if(typeof ol_nofollow=='undefined')var ol_nofollow=0;
if(typeof ol_noclose=='undefined')var ol_noclose=0;
if(typeof ol_mouseoff=='undefined')var ol_mouseoff=0;
if(typeof ol_hpos=='undefined')var ol_hpos=RIGHT;
if(typeof ol_vpos=='undefined')var ol_vpos=BELOW;
if(typeof ol_status=='undefined')var ol_status="";
if(typeof ol_autostatus=='undefined')var ol_autostatus=0;
if(typeof ol_snapx=='undefined')var ol_snapx=0;
if(typeof ol_snapy=='undefined')var ol_snapy=0;
if(typeof ol_fixx=='undefined')var ol_fixx= -1;
if(typeof ol_fixy=='undefined')var ol_fixy= -1;
if(typeof ol_relx=='undefined')var ol_relx=null;
if(typeof ol_rely=='undefined')var ol_rely=null;
if(typeof ol_midx=='undefined')var ol_midx=null;
if(typeof ol_midy=='undefined')var ol_midy=null;
if(typeof ol_ref=='undefined')var ol_ref="";
if(typeof ol_refc=='undefined')var ol_refc='UL';
if(typeof ol_refp=='undefined')var ol_refp='UL';
if(typeof ol_refx=='undefined')var ol_refx=0;
if(typeof ol_refy=='undefined')var ol_refy=0;
if(typeof ol_fgbackground=='undefined')var ol_fgbackground="";
if(typeof ol_bgbackground=='undefined')var ol_bgbackground="";
if(typeof ol_cgbackground=='undefined')var ol_cgbackground="";
if(typeof ol_padxl=='undefined')var ol_padxl=1;
if(typeof ol_padxr=='undefined')var ol_padxr=1;
if(typeof ol_padyt=='undefined')var ol_padyt=1;
if(typeof ol_padyb=='undefined')var ol_padyb=1;
if(typeof ol_fullhtml=='undefined')var ol_fullhtml=0;
if(typeof ol_capicon=='undefined')var ol_capicon="";
if(typeof ol_frame=='undefined')var ol_frame=self;
if(typeof ol_timeout=='undefined')var ol_timeout=0;
if(typeof ol_delay=='undefined')var ol_delay=0;
if(typeof ol_hauto=='undefined')var ol_hauto=0;
if(typeof ol_vauto=='undefined')var ol_vauto=0;
if(typeof ol_nojustx=='undefined')var ol_nojustx=0;
if(typeof ol_nojusty=='undefined')var ol_nojusty=0;
////////
// ARRAY CONFIGURATION - See overlibConfig.txt for descriptions.
////////
if(typeof ol_texts=='undefined')var ol_texts=new Array("Text 0","Text 1");
if(typeof ol_caps=='undefined')var ol_caps=new Array("Caption 0","Caption 1");
////////
// END CONFIGURATION -- Don't change anything below, all configuration is above.
////////

////////
// INIT -- Runtime variables.
////////
var o3_text="",o3_cap="",o3_sticky=0,o3_nofollow=0,o3_background="",o3_noclose=0,o3_mouseoff=0;
var o3_hpos=RIGHT,o3_offsetx=10,o3_offsety=10,o3_fgcolor="",o3_bgcolor="",o3_cgcolor="";
var o3_textcolor="",o3_capcolor="",o3_closecolor="",o3_width=200,o3_wrap=0,o3_wrapmax=0;
var o3_height= -1,o3_border=1,o3_base=0,o3_status="",o3_autostatus=0,o3_snapx=0,o3_snapy=0;
var o3_fixx= -1,o3_fixy= -1,o3_relx=null,o3_rely=null,o3_midx=null,o3_midy=null,o3_ref="";
var o3_refc='UL',o3_refp='UL',o3_refx=0,o3_refy=0,o3_fgbackground="",o3_bgbackground="";
var o3_cgbackground="",o3_padxl=0,o3_padxr=0,o3_padyt=0,o3_padyb=0,o3_fullhtml=0,o3_vpos=BELOW;
var o3_capicon="",o3_textfont="Verdana,Arial,Helvetica",o3_captionfont="Verdana,Arial,Helvetica";
var o3_closefont="Verdana,Arial,Helvetica",o3_textsize=1,o3_captionsize=1,o3_closesize=1;
var o3_frame=self,o3_timeout=0,o3_delay=0,o3_hauto=0,o3_vauto=0,o3_nojustx=0,o3_nojusty=0;
var o3_close="Close",o3_closeclick=0,o3_closetitle="",o3_fgclass="",o3_bgclass="",o3_cgclass="";
var o3_textpadding=2,o3_textfontclass="",o3_captionpadding=2,o3_captionfontclass="";
var o3_closefontclass="",o3_capbelow=0,CSSOFF=DONOTHING,CSSCLASS=DONOTHING;
// Display state variables
var o3_x=0,o3_y=0,o3_showingsticky=0,o3_allowmove=0,o3_removecounter=0;
var o3_delayid=0,o3_timerid=0,o3_showid=0;
// Our layer
var over=null,OLfnRef="",OLhover=0;
// Decide browser version
var OLua=navigator.userAgent.toLowerCase();
var OLns4=(navigator.appName=='Netscape'&&parseInt(navigator.appVersion)==4);
var OLns6=(document.getElementById)?1:0;
var OLie4=(document.all)?1:0;
var OLmac=(OLua.indexOf('mac')>=0)?1:0;
var OLsaf=(OLua.indexOf('safari')>=0)?1:0;
var OLkon=(OLua.indexOf('konqueror')>=0)?1:0;
var OLopr=(OLua.indexOf('opera')>=0)?1:0;
var OLop7=(OLopr&&document.createTextNode)?1:0;
if(OLopr){OLns4=OLns6=0;if(!OLop7)OLie4=0;}
var OLieM=((OLie4&&OLmac)&&!(OLsaf||OLkon||OLopr))?1:0;
var OLdr='document.body';
var OLie5=0,OLie55=0;if(OLie4&&!OLopr){
if((OLv=OLua.match(/msie (\d\.\d+)\.*/i))&&(OLv=parseFloat(OLv[1]))>=5.0){
OLie5=1;OLns6=0;if(OLv>=5.5)OLie55=1;
if(typeof document.compatMode!='undefined'&&document.compatMode=='CSS1Compat')
OLdr='document.documentElement';}if(OLns6)OLie4=0;}
if(OLns4)window.onresize=function(){location.reload();}
// Capture events or diffuse the public functions.
var OLchkMseCapture=1,OLcapExtent;
if(OLns4||OLie4||OLns6)OLmouseCapture();
else{overlib=nd=cClick=OLpageDefaults=no_overlib;ver3fix=true;}

////////
// PUBLIC FUNCTIONS
////////
// overlib(arg0, ..., argN); Loads defaults then args into global runtime variables.
function overlib(){
if(!(OLloaded&&OLgateOK))return;
if((OLexclusivePI)&&OLisExclusive(overlib.arguments))return true;
if(OLchkMseCapture)OLmouseCapture();
if(over)cClick();
// Load defaults to runtime.
OLloadP1or2();o3_close=ol_close;o3_closeclick=ol_closeclick;o3_closetitle=ol_closetitle;
o3_noclose=ol_noclose;o3_mouseoff=ol_mouseoff;o3_sticky=ol_sticky;o3_closecolor=ol_closecolor;
o3_closefont=ol_closefont;o3_closesize=ol_closesize;o3_closefontclass=ol_closefontclass;
OLsetRunTimeVar();
OLfnRef="";OLhover=0;o3_frame=ol_frame;
if(OLns4)over=o3_frame.document.layers['overDiv'];
else if(OLie4)over=o3_frame.document.all['overDiv'];
else if(OLns6)over=o3_frame.document.getElementById("overDiv");
OLparseTokens('o3_',overlib.arguments);
if(OLbubblePI&&o3_bubble)OLchkForBubbleEffect();
if(OLdebugPI&&o3_allowdebug!="")OLsetDebugCanShow(o3_allowdebug);
if(OLshadowPI)OLinitShadow();
if(OLiframePI)OLinitIframe();
if(OLfilterPI)OLinitFilterLyr();
// Prepare status line
if(OLexclusivePI&&o3_exclusive&&o3_exclusivestatus!="")o3_status=o3_exclusivestatus;
else if(o3_autostatus==2&&o3_cap!="")o3_status=o3_cap;
else if(o3_autostatus==1&&o3_text!="")o3_status=o3_text;
if(o3_delay==0){return OLmain();
}else{o3_delayid=setTimeout("OLmain()",o3_delay);
// Set status line now if specified.
if(o3_status!=""){self.status=o3_status;return true;}
else if(!(OLop7&&event&&event.type=='mouseover'))return false;}
}

// Clears popups if appropriate
function nd(time){
if(!(OLloaded&&OLgateOK))return;
if((OLexclusivePI)&&OLisExclusive())return true;
if(time&&!o3_delay){
if(o3_timerid>0)clearTimeout(o3_timerid);o3_timerid=setTimeout("cClick()",(o3_timeout=time));
}else{if(o3_removecounter>=1)o3_showingsticky=0;if(o3_showingsticky==0){
o3_allowmove=0;if(over)OLhideObject(over);}else{o3_removecounter++;}}
return true;
}

// The Close function for stickies
function cClick(){
if(OLloaded&&OLgateOK){OLhover=0;OLhideObject(over);o3_showingsticky=0;}
return false;
}

// Set page-specific defaults.
function OLpageDefaults(){
OLparseTokens('ol_',OLpageDefaults.arguments);
}

// For unsupported browsers.
function no_overlib(){return ver3fix;}

////////
// OVERLIB MAIN FUNCTION SET
////////
function OLmain(){
o3_delay=0;
if(OLdraggablePI)OLcheckDrag();
// Act on NOCLOSE or MOUSEOFF if in our frame, otherwise, ignore.
if(o3_frame==self){if(o3_noclose)OLoptMOUSEOFF(0);else if(o3_mouseoff)OLoptMOUSEOFF(1);}
// Create and write layer content
OLdoLyr();
// For initial layer placement, even stickies may be moved.
o3_allowmove=0;
// Initiate a timer for timeout
if(o3_timeout>0){
if(o3_timerid>0)clearTimeout(o3_timerid);o3_timerid=setTimeout("cClick()",o3_timeout);}
// Use REFerence if requested (center on failure)
if(o3_ref){OLrefXY=OLgetRefXY(o3_ref);if(OLrefXY[0]==null){o3_ref="";o3_midx=0;o3_midy=0;}}
// Place and show layer
OLdisp(o3_status);if(o3_status!="")return true;
else if(!(OLop7&&event&&event.type=='mouseover'))return false;
}

// Loads defaults for primaries or secondaries
function OLloadP1or2(){
o3_text=ol_text;o3_cap=ol_cap;o3_capbelow=ol_capbelow;o3_textpadding=ol_textpadding;
o3_captionpadding=ol_captionpadding;o3_border=ol_border;o3_base=ol_base;o3_status=ol_status;
o3_autostatus=ol_autostatus;o3_nofollow=ol_nofollow;o3_width=ol_width;o3_wrap=ol_wrap;
o3_wrapmax=ol_wrapmax;o3_height=ol_height;o3_hpos=ol_hpos;o3_vpos=ol_vpos;o3_offsetx=ol_offsetx;
o3_offsety=ol_offsety;o3_snapx=ol_snapx;o3_snapy=ol_snapy;o3_relx=ol_relx;o3_rely=ol_rely;
o3_midx=ol_midx;o3_midy=ol_midy;o3_ref=ol_ref;o3_refc=ol_refc;o3_refp=ol_refp;o3_refx=ol_refx;
o3_refy=ol_refy;o3_fixx=ol_fixx;o3_fixy=ol_fixy;o3_nojustx=ol_nojustx;o3_nojusty=ol_nojusty;
o3_hauto=ol_hauto;o3_vauto=ol_vauto;o3_timeout=ol_timeout;o3_delay=ol_delay;
o3_fgcolor=ol_fgcolor;o3_bgcolor=ol_bgcolor;o3_cgcolor=ol_cgcolor;o3_textcolor=ol_textcolor;
o3_capcolor=ol_capcolor;o3_textfont=ol_textfont;o3_captionfont=ol_captionfont;
o3_textsize=ol_textsize;o3_captionsize=ol_captionsize;o3_fgbackground=ol_fgbackground;
o3_bgbackground=ol_bgbackground;o3_cgbackground=ol_cgbackground;o3_capicon=ol_capicon;
o3_background=ol_background;o3_padxl=ol_padxl;o3_padxr=ol_padxr;o3_padyt=ol_padyt;
o3_padyb=ol_padyb;o3_fullhtml=ol_fullhtml;o3_fgclass=ol_fgclass;o3_bgclass=ol_bgclass;
o3_cgclass=ol_cgclass;o3_textfontclass=ol_textfontclass;o3_captionfontclass=ol_captionfontclass;
}

// Chooses LGF 
function OLdoLGF(){
return (o3_background!=''||o3_fullhtml)?OLcontentBackground(o3_text,o3_background,o3_fullhtml):
(o3_cap=="")?OLcontentSimple(o3_text):
(o3_sticky)?OLcontentCaption(o3_text,o3_cap,o3_close):OLcontentCaption(o3_text,o3_cap,'');
}

// Creates and writes layer content
function OLdoLyr(){
// Create content
if(o3_background==''&&!o3_fullhtml){
if(o3_fgbackground!='')o3_fgbackground=' background="'+o3_fgbackground+'"';
if(o3_bgbackground!='')o3_bgbackground=' background="'+o3_bgbackground+'"';
if(o3_cgbackground!='')o3_cgbackground=' background="'+o3_cgbackground+'"';
if(o3_fgcolor!='')o3_fgcolor=' bgcolor="'+o3_fgcolor+'"';
if(o3_bgcolor!='')o3_bgcolor=' bgcolor="'+o3_bgcolor+'"';
if(o3_cgcolor!='')o3_cgcolor=' bgcolor="'+o3_cgcolor+'"';
if(o3_height>0)o3_height=' height="'+o3_height+'"';else o3_height='';}
if(!OLns4)OLrepositionTo(over,(OLns6?20:0),0);
var lyrHtml=OLdoLGF();
// If sticky, initialize
if(o3_sticky){if(o3_timerid>0){clearTimeout(o3_timerid);o3_timerid=0;}
o3_showingsticky=1;o3_removecounter=0;}
// Write layer
if(o3_wrap&&!o3_fullhtml){
OLlayerWrite(lyrHtml);o3_width=(OLns4?over.clip.width:over.offsetWidth);
o3_wrap=0;if(o3_wrapmax>0&&o3_width>o3_wrapmax)o3_width=o3_wrapmax;lyrHtml=OLdoLGF();}
OLlayerWrite(lyrHtml);o3_width=(OLns4?over.clip.width:over.offsetWidth);
if(OLbubblePI&&o3_bubble)OLgenerateBubble(lyrHtml);
}

////////
// LAYER GENERATION FUNCTIONS
////////
// Makes simple table without caption
function OLcontentSimple(text){
var txt=
'<table'+(o3_wrap?'':' width="'+o3_width+'"')+o3_height+' border="0" cellpadding="'+o3_border
+'" cellspacing="0"'+(o3_bgclass?' class="'+o3_bgclass+'"':o3_bgcolor+o3_bgbackground)
+'><tr><td><table width="100%"'+o3_height+' border="0" cellpadding="'+o3_textpadding
+'" cellspacing="0"'+(o3_fgclass?' class="'+o3_fgclass+'"':o3_fgcolor+o3_fgbackground)
+'><tr><td valign="top"'+(o3_fgclass?' class="'+o3_fgclass+'"':'')+'>'
+OLlgfUtil(0,o3_textfontclass,'div',o3_textcolor,o3_textfont,o3_textsize)+text
+OLlgfUtil(1,'','div')+'</td></tr></table>'+((o3_base>0&&!o3_wrap)?
('<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td height="'+o3_base
+'"></td></tr></table>'):'')+'</td></tr></table>';
OLsetBackground('');
return txt;
}

// Makes table with caption and optional close link
function OLcontentCaption(text,title,close){
var closing='',closeevent='onMouseOver',caption,maintxt,txt;
if(o3_closeclick==1)closeevent=(o3_closetitle?'title="'+o3_closetitle+'"':'')+' onClick';
if(o3_capicon!='')o3_capicon='<img src="'+o3_capicon+'" /> ';
if(close!=''){closing='<td align="right"><a hreF="javascript:return '+OLfnRef+'cClick();" '
+closeevent+'="return '+OLfnRef+'cClick();"'+(o3_closefontclass?' class="'+o3_closefontclass
+'">':'>'+OLlgfUtil(0,'','span',o3_closecolor,o3_closefont,o3_closesize))+close
+(o3_closefontclass?'':OLlgfUtil(1,'','span'))+'</a></td>';}
caption='<table width="100%" border="0" cellpadding="'+o3_captionpadding+'" cellspacing="0"'
+(o3_cgclass?' class="'+o3_cgclass+'"':o3_cgcolor+o3_cgbackground)+'><tr><td'
+(o3_cgclass?' class="'+o3_cgclass+'">':'>')+(o3_captionfontclass?'<div class="'
+o3_captionfontclass+'">':'<strong>'
+OLlgfUtil(0,'','div',o3_capcolor,o3_captionfont,o3_captionsize))+o3_capicon+title
+OLlgfUtil(1,'','div')+(o3_captionfontclass?'':'</strong>')+'</td>'+closing+'</tr></table>';
maintxt='<table width="100%" '+o3_height+' border="0" cellpadding="'+o3_textpadding
+'" cellspacing="0"'+(o3_fgclass?' class="'+o3_fgclass+'"':o3_fgcolor+o3_fgbackground)
+'><tr><td valign="top"'+(o3_fgclass?' class="'+o3_fgclass+'"':'')+'>'
+OLlgfUtil(0,o3_textfontclass,'div',o3_textcolor,o3_textfont,o3_textsize)+text
+OLlgfUtil(1,'','div')+'</td></tr></table>';
txt='<table'+(o3_wrap?'':' width="'+o3_width+'"')+o3_height+' border="0" cellpadding="'
+o3_border+'" cellspacing="0"'+(o3_bgclass?' class="'+o3_bgclass+'"':o3_bgcolor+o3_bgbackground)
+'><tr><td>'+(o3_capbelow?maintxt+caption:caption+maintxt)+((o3_base>0&&!o3_wrap)?
('<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td height="'+o3_base
+'"></td></tr></table>'):'')+'</td></tr></table>';
OLsetBackground('');
return txt;
}

// Sets the background picture, padding and lots more. :)
function OLcontentBackground(text, picture, hasfullhtml){
var txt;if(hasfullhtml){txt=text;}else{txt=
'<table'+(o3_wrap?'':' width="'+o3_width+'"')+' border="0" cellpadding="0" cellspacing="0" '
+'height="'+o3_height+'"><tr><td colspan="3" height="'+o3_padyt+'"></td></tr><tr><td width="'
+o3_padxl+'"></td><td valign="top"'+(o3_wrap?'':' width="'+(o3_width-o3_padxl-o3_padxr)+'"')+'>'
+OLlgfUtil(0,o3_textfontclass,'div',o3_textcolor,o3_textfont,o3_textsize)+text+
OLlgfUtil(1,'','div')+'</td><td width="'+o3_padxr+'"></td></tr><tr><td colspan="3" height="'
+o3_padyb+'"></td></tr></table>';}
OLsetBackground(picture);
return txt;
}

// LGF utility.
function OLlgfUtil(end,tfc,ele,col,fac,siz){
if(end)return ('</'+(OLns4?'font':ele)+'>');else return (tfc?'<div class="'+tfc+'">':
('<'+(OLns4?'font color="'+col+'" face="'+OLquoteMultiNameFonts(fac)+'" size="'+siz:ele
+' style="color:'+col+';font-family:'+OLquoteMultiNameFonts(fac)+';font-size:'+siz+';'
+(ele=='span'?'text-decoration:underline;':''))+'">'));
}

// Quotes multi-word font names
function OLquoteMultiNameFonts(theFont){
var i,v,pM=theFont.split(',');
for(i=0;i<pM.length;i++){v=pM[i];v=v.replace(/^\s+/,'').replace(/\s+$/,'');
if(/\s/.test(v) && !/['"]/.test(v)){v="\'"+v+"\'";pM[i]=v;}}
return pM.join();
}

// Loads a picture into the div.
function OLsetBackground(pic){
if(pic==''){
if(OLns4)over.background.src=null;else if(OLie4||OLns6)over.style.backgroundImage='none';
}else{
if(OLns4)over.background.src=pic;else if(OLie4||OLns6)over.style.backgroundImage='url('+pic+')';}
}

////////
// HANDLING FUNCTIONS
////////
// Displays the popup
function OLdisp(statustext){
if(o3_allowmove==0){
if(OLshadowPI)OLdispShadow();if(OLiframePI)OLdispIfShim();
OLplaceLayer();o3_showid=setTimeout("OLshowObject(over)",1);
o3_allowmove=(o3_sticky||o3_nofollow)?0:1;}
if(statustext!="")self.status=statustext;
}

// Decides where we want the popup.
function OLplaceLayer(){
var X,Y,pgLeft,pgTop,pWd=o3_width,pHt,iWd=100,iHt=100,SB=0,LM=0,CX=0,TM=0,BM=0,CY=0;
// HORIZONTAL PLACEMENT
if(eval('o3_frame.'+OLdr)&&typeof eval('o3_frame.'+OLdr).clientWidth=='number'&&
eval('o3_frame.'+OLdr).clientWidth>0)iWd=eval('o3_frame.'+OLdr).clientWidth;
else if(typeof(o3_frame.innerWidth)=='number'){
SB=Math.ceil(1.4*(o3_frame.outerWidth-o3_frame.innerWidth));if(SB>20)SB=20;
iWd=o3_frame.innerWidth;}pgLeft=(OLie4)?eval('o3_frame.'+OLdr).scrollLeft:o3_frame.pageXOffset;
if(OLie55&&OLfilterPI&&o3_filtershadow)SB=CX=5;else
if((OLshadowPI)&&bkdrop&&o3_shadow&&o3_shadowx){SB+=((o3_shadowx>0)?o3_shadowx:0);
LM=((o3_shadowx<0)?Math.abs(o3_shadowx):0);CX=Math.abs(o3_shadowx);}
if(o3_ref!=""||o3_fixx> -1||o3_relx!=null||o3_midx!=null){
if(o3_ref!=""){
// Relative to a REFerence
X=OLrefXY[0];if(OLie55&&OLfilterPI&&o3_filtershadow){if(o3_refp=='UR'||o3_refp=='LR')X -= 5;}
else if((OLshadowPI)&&bkdrop&&o3_shadow&&o3_shadowx){  
if(o3_shadowx<0&&(o3_refp=='UL'||o3_refp=='LL'))X += o3_shadowx;
else if(o3_shadowx>0&&(o3_refp=='UR'||o3_refp=='LR'))X -= o3_shadowx;}
}else{if(o3_midx!=null){
// Middle of popup relative to middle of window
X=parseInt(pgLeft+((iWd-pWd-SB-LM)/2)+o3_midx);
}else{if(o3_relx!=null){
// Relative to window margins
if(o3_relx>=0)X=pgLeft+o3_relx+LM;else X=pgLeft+o3_relx+iWd-pWd-SB;
}else{
// Fixed position
X=o3_fixx+LM;}}}
}else{
// From mouse
if(o3_hauto){
if(o3_hpos==LEFT&&o3_x-pgLeft<iWd/2&&o3_x-pWd-o3_offsetx<pgLeft+LM)o3_hpos=RIGHT;else
if(o3_hpos==RIGHT&&o3_x-pgLeft>iWd/2&&o3_x+pWd+o3_offsetx>pgLeft+iWd-SB)o3_hpos=LEFT;}
X=(o3_hpos==CENTER)?parseInt(o3_x-((pWd+CX)/2)+o3_offsetx):
(o3_hpos==LEFT)?o3_x-o3_offsetx-pWd:o3_x+o3_offsetx;
if(o3_snapx>1){
// Snapping
var snapping=X % o3_snapx;
if(o3_hpos==LEFT){X=X-(o3_snapx+snapping);}else{X=X+(o3_snapx-snapping);}}}
if(!o3_nojustx&&X+pWd>pgLeft+iWd-SB)
// Justify
X=iWd+pgLeft-pWd-SB;if(!o3_nojustx&&X-LM<pgLeft)X=pgLeft+LM;
// VERTICAL PLACEMENT
pgTop=OLie4?eval('o3_frame.'+OLdr).scrollTop:o3_frame.pageYOffset;
if(OLie4||(o3_frame.document.body&&typeof o3_frame.document.body.clientHeight=='number'&&
o3_frame.document.body.clientHeight>0))iHt=eval('o3_frame.'+OLdr).clientHeight;
else if(typeof(o3_frame.innerHeight)=='number')iHt=o3_frame.innerHeight;
if(OLbubblePI&&o3_bubble)pHt=OLbubbleHt;else pHt=OLns4?over.clip.height:over.offsetHeight;
if((OLshadowPI)&&bkdrop&&o3_shadow&&o3_shadowy){TM=(o3_shadowy<0)?Math.abs(o3_shadowy):0;
if(OLie55&&OLfilterPI&&o3_filtershadow)BM=CY=5;else
BM=(o3_shadowy>0)?o3_shadowy:0;CY=Math.abs(o3_shadowy);}
if(o3_ref!=""||o3_fixy> -1||o3_rely!=null||o3_midy!=null){
if(o3_ref!=""){
// Relative to a REFerence
Y=OLrefXY[1];if(OLie55&&OLfilterPI&&o3_filtershadow){if(o3_refp=='LL'||o3_refp=='LR')Y -= 5;}
else if((OLshadowPI)&&bkdrop&&o3_shadow&&o3_shadowy){
if(o3_shadowy<0&&(o3_refp=='UL'||o3_refp=='UR'))Y+=o3_shadowy;else
if(o3_shadowy>0&&(o3_refp=='LL'||o3_refp=='LR'))Y-=o3_shadowy;}
}else{if(o3_midy!=null){
// Middle of popup relative to middle of window
Y=parseInt(pgTop+((iHt-pHt-CY)/2)+o3_midy);
}else{if(o3_rely!=null){
// Relative to window margins
if(o3_rely>=0)Y=pgTop+o3_rely+TM;else Y=pgTop+o3_rely+iHt-pHt-BM;}else{
// Fixed position
Y=o3_fixy+TM;}}}
}else{
// From mouse
if(o3_vauto){
if(o3_vpos==ABOVE&&o3_y-pgTop<iHt/2&&o3_y-pHt-o3_offsety<pgTop)o3_vpos=BELOW;else
if(o3_vpos==BELOW&&o3_y-pgTop>iHt/2&&o3_y+pHt+o3_offsety+(OLns4?17:0)>pgTop+iHt-BM)
o3_vpos=ABOVE;}Y=(o3_vpos==VCENTER)?parseInt(o3_y-((pHt+CY)/2)+o3_offsety):
(o3_vpos==ABOVE)?o3_y-(pHt+o3_offsety+BM):o3_y+o3_offsety+TM;
if(o3_snapy>1){
// Snapping
var snapping=Y % o3_snapy;
if(pHt>0&&o3_vpos==ABOVE){Y=Y-(o3_snapy+snapping);}else{Y=Y+(o3_snapy-snapping);}}}
if(!o3_nojusty&&Y+pHt+BM>pgTop+iHt)
// Justify
Y=pgTop+iHt-pHt-BM;if(!o3_nojusty&&Y-TM<pgTop)Y=pgTop+TM;
// Actually move the object.
OLrepositionTo(over,X,Y);if(OLns6){iHt=o3_frame.innerHeight;OLrepositionTo(over,X,Y);}
if(OLshadowPI)OLrepositionShadow(X,Y);
if(OLiframePI)OLrepositionIfShim(X,Y);
if(OLscrollPI)OLchkScroll(X-pgLeft,Y-pgTop);
}

// Gets location of a REFerence object
function OLgetRefXY(ref){
var mn=ref,mref=OLgetRefById(mn,o3_frame.document),mkObj,of,refXY;
if(mref==null)mref=OLgetRefByName(mn,o3_frame.document);if(mref==null)return [null,null];
mkObj=mref;refXY=[o3_refx,o3_refy];
if(OLns4){if(typeof mref.length!='undefined'&&mref.length>1){
mkObj=mref[0];refXY[0]+=mref[0].x+mref[1].pageX;refXY[1]+=mref[0].y+mref[1].pageY;
}else{if((mref.toString().indexOf('Image')!= -1)||(mref.toString().indexOf('Anchor')!= -1)){
refXY[0]+=mref.x;refXY[1]+=mref.y;}else{refXY[0]+=mref.pageX;refXY[1]+=mref.pageY;}}
}else{refXY[0]+=OLpageLoc(mref,'Left');refXY[1]+=OLpageLoc(mref,'Top');}
of=OLgetRefOffsets(mkObj);refXY[0]+=of[0];refXY[1]+=of[1];
return refXY;
}

// Gets popup vs REFerence offsets
function OLgetRefOffsets(mkObj){
var mc=o3_refc.toUpperCase(),mp=o3_refp.toUpperCase(),mW=mH=pW=pH=0,off=[0,0];
pW=(OLbubblePI&&o3_bubble)?o3_width:OLns4?over.clip.width:over.offsetWidth;
pH=(OLbubblePI&&o3_bubble)?OLbubbleHt:OLns4?over.clip.height:over.offsetHeight;
if((!OLop7)&&mkObj.toString().indexOf('Image')!= -1){mW=mkObj.width;mH=mkObj.height;
}else if((!OLop7)&&mkObj.toString().indexOf('Anchor')!= -1){mc=o3_refc='UL';}else{
mW=(OLns4)?mkObj.clip.width:mkObj.offsetWidth;mH=(OLns4)?mkObj.clip.height:mkObj.offsetHeight;}
if(mc=='UL'){off=(mp=='UR')?[-pW,0]:(mp=='LL')?[0,-pH]:(mp=='LR')?[-pW,-pH]:[0,0];
}else if(mc=='UR'){off=(mp=='UR')?[mW-pW,0]:(mp=='LL')?[mW,-pH]:(mp=='LR')?[mW-pW,-pH]:[mW,0];
}else if(mc=='LL'){off=(mp=='UR')?[-pW,mH]:(mp=='LL')?[0,mH-pH]:(mp=='LR')?[-pW,mH-pH]:[0,mH];
}else if(mc=='LR'){off=(mp=='UR')?[mW-pW,mH]:(mp=='LL')?[mW,mH-pH]:(mp=='LR')?[mW-pW,mH-pH]:
[mW,mH];}
return off;
}

// Gets x or y location of object
function OLpageLoc(o,t){
var l=0;
while(o.offsetParent&&o.offsetParent.tagName.toLowerCase()!='html'){
l+=o['offset'+t];o=o.offsetParent;}l+=o['offset'+t];
return l;
} 

// Gets REFerence by id (or name synonym for post-NS4 browsers)
function OLgetRefById(l,d){
var r="",j;d=(d||document);
if(d.all){return d.all[l];}else if(d.getElementById){return d.getElementById(l);
}else if(d.layers&&d.layers.length>0){if(d.layers[l])return d.layers[l];
for(j=0;j<d.layers.length;j++){r=OLgetRefById(l,d.layers[j].document);if(r)return r;}}
return null;
}

// Seeks REFerence by name if not found by id
function OLgetRefByName(l,d){
var r=null,j;d=(d||document);
if(typeof d.images[l]!='undefined'&&d.images[l]){return d.images[l];
}else if(typeof d.anchors[l]!='undefined'&&d.anchors[l]){return d.anchors[l];
}else if(d.layers&&d.layers.length>0){
for(j=0;j<d.layers.length;j++){r=OLgetRefByName(l,d.layers[j].document);
if(r&&r.length>0)return r;else if(r)return [r,d.layers[j]];}}
return null;
}

// Moves the layer
function OLmouseMove(e){
var e=(e)?e:event;
if(e.pageX){o3_x=e.pageX;o3_y= e.pageY;}
else if(e.clientX){
o3_x=e.clientX+eval('o3_frame.'+OLdr).scrollLeft;
o3_y=e.clientY+eval('o3_frame.'+OLdr).scrollTop;}
if(o3_allowmove){OLplaceLayer();if(OLhidePI)OLhideUtil(0,1,1,0,0,0);}
if(OLhover&&!OLns4&&OLcursorOff())cClick();
}

// Capture the mouse and chain other scripts.
function OLmouseCapture(){
var fN,mseHandler=OLmouseMove,re=/function[ ]+(\w+)\(/;
OLcapExtent=document;
if(document.onmousemove||(!OLie4&&window.onmousemove)){
if(window.onmousemove)OLcapExtent=window;
fN=OLcapExtent.onmousemove.toString().match(re)
if(!fN||fN[1]=='anonymous'||fN[1]=='OLmouseMove'){OLchkMseCapture=0;return;}
var str=fN[1]+'(e);'+'OLmouseMove(e);';mseHandler=new Function('e',str)}
OLcapExtent.onmousemove=mseHandler
if(OLns4)OLcapExtent.captureEvents(Event.MOUSEMOVE)
}

////////
// PARSING FUNCTION -- Does the actual command parsing.
////////
function OLparseTokens(pf,ar){
var v,md= -1,par=(pf!='ol_');
OLudf=(par&&!ar.length?1:0);
for(i=0;i< ar.length;i++){
if(md<0){
// Arg is maintext,unless it's a number
if(typeof ar[i]=='number'){OLudf=(par?1:0);i--;}
else{switch(pf){
case 'ol_':ol_text=ar[i];break;
default:o3_text=ar[i];}}
md=0;
}else{
if(ar[i]==INARRAY){OLudf=0;eval(pf+'text=ol_texts['+ar[++i]+']');continue;}
if(ar[i]==CAPARRAY){eval(pf+'cap=ol_caps['+ar[++i]+']');continue;}
if(ar[i]==CAPTION){eval(pf+"cap='"+OLescSglQt(ar[++i])+"'");continue;}
if(ar[i]==STICKY){eval(pf+'sticky=('+pf+'sticky==0)?1:0');continue;}
if(ar[i]==-STICKY){eval(pf+'sticky=0');continue;}
if(ar[i]==NOFOLLOW){eval(pf+'nofollow=('+pf+'nofollow==0)?1:0');continue;}
if(ar[i]==-NOFOLLOW){eval(pf+'nofollow=0');continue;}
if(ar[i]==BACKGROUND){eval(pf+"background='"+OLescSglQt(ar[++i])+"'");continue;}
if(ar[i]==NOCLOSE){eval(pf+'noclose=('+pf+'noclose==0)?1:0');continue;}
if(ar[i]==-NOCLOSE){eval(pf+'noclose=0');continue;}
if(ar[i]==MOUSEOFF){eval(pf+'mouseoff=('+pf+'mouseoff==0)?1:0');continue;}
if(ar[i]==-MOUSEOFF){eval(pf+'mouseoff=0');continue;}
if(ar[i]==RIGHT||ar[i]==LEFT||ar[i]==CENTER){eval(pf+'hpos='+ar[i]);continue;}
if(ar[i]==OFFSETX){eval(pf+'offsetx='+ar[++i]);continue;}
if(ar[i]==OFFSETY){eval(pf+'offsety='+ar[++i]);continue;}
if(ar[i]==FGCOLOR){eval(pf+'fgcolor="'+ar[++i]+'"');continue;}
if(ar[i]==BGCOLOR){eval(pf+'bgcolor="'+ar[++i]+'"');continue;}
if(ar[i]==CGCOLOR){eval(pf+'cgcolor="'+ar[++i]+'"');continue;}
if(ar[i]==TEXTCOLOR){eval(pf+"textcolor='"+OLescSglQt(ar[++i])+"'");continue;}
if(ar[i]==CAPCOLOR){eval(pf+"capcolor='"+OLescSglQt(ar[++i])+"'");continue;}
if(ar[i]==CLOSECOLOR){eval(pf+"closecolor='"+OLescSglQt(ar[++i])+"'");continue;}
if(ar[i]==WIDTH){eval(pf+'width='+ar[++i]);continue;}
if(ar[i]==WRAP){eval(pf+'wrap=('+pf+'wrap==0)?1:0');continue;}
if(ar[i]==-WRAP){eval(pf+'wrap=0');continue;}
if(ar[i]==WRAPMAX){eval(pf+'wrapmax='+ar[++i]);continue;}
if(ar[i]==HEIGHT){eval(pf+'height='+ar[++i]);continue;}
if(ar[i]==BORDER){eval(pf+'border='+ar[++i]);continue;}
if(ar[i]==BASE){eval(pf+'base='+ar[++i]);continue;}
if(ar[i]==STATUS){eval(pf+"status='"+OLescSglQt(ar[++i])+"'");continue;}
if(ar[i]==AUTOSTATUS){eval(pf+'autostatus=('+pf+'autostatus==1)?0:1');continue;}
if(ar[i]==-AUTOSTATUS){eval(pf+'autostatus=('+pf+'autostatus==2)?2:0');continue;}
if(ar[i]==AUTOSTATUSCAP){eval(pf+'autostatus=('+pf+'autostatus==2)?0:2');continue;}
if(ar[i]==-AUTOSTATUSCAP){eval(pf+'autostatus=('+pf+'autostatus==1)?1:0');continue;}
if(ar[i]==CLOSETEXT){eval(pf+"close='"+OLescSglQt(ar[++i])+"'");continue;}
if(ar[i]==SNAPX){eval(pf+'snapx='+ar[++i]);continue;}
if(ar[i]==SNAPY){eval(pf+'snapy='+ar[++i]);continue;}
if(ar[i]==FIXX){eval(pf+'fixx='+ar[++i]);continue;}
if(ar[i]==FIXY){eval(pf+'fixy='+ar[++i]);continue;}
if(ar[i]==RELX){eval(pf+'relx='+ar[++i]);continue;}
if(ar[i]==RELY){eval(pf+'rely='+ar[++i]);continue;}
if(ar[i]==MIDX){eval(pf+'midx='+ar[++i]);continue;}
if(ar[i]==MIDY){eval(pf+'midy='+ar[++i]);continue;}
if(ar[i]==REF){eval(pf+"ref='"+OLescSglQt(ar[++i])+"'");continue;}
if(ar[i]==REFC){eval(pf+"refc='"+OLescSglQt(ar[++i])+"'");continue;}
if(ar[i]==REFP){eval(pf+"refp='"+OLescSglQt(ar[++i])+"'");continue;}
if(ar[i]==REFX){eval(pf+'refx='+ar[++i]);continue;}
if(ar[i]==REFY){eval(pf+'refy='+ar[++i]);continue;}
if(ar[i]==FGBACKGROUND){eval(pf+"fgbackground='"+OLescSglQt(ar[++i])+"'");continue;}
if(ar[i]==BGBACKGROUND){eval(pf+"bgbackground='"+OLescSglQt(ar[++i])+"'");continue;}
if(ar[i]==CGBACKGROUND){eval(pf+"cgbackground='"+OLescSglQt(ar[++i])+"'");continue;}
if(ar[i]==PADX){eval(pf+'padxl='+ar[++i]);eval(pf+'padxr='+ar[++i]);continue;}
if(ar[i]==PADY){eval(pf+'padyt='+ar[++i]);eval(pf+'padyb='+ar[++i]);continue;}
if(ar[i]==FULLHTML){eval(pf+'fullhtml=('+pf+'fullhtml==0)?1:0');continue;}
if(ar[i]==-FULLHTML){eval(pf+'fullhtml=0');continue;}
if(ar[i]==BELOW||ar[i]==ABOVE||ar[i]==VCENTER){eval(pf+'vpos='+ar[i]);continue;}
if(ar[i]==CAPICON){eval(pf+'capicon="'+ar[++i]+'"');continue;}
if(ar[i]==TEXTFONT){eval(pf+"textfont='"+OLescSglQt(ar[++i])+"'");continue;}
if(ar[i]==CAPTIONFONT){eval(pf+"captionfont='"+OLescSglQt(ar[++i])+"'");continue;}
if(ar[i]==CLOSEFONT){eval(pf+"closefont='"+OLescSglQt(ar[++i])+"'");continue;}
if(ar[i]==TEXTSIZE){eval(pf+"textsize='"+ar[++i]+"'");continue;}
if(ar[i]==CAPTIONSIZE){eval(pf+"captionsize='"+ar[++i]+"'");continue;}
if(ar[i]==CLOSESIZE){eval(pf+"closesize='"+ar[++i]+"'");continue;}
if(ar[i]==TIMEOUT){eval(pf+'timeout='+ar[++i]);continue;}
if(ar[i]==DELAY){eval(pf+'delay='+ar[++i]);continue;}
if(ar[i]==HAUTO){eval(pf+'hauto=('+pf+'hauto==0)?1:0');continue;}
if(ar[i]==-HAUTO){eval(pf+'hauto=0');continue;}
if(ar[i]==VAUTO){eval(pf+'vauto=('+pf+'vauto==0)?1:0');continue;}
if(ar[i]==-VAUTO){eval(pf+'vauto=0');continue;}
if(ar[i]==NOJUSTX){eval(pf+'nojustx=('+pf+'nojustx==0)?1:0');continue;}
if(ar[i]==-NOJUSTX){eval(pf+'nojustx=0');continue;}
if(ar[i]==NOJUSTY){eval(pf+'nojusty=('+pf+'nojusty==0)?1:0');continue;}
if(ar[i]==-NOJUSTY){eval(pf+'nojusty=0');continue;}
if(ar[i]==CLOSECLICK){eval(pf+'closeclick=('+pf+'closeclick==0)?1:0');continue;}
if(ar[i]==-CLOSECLICK){eval(pf+'closeclick=0');continue;}
if(ar[i]==CLOSETITLE){eval(pf+"closetitle='"+OLescSglQt(ar[++i])+"'");continue;}
if(ar[i]==FGCLASS){eval(pf+"fgclass='"+OLescSglQt(ar[++i])+"'");continue;}
if(ar[i]==BGCLASS){eval(pf+"bgclass='"+OLescSglQt(ar[++i])+"'");continue;}
if(ar[i]==CGCLASS){eval(pf+"cgclass='"+OLescSglQt(ar[++i])+"'");continue;}
if(ar[i]==TEXTPADDING){eval(pf+'textpadding='+ar[++i]);continue;}
if(ar[i]==TEXTFONTCLASS){eval(pf+"textfontclass='"+OLescSglQt(ar[++i])+"'");continue;}
if(ar[i]==CAPTIONPADDING){eval(pf+'captionpadding='+ar[++i]);continue;}
if(ar[i]==CAPTIONFONTCLASS){eval(pf+"captionfontclass='"+OLescSglQt(ar[++i])+"'");continue;}
if(ar[i]==CLOSEFONTCLASS){eval(pf+"closefontclass='"+OLescSglQt(ar[++i])+"'");continue;}
if(ar[i]==CAPBELOW){eval(pf+'capbelow=('+pf+'capbelow==0)?1:0');continue;}
if(ar[i]==-CAPBELOW){eval(pf+'capbelow=0');continue;}
if(ar[i]==DONOTHING){continue;}
i=OLparseCmdLine(pf,i,ar);}}
if((OLfunctionPI)&&OLudf&&o3_function)o3_text=o3_function();
if(pf=='o3_')OLfontSize();
}

////////
// LAYER FUNCTIONS
////////
// Writes to a layer
function OLlayerWrite(txt){
txt+="\n";
if(OLns4){over.document.write(txt);over.document.close();
}else if(typeof over.innerHTML!='undefined'){if(OLieM)over.innerHTML='';over.innerHTML=txt;
}else{range=o3_frame.document.createRange();range.setStartAfter(over);
domfrag=range.createContextualFragment(txt);
while(over.hasChildNodes()){over.removeChild(over.lastChild);}
over.appendChild(domfrag);}
}

// Make an object visible
function OLshowObject(obj){
var theObj=(OLns4?obj:obj.style);o3_showid=0;
if(((OLfilterPI)&&!OLchkFilter(theObj))||!OLfilterPI)theObj.visibility="visible";
if(OLshadowPI)OLshowShadow();if(OLiframePI)OLshowIfShim();if(OLhidePI)OLhideUtil(1,1,0);
}

// Hides an object
function OLhideObject(obj){
var theObj=(OLns4?obj:obj.style);
if(o3_showid>0){clearTimeout(o3_showid);o3_showid=0;}
if(OLiframePI)OLhideIfShim();if(OLshadowPI)OLhideShadow();
theObj.visibility="hidden";
if(OLhidePI)OLhideUtil(0,0,1);if((OLfilterPI)&&o3_filter)OLcleanupFilter(theObj);
if(o3_timerid>0)clearTimeout(o3_timerid);if(o3_delayid>0)clearTimeout(o3_delayid);
o3_timerid=0;o3_delayid=0;
self.status="";
if(OLdraggablePI&&o3_dragging)OLclearDrag();
if(over.onmouseout||over.onmouseover){
if(OLns4)over.releaseEvents(Event.MOUSEOUT||Event.MOUSEOVER);
over.onmouseout=over.onmouseover=null;}
if(OLscrollPI)OLclearScroll();
}

// Move a layer
function OLrepositionTo(obj,xL,yL){
var theObj=(OLns4?obj:obj.style);
theObj.left=(OLns4?xL:xL+'px');
theObj.top=(OLns4?yL:yL+'px');
}

// Check position of cursor relative to overDiv; mouseOut function
function OLcursorOff(){
if(OLovertwoPI&&over==over2)return false;
var left=parseInt(over.style.left),top=parseInt(over.style.top);
var right=left+o3_width,bottom=top+((OLbubblePI&&o3_bubble)?OLbubbleHt:over.offsetHeight);
if(o3_x<left||o3_x>right||o3_y<top||o3_y>bottom)return true;
return false;
}

////////
// COMMAND FUNCTIONS
////////
// Sets up mouseoff feature for stickies
function OLoptMOUSEOFF(close){
if(!close)o3_close="";
if(OLns4){
over.captureEvents(Event.MOUSEOUT||Event.MOUSEOVER);
over.onmouseover=function(){if(o3_timerid>0){clearTimeout(o3_timerid);o3_timerid=0;}}
over.onmouseout=cClick;
}else if(OLie4||OLns6)over.onmouseover=function()
{OLhover=1;if(o3_timerid>0){clearTimeout(o3_timerid);o3_timerid=0;}}
return 0;
}

function OLescSglQt(str){
return str.toString().replace(/'/g,"\\'");
}

function OLhasDims(str){
return /[%\-a-z]+$/.test(str);
}

function OLfontSize(){
var i;
if(OLhasDims(o3_textsize)){if(OLns4)o3_textsize="2";}else
if(!OLns4){i=parseInt(o3_textsize);o3_textsize=(i>0&&i<8)?OLpct[i]:OLpct[0];}
if(OLhasDims(o3_captionsize)){if(OLns4)o3_captionsize="2";}else
if(!OLns4){i=parseInt(o3_captionsize);o3_captionsize=(i>0&&i<8)?OLpct[i]:OLpct[0];}
if(OLhasDims(o3_closesize)){if(OLns4)o3_closesize="2";}else
if(!OLns4){i=parseInt(o3_closesize);o3_closesize=(i>0&&i<8)?OLpct[i]:OLpct[0];}
}

////////
//  REGISTRATION ROUTINES
////////
function OLsetRunTimeVar(){
if(typeof runTime!='undefined'&&runTime.length)for(var k=0;k<runTime.length;k++)runTime[k]();
}

function OLparseCmdLine(pf,i,args){
if(typeof cmdLine!='undefined'&&cmdLine.length){for(var k=0;k<cmdLine.length;k++){
var j=cmdLine[k](pf,i,args);if(j>-1){i=j;break;}}}
return i;
}

function OLisFunc(fnRef){
var rtn=true;
if(typeof fnRef=='object'){for(var i=0;i<fnRef.length;i++){
if(typeof fnRef[i]=='function')continue;rtn=false;break;}
}else if(typeof fnRef!='function')rtn=false;
return rtn;
}

function OLregCmds(cmdStr){
if(typeof cmdStr!='string')return;
var pM=cmdStr.split(',');pMtr=pMtr.concat(pM);
for(var i=0;i<pM.length;i++)eval(pM[i].toUpperCase()+'='+pmCnt++);
}

function OLregRunTimeFunc(functions){
if(OLisFunc(functions)){
if(typeof runTime=='undefined')runTime=new Array();
if(typeof functions=='object')runTime=runTime.concat(functions);
else runTime[runTime.length++]=functions;}
}

function OLregCmdLineFunc(functions){
if(OLisFunc(functions)){
if(typeof cmdLine=='undefined')cmdLine=new Array();
if(typeof functions=='object')cmdLine=cmdLine.concat(functions);
else cmdLine[cmdLine.length++]=functions;}
}

OLloaded=1;
