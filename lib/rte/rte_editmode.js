////////////////////////////////////////////////////////////////////////////////
//
// HTML Text Editing Component for hosting in Web Pages
// Copyright (C) 2001  Ramesys (Contracting Services) Limited
//
// This library is free software; you can redistribute it and/or
// modify it under the terms of the GNU Lesser General Public
// License as published by the Free Software Foundation; either
// version 2.1 of the License, or (at your option) any later version.
//
// This library is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
// Lesser General Public License for more details.
//
// You should have received a copy of the GNU LesserGeneral Public License
// along with this program; if not a copy can be obtained from
//
//    http://www.gnu.org/copyleft/lesser.html
//
// or by writing to:
//
//    Free Software Foundation, Inc.
//    59 Temple Place - Suite 330,
//    Boston,
//    MA  02111-1307,
//    USA.
//
// Original Developer:
//
//	Austin David France
//	Ramesys (Contracting Services) Limited
//	Mentor House
//	Ainsworth Street
//	Blackburn
//	Lancashire
//	BB1 6AY
//	United Kingdom
//  email: Austin.France@Ramesys.com
//
// Home Page:    http://richtext.sourceforge.net/
// Support:      http://richtext.sourceforge.net/
//
////////////////////////////////////////////////////////////////////////////////
//
// Author(s):	leonreinders@hetnet.nl				LEON
//
//				Austin.France@Ramesys.com			OZ
//
// History:
//
//	LEON	04-08-2001
//			Initial Implementation
//
//	OZ		Disable/enable post button during view source mode.
//
////////////////////////////////////////////////////////////////////////////////

// setEditMode(): switch between html and textview
function setEditMode() {
	switchMode.blur(); // htmlview
	if (switchMode.checked == true) {
		ctlStyle.disabled = ctlFont.disabled = ctlSize.disabled = true;
		doc.style.fontFamily = "Courier";
		doc.style.fontSize = "10px";
		RichEditor.txtView = false;
		doc.innerText = codeSweeper();
		doc.innerHTML = ccParser(doc.innerHTML);
        alert("Remember to uncheck this Source box again before saving your changes!!");
	} else {
		ctlStyle.disabled = ctlFont.disabled = ctlSize.disabled  = false;
		doc.style.fontFamily = doc.style.fontSize = "";
		RichEditor.txtView = true;
		doc.focus();
		doc.innerHTML = doc.innerText;
	}
}

// resetMode();
function resetMode(){
	if (switchMode.checked == true) {
		switchMode.click();
	}
}

// ccParser(): colorcode-parser for html-editing view
function ccParser(html) {

	html = html.replace(/@/gi,"_AT_");
	html = html.replace(/#/gi,"_HASH_");

	var htmltag = /(&lt;[\w\/]+[ ]*[\w\=\"\'\.\/\;\: \)\(-]*&gt;)/gi;
	html = html.replace(htmltag,"<span class=ccp_tag>$1</span>");

	var imgtag = /<span class=ccp_tag>(&lt;IMG[ ]*[\w\=\"\'\.\/\;\: \)\(-]*&gt;)<\/span>/gi;
	html = html.replace(imgtag,"<span class=ccp_img>$1</span>");

	var formtag = /<span class=ccp_tag>(&lt;[\/]*(form|input){1}[ ]*[\w\=\"\'\.\/\;\: \)\(-]*&gt;)<\/span>/gi;
	html = html.replace(formtag,"<br><span class=ccp_form>$1</span>");

	var tabletag = /<span class=ccp_tag>(&lt;[\/]*(table|tbody|th|tr|td){1}([ ]*[\w\=\"\'\.\/\;\:\)\(-]*){0,}&gt;)<\/span>/gi;
	html = html.replace(tabletag,"<span class=ccp_table>$1</span>");

	//var Atag = /<span class=ccp_tag>(&lt;(\/a&gt;|[\W _\w\=\"\'\.\/\;\:\)\(-]&gt;){1})<\/span>/gi;
	var Atag = /<span class=ccp_tag>(&lt;\/a&gt;){1}<\/span>/gi;
	html = html.replace(Atag,"<span class=ccp_A>$1</span>");

	var Atag = /<span class=ccp_tag>(&lt;a [\W _\w\=\"\'\.\/\;\:\)\(-]+&gt;){1,}<\/span>/gi;
	html = html.replace(Atag,"<span class=ccp_A>$1</span>");

	var parameter = /=("[ \w\'\.\/\;\:\)\(-]+"|'[ \w\"\.\/\;\:\)\(-]+')/gi;
	html = html.replace(parameter,"=<span class=ccp_paramvalue>$1</span>");

	var entity = /&amp;([\w]+);/gi;
	html = html.replace(entity,"<span class=ccp_entity>&amp;$1;</span>");

	var comment = /(&lt;\!--[\W _\w\=\"\'\.\/\;\:\)\(-]*--&gt;)/gi;
	html = html.replace(comment,"<br><span class=ccp_htmlcomment>$1</span>");

	html = html.replace(/_AT_/gi,"@");
	html = html.replace(/_HASH_/gi,"#");

	return html;
}
