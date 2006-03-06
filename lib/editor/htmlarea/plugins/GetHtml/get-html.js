/**
  * Based on XML_Utility functions submitted by troels_kn.
  * credit also to adios, who helped with reg exps:
  * http://www.sitepoint.com/forums/showthread.php?t=201052
  * 
  * A replacement for HTMLArea.getHTML
  *
  * Features:
  *   - Generates XHTML code
  *   - Much faster than HTMLArea.getHTML
  *   - Eliminates the hacks to accomodate browser quirks
  *   - Returns correct code for Flash objects and scripts
  *   - Formats html in an indented, readable format in html mode
  *   - Preserves script and pre formatting
  *   - Preserves formatting in comments
  *   - Removes contenteditable from body tag in full-page mode
  *   - Supports only7BitPrintablesInURLs config option
  *   - Supports htmlRemoveTags config option
  */
  
function GetHtml(editor) {
    this.editor = editor;
}

GetHtml._pluginInfo = {
	name          : "GetHtml",
	version       : "1.0",
	developer     : "Nelson Bright",
	developer_url : "http://www.brightworkweb.com/",
	license       : "htmlArea"
};

HTMLArea.RegExpCache = [
/*00*/  new RegExp().compile(/<\s*\/?([^\s\/>]+)[\s*\/>]/gi),//lowercase tags
/*01*/  new RegExp().compile(/(\S*\s*=\s*)?_moz[^=>]*(=\s*[^>]*)?/gi),//strip _moz attributes
/*02*/  new RegExp().compile(/\s*=\s*(([^'"][^>\s]*)([>\s])|"([^"]+)"|'([^']+)')/g),// find attributes
/*03*/  new RegExp().compile(/\/>/g),//strip singlet terminators
/*04*/ // new RegExp().compile(/<(br|hr|img|input|link|meta|param|embed)([^>]*)>/g),//terminate singlet tags
/*04*/  new RegExp().compile(/<(br|hr|img|input|link|meta|param|embed|area)((\s*\S*="[^"]*")*)>/g),//terminate singlet tags
/*05*/  new RegExp().compile(/(checked|compact|declare|defer|disabled|ismap|multiple|no(href|resize|shade|wrap)|readonly|selected)([\s>])/gi),//expand singlet attributes
/*06*/  new RegExp().compile(/(="[^']*)'([^'"]*")/),//check quote nesting
/*07*/  new RegExp().compile(/&(?=[^<]*>)/g),//expand query ampersands
/*08*/  new RegExp().compile(/<\s+/g),//strip tagstart whitespace
/*09*/  new RegExp().compile(/\s+(\/)?>/g),//trim whitespace
/*10*/  new RegExp().compile(/\s{2,}/g),//trim extra whitespace
/*11*/  new RegExp().compile(/\s+([^=\s]+)(="[^"]+")/g),// lowercase attribute names
/*12*/  new RegExp().compile(/(\S*\s*=\s*)?contenteditable[^=>]*(=\s*[^>\s\/]*)?/gi),//strip contenteditable
/*13*/  new RegExp().compile(/((href|src)=")([^\s]*)"/g), //find href and src for stripBaseHref()
/*14*/  new RegExp().compile(/<\/?(div|p|h[1-6]|table|tr|td|th|ul|ol|li|blockquote|object|br|hr|img|embed|param|pre|script|html|head|body|meta|link|title|area)[^>]*>/g),
/*15*/  new RegExp().compile(/<\/(div|p|h[1-6]|table|tr|td|th|ul|ol|li|blockquote|object|html|head|body|script)( [^>]*)?>/g),//blocklevel closing tag
/*16*/  new RegExp().compile(/<(div|p|h[1-6]|table|tr|td|th|ul|ol|li|blockquote|object|html|head|body|script)( [^>]*)?>/g),//blocklevel opening tag
/*17*/  new RegExp().compile(/<(br|hr|img|embed|param|pre|meta|link|title|area)[^>]*>/g),//singlet tag
/*18*/  new RegExp().compile(/(^|<\/(pre|script)>)(\s|[^\s])*?(<(pre|script)[^>]*>|$)/g),//find content NOT inside pre and script tags
/*19*/  new RegExp().compile(/(<pre[^>]*>)(\s|[^\s])*?(<\/pre>)/g),//find content inside pre tags
/*20*/  new RegExp().compile(/(^|<!--(\s|\S)*?-->)((\s|\S)*?)(?=<!--(\s|\S)*?-->|$)/g),//find content NOT inside comments
/*21*/  new RegExp().compile(/\S*=""/g), //find empty attributes
/*22*/  new RegExp().compile(/<!--[\s\S]*?-->|<\?[\s\S]*?\?>|<[^>]*>/g) //find all tags, including comments and php
];

/** 
  * Cleans HTML into wellformed xhtml
  */
HTMLArea.prototype.cleanHTML = function(sHtml) {
	var c = HTMLArea.RegExpCache;
	sHtml = sHtml.
		replace(c[0], function(str) { return str.toLowerCase(); } ).//lowercase tags/attribute names
		replace(c[1], ' ').//strip _moz attributes
		replace(c[12], ' ').//strip contenteditable
		replace(c[2], '="$2$4$5"$3').//add attribute quotes
		replace(c[21], ' ').//strip empty attributes
		replace(c[11], function(str, p1, p2) { return ' '+p1.toLowerCase()+p2; }).//lowercase attribute names
		replace(c[3], '>').//strip singlet terminators
		replace(c[9], '$1>').//trim whitespace
		replace(c[5], '$1="$1"$3').//expand singlet attributes
		replace(c[4], '<$1$2 />').//terminate singlet tags
		replace(c[6], '$1$2').//check quote nesting
	//	replace(c[7], '&amp;').//expand query ampersands
		replace(c[8], '<').//strip tagstart whitespace
		replace(c[10], ' ');//trim extra whitespace
	if(HTMLArea.is_ie && c[13].test(sHtml)) {//
		sHtml = sHtml.replace(c[13],'$1'+this.stripBaseURL(RegExp.$3)+'"');
	}
	if(this.config.only7BitPrintablesInURLs && c[13].test(sHtml)) {
	  sHtml = sHtml.replace(c[13], '$1'+RegExp.$3.replace(/([^!-~]+)/g,function(chr){return escape(chr);})+'"');
	}
	return sHtml;
};

/**
  * Prettyfies html by inserting linebreaks before tags, and indenting blocklevel tags
  */
HTMLArea.indent = function(s, sindentChar) {
	HTMLArea.__nindent = 0;
	HTMLArea.__sindent = "";
	HTMLArea.__sindentChar = (typeof sindentChar == "undefined") ? "  " : sindentChar;
	var c = HTMLArea.RegExpCache;
	if(HTMLArea.is_gecko) { //moz changes returns into <br> inside <pre> tags
		s = s.replace(c[19], function(str){return str.replace(/<br \/>/g,"\n")});
	}
	s = s.replace(c[18], function(strn) { //skip pre and script tags
	  strn = strn.replace(c[20], function(st,$1,$2,$3) { //exclude comments
		string = $3.replace(/[\n\r]/gi, " ").replace(/\s+/gi," ").replace(c[14], function(str) {
			if (str.match(c[16])) {
				var s = "\n" + HTMLArea.__sindent + str;
				// blocklevel openingtag - increase indent
				HTMLArea.__sindent += HTMLArea.__sindentChar;
				++HTMLArea.__nindent;
				return s;
			} else if (str.match(c[15])) {
				// blocklevel closingtag - decrease indent
				--HTMLArea.__nindent;
				HTMLArea.__sindent = "";
				for (var i=HTMLArea.__nindent;i>0;--i) {
					HTMLArea.__sindent += HTMLArea.__sindentChar;
				}
				return "\n" + HTMLArea.__sindent + str;
			} else if (str.match(c[17])) {
				// singlet tag
				return "\n" + HTMLArea.__sindent + str;
			}
			return str; // this won't actually happen
		});
		return $1 + string;
	  });return strn;
    });
    if (s.charAt(0) == "\n") {
        return s.substring(1, s.length);
    }
    s = s.replace(/ *\n/g,'\n');//strip spaces at end of lines
    return s;
};

HTMLArea.getHTML = function(root, outputRoot, editor) {
	var html = "";
	var c = HTMLArea.RegExpCache;

	if(root.nodeType == 11) {//document fragment
	    //we can't get innerHTML from the root (type 11) node, so we 
	    //copy all the child nodes into a new div and get innerHTML from the div
	    var div = document.createElement("div");
	    var temp = root.insertBefore(div,root.firstChild);
	    for (j = temp.nextSibling; j; j = j.nextSibling) { 
	    		temp.appendChild(j.cloneNode(true));
	    }
	    html += temp.innerHTML.replace(c[22], function(tag){
			if(/^<[!\?]/.test(tag)) return tag; //skip comments and php tags
			else return editor.cleanHTML(tag)});

	} else {

		var root_tag = (root.nodeType == 1) ? root.tagName.toLowerCase() : ''; 
		if (outputRoot) { //only happens with <html> tag in fullpage mode
			html += "<" + root_tag;
			var attrs = root.attributes; // strangely, this doesn't work in moz
			for (i = 0; i < attrs.length; ++i) {
				var a = attrs.item(i);
				if (!a.specified) {
				  continue;
				}
				var name = a.nodeName.toLowerCase();
				var value = a.nodeValue;
				html += " " + name + '="' + value + '"';
			}
			html += ">";
		}
		if(root_tag == "html") {
			innerhtml = editor._doc.documentElement.innerHTML;
		} else {
			innerhtml = root.innerHTML;
		}
		//pass tags to cleanHTML() one at a time
		//includes support for htmlRemoveTags config option
		html += innerhtml.replace(c[22], function(tag){
			if(/^<[!\?]/.test(tag)) return tag; //skip comments and php tags
			else if(!(editor.config.htmlRemoveTags && editor.config.htmlRemoveTags.test(tag.replace(/<([^\s>\/]+)/,'$1'))))
				return editor.cleanHTML(tag);
			else return ''});
		//IE drops  all </li> tags in a list except the last one
		if(HTMLArea.is_ie) {
			html = html.replace(/<li( [^>]*)?>/g,'</li><li$1>').
				replace(/(<(ul|ol)[^>]*>)[\s\n]*<\/li>/g, '$1').
				replace(/<\/li>([\s\n]*<\/li>)+/g, '<\/li>');
		}
		if(HTMLArea.is_gecko)
			html = html.replace(/(.*)<br \/>\n$/, '$1'). //strip trailing <br> added by moz
				replace(/^\n(.*)/, '$1'); //strip leading newline added by moz
		if (outputRoot) {
			html += "</" + root_tag + ">";
		}
		html = HTMLArea.indent(html);
	};
//	html = HTMLArea.htmlEncode(html);

	return html;
};

//override (hack) outwardHtml() to handle onclick suppression
HTMLArea.prototype._origOutwardHtml = HTMLArea.prototype.outwardHtml;
HTMLArea.prototype.outwardHtml = function(html) {
	html = html.replace("onclick=\"try{if(document.designMode && document.designMode == 'on') return false;}catch(e){} window.open(", "onclick=\"window.open(");
	html = this._origOutwardHtml(html);
	return html;
};
