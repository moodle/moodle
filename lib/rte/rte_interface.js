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
// Author(s):	austin.france@ramesys.com			OZ
//				leonreinders@hetnet.nl				LEON
//
// History:
//
//	LEON	04-08-2001
//			Added styleData functionality for extending the style dropdown
//
//	OZ		30-08-2001
//			Re-worked styleData to restore FormatBlock logic which works better
//			for some styles, for example, heading styles - as these can be
//			applied without having to select text.
//
//	OZ		02-09-2001
//			Extended the richeditor interface to make the docHtml property read
//			write.
//
//	OZ		08-01-2002
//			Extended the richeditor interface to include an options property.
//			This property can be set to enable/disable functionality in the
//			editor.
//
//	OZ		21-01-2002
//			After set editor options, apply them.  In some circumstances the
//			options property is set after the editor has initialised and so the
//			options need to be re-applied.
//
//	OZ		12-02-2002
//			Add new property debugWindow which should be a reference to an HTML
//			element that can contain innerHTML.  A table is inserted into this
//			element and debug statements are output.
//
//	OZ		12-06-2002 [ richtext-Bugs-567677 ] Cursor at bottom of document on load
//			When initialising HTML throught the docHtml property, get a text range
//			object before inserting HTML (this will reflect the cursor position for
//			the empty document), insert the HTML (this moves the cursor position)
//			and then collapse the resulting range which now covers the entire document
//			to the beginning (i.e. move the insertion point to the start of the
//			selection).  All changes isolated to the put_docHtml() routine.
//
//	OZ		18-06-2002
//			Add support for a docXHtml property to allow an XHMTL representation
//			of the document to be extracted.  See rte_xhtml.js for the
//			implementation.
//
//	OZ		01-07-2002
//			If the editor is slow at initialisation (for example if an alert box
//			is placed inside initEditor() in rte.js) then it is possible for
//			the docHtml property to be called before the editor is properly
//			initialised.  This is ok except that we can't put focus on the editor
//			until it is visible.
//
////////////////////////////////////////////////////////////////////////////////

// object:		RichEditor()
// description: This object provides the interface to the calling page.
function RichEditor()
{
   var selectedImage = null; // currently selected image

	this.put_docHtml			= put_docHtml;
	this.get_docHtml			= get_docHtml;			// OZ
	this.get_docXHtml			= get_docXHtml;			// OZ
	this.put_defaultFont		= put_defaultFont;
	this.put_defaultFontSize	= put_defaultFontSize;
	this.put_styleData			= put_styleData;		// LEON
	this.put_options			= put_options;
	this.addField				= addField;
	this.getValue				= getValue;
	this.put_debugWindow		= put_debugWindow;		// OZ
}

// property:	docHtml
// access:		read/write
// description: Set this property to define the initial HTML to be
//				edited.
// author:		austin.france@ramesys.com
function put_docHtml(passedValue) {
	var r = document.selection.createRange();
	doc.innerHTML = passedValue;
	r.collapse(true);
	r.select();

	// Only if editor initialisation has completed (and therfore visible)
	if (editor.style.visibility == "visible") {
		doc.focus();
		reset();
	}
}

function get_docHtml() {
	return doc.innerHTML;
}

// property:	docXHtml
// access:		read only
// description: Return an XHTML representation of the document.  
// author:		austin.france@ramesys.com
function get_docXHtml() {								// OZ
	// Ignore any contenteditable attributes seen as these are 
	// inherited from the editor and not relevent to the document
	// HTML.
	return innerXHTML(doc, new RegExp("contenteditable"));
}

// property:	defaultFont
// access:		write only
// description:	Sets the default font for the editor.  The default
//				if this is not specified is whatever the microsoft
//				html editing component decides (Times New Roman
//				typically)
// author:		austin.france@ramesys.com
function put_defaultFont(passedValue) {
	doc.style.fontFamily = passedValue;
}

// property:	defaultFontSize
// access:		write only
// description:	Sets the default font size for the editor.
// author:		austin.france@ramesys.com
function put_defaultFontSize(passedValue) {
	switch(passedValue) {
	case "1": passedValue = "xx-small"; break;
	case "2": passedValue = "x-small";	break;
	case "3": passedValue = "small";	break;
	case "4": passedValue = "medium";	break;
	case "5": passedValue = "large";	break;
	case "6": passedValue = "x-large";	break;
	case "7": passedValue = "xx-large";	break;
	}
	doc.style.fontSize = passedValue;
}

// property:	styleData
// access:		writeOnly
// description:	Defines extended style data for the style dropdown
// author:		leonreinders@hetnet.nl
function put_styleData(passedValue) {

	var a,b;

	// Define the default style list
	this.styleList = [
		// element		description			Active
		[null,			"Normal",			0],
		[null,			"Heading 1",		0],
		[null,			"Heading 2",		0],
		[null,			"Heading 3",		0],
		[null,			"Heading 4",		0],
		[null,			"Heading 5",		0],
		[null,			"Heading 6",		0],
		[null,			"Address",			0],
		[null,			"Formatted",		0],
		["BLOCKQUOTE",	"Blockquote",		0],
		["CITE",		"Citation",			0],
		["BDO",			"Reversed",			0],
		["BIG",			"Big",				0],
		["SMALL",		"Small",			0],
		["DIV",			"Div",				0],
		["SUP",			"Superscript",		0],
		["SUB",			"Subscript",		0]
	];

	// Add the passed styles to the documents stylesheet
	for (var i = 0; passedValue && i < passedValue.length; i++)
	{
		for (var j = 0; j < passedValue[i].rules.length; j++)
		{
			// Extract the rule and the rule definition from the passed style
			// data.
			a = passedValue[i].rules[j].selectorText.toString().toLowerCase();
			b = passedValue[i].rules[j].style.cssText.toLowerCase();

			// Ignore non-style entries
			if (!a || !b) continue;

			// Add this rule to our style sheet
			document.styleSheets[0].addRule(a,b);

			// Id: These are added to the document style sheet but are not
			// available in the style dropdown
			if (a.indexOf("#") != -1) {
				continue;
			}

			// Class: Append a cless element to the style list
			if (a.indexOf(".") == 0) {
				this.styleList[this.styleList.length] = [a, "Class " + a, 1];
			}

			// SubClass: Append the sub-class to the style list
			else if(a.indexOf(".") > 0) {
				this.styleList[this.styleList.length] = [a, a, 1];
			}

			// Otherwise, assume it's a tag and select the existing tag entry
			// in the style list.
			else {
				for (var k = 0; k < this.styleList.length; k++) {
					if (this.styleList[k][0] == a) {
						this.styleList[k][2] = 1;
						break;
					}
				}
			}
		}
	}

	// Initialise the style dropdown with the new style list
	initStyleDropdown(this.styleList);
}

function addField(name, label, maxlen, value, size) {
	var row = rebarBottom.parentElement.insertRow(rebarBottom.rowIndex);
	var cell = row.insertCell();
	cell.className = 'rebar';
	cell.width = '100%';
	cell.innerHTML = '<nobr width="100%"><span class="field" width="100%">'
						+ '<img class="spacer" src="spacer.gif" width="2">'
						+ '<span class="start"></span>'
						+ '<span class="label">' + label + ':</span>'
						+ '&nbsp;<input class="field" type="text"'
							+ ' name="' + name + '" maxlength="' + maxlen + '"'
								+ (value ? ' value="' + value + '"' : '')
								+ 'size="' + (size ? size : 58) + '"'
								+ '>&nbsp;'
						+ '</span>'
						+ '</nobr>';
}

function getValue(name) {
	return document.all(name).value;
}

// property:	options
// access:		writeOnly
// description:	Sets options for the editor.  Used by the editor to control
//				certain features
//
//				viewsource=<true|false>;...
//
// author:		austin.france@ramesys.com
function put_options(passedValue) {
	this.options = passedValue;
	applyOptions(this.options);
}

// property:	debugWindow
// access:		writeOnly
// description:	Tells the editor to emit debugs to the debug window.
// author:		austin.france@ramesys.com
function put_debugWindow(passedValue) {
	this.debugWindow = passedValue;
	DBG();
}
