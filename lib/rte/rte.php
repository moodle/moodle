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
//   Austin David France
//   Ramesys (Contracting Services) Limited
//   Mentor House
//   Ainsworth Street
//   Blackburn
//   Lancashire
//   BB1 6AY
//   United Kingdom
//  email: Austin.France@Ramesys.com
//
// Home Page:    http://richtext.sourceforge.net/
// Support:      http://richtext.sourceforge.net/
//
////////////////////////////////////////////////////////////////////////////////
//
// Authors & Contributers:
//
//   OZ      Austin David France      [austin.france@ramesys.com]
//            Primary Developer
//
//   LEON   Leon Reinders         [leonreinders@hetnet.nl]
//            Author of View Source, History and Extended Style Functions
//
//   DIRK   Dirk Datzert         [Dirk.Datzert@rasselstein-hoesch.de]
//            Justify Full Option
//
//	 BC		Bill Chalmers		[bill_paula@btinternet.com]
//			 Font Selection
//
// History:
//
//   OZ      21-01-2002
//         Fix a bug in applyOptions() that was not detecting a truth setting
//         properly.  Changed substr(eq) to substr(eq+1).  Also, set the
//         runtime style property, not the style property.
//
//   OZ      22-01-2002
//         Moved initEditor() function into here from richedit.html
//
//   OZ      22-01-2002
//         Added handleDrag() method to handle drag and drop events within the
//         html of the editor.  Drag and drop is currently disabled until we
//         can find a practicle use for it.
//
//   OZ      10-02-2002
//         Added code to handle the new Full Justify Option.  Implementation of
//         a mod to the editor made by Dirk Datzert who supplied the code and
//         the Image.
//
//   OZ      11-02-2002
//         Startup with text area set to contenteditable="false".  The content
//         is made editable when the editor has been initialised.
//
//   OZ      12-02-2002
//         Fix handling of mouse hover when hover over a button that is in the
//         down state.  The down state of the button was being lost.  This is
//         a re-introduction of an earlier bug which I thought was fixed.
//         The bug also occured when the button was pressed in some 
//         circumstances.  The fix implemented is to have a button state 
//         property which is set when the state of a button is set in the
//         setState() routine and this is used to restore the button state when
//         the button is released or mouse moves out.
//
//	OZ		12-06-2002 [richtext-Bugs-567960] Text area of editor window not get focus
//			Ensure the doc element (the DIV) has focus once initialisation is
//			complete.  This ensures that when no HTML is supplied via the docHtml
//			property, that focus is where the user expects.
//
//	BC	     10-07-2002
//			 added getfontface() function to retrieve the new "web style" font selection
//			 this function is called from reset() in the same way as getStyle()
//
////////////////////////////////////////////////////////////////////////////////

// Internal (private) properties.  
// RichEditor is the global RichEditor object (function) of which there is only
// 1 instance.
RichEditor.txtView = true;         // WYSIWYG mode.  false == View Source

// initEditor(): Initialise the editor (called on window load, see below)
function initEditor()
{
	// Apply style data if supplied
	if (!public_description.styleData) {
	  public_description.put_styleData(null);
	}

	// Apply default editor options
	var strDefaults = 'dragdrop=no;source=yes';
	strDefaults += ';history=' + (document.queryCommandSupported('Undo') ? 'yes' : 'no');
	applyOptions(strDefaults);

	// Prepare the editable region
	loading.style.display = 'none';
    doc.contentEditable = "true";
    editor.style.visibility = 'visible';

	// OZ - 12-06-2002
	// Put focus into the document (required when no HTML is supplied via docHtml property)
	doc.focus();
}

// checkRange(): make sure our pretend document (the content editable
// DIV with id of "doc") has focus and that a text range exists (which
// is what execCommand() operates on).
function checkRange()
{
   RichEditor.selectedImage = null;
   if (!RichEditor.txtView) return;      // Disabled in View Source mode
   doc.focus();
   if (document.selection.type == "None") {
      document.selection.createRange();
   }
var r = document.selection.createRange();
   DBG(1, 'RANGE Bounding('
            + 'top='+r.boundingHeight
            + ', left='+r.boundingHeight
            + ', width='+r.boundingWidth
            + ', height='+r.boundingHeight + ')'
         + ', Offset('
            + 'top='+r.offsetTop
            + ', left='+r.offsetLeft + ')'
         + ', Text=(' + r.text + ')'
         + ', HTML=(' + r.htmlText + ')'
      );
}

// post(): Called in response to clicking the post button in the
// toolbar. It fires an event in the container named post, passing the
// HTML of our newly edited document as the data argument.
function post()
{
   DBG(1, 'Raise "post" event');
   window.external.raiseEvent("post", doc.innerHTML);
}

// insert(): called in response to clicking the insert table, image,
// smily icons in the toolbar.  Loads up an appropriate dialog to
// prompt for information, the dialog then returns the HTML code or
// NULL.  We paste the HTML code into the document.
function insert(what)
{
   if (!RichEditor.txtView) return;      // Disabled in View Source mode

   DBG(1, 'insert(' + what + ')');

   // Chose action based on what is being inserted.
   switch(what)
   {
   case "table":
      strPage = "dlg_ins_table.html";
      strAttr = "status:no;dialogWidth:340px;dialogHeight:360px;help:no";
      break;
   case "smile":
      strPage = "dlg_ins_smile.html";
      strAttr = "status:no;dialogWidth:300px;dialogHeight:350px;help:no";
      break;
   case "char":
      strPage = "dlg_ins_char.html";
      strAttr = "status:no;dialogWidth:450px;dialogHeight:290px;help:no";
      break;
   case "image":
      strPage = "dlg_ins_image.php?id=<?php echo $id ?>";
      strAttr = "status:no;dialogWidth:400px;dialogHeight:200px;help:no";' '
      break;
   case "about":
      strPage = "dlg_about.html";
      strAttr = "status:no;dialogWidth:500px;dialogHeight:405px;help:no";' '
      break;
   }

   // run the dialog that implements this type of element
   html = showModalDialog(strPage, window, strAttr);

   // and insert any result into the document.
   if (html) {
      insertHtml(html);
   }
}

// insertHtml(): Insert the supplied HTML into the current position
// within the document.
function insertHtml(html)
{
   doc.focus();
   var sel = document.selection.createRange();
   // don't try to insert HTML into a control selection (ie. image or table)
   if (document.selection.type == 'Control') {
      return;
   }
   sel.pasteHTML(html);
}

// doStyle(): called to handle the simple style commands such a bold,
// italic etc.  These require no special handling, just a call to
// execCommand().  We also call reset so that the toolbar represents
// the state of the current text.
//
// 2002-07-30 Updated based on patch submitted by Michael Keck (mkkeck) 
//
function doStyle(s){ 
   if(!RichEditor.txtView) return; 
   /* Disabled in View Source mode */ 
   DBG(1, 'doStyle(' + s + ')'); 
   checkRange(); 
   if(s!='InsertHorizontalRule'){ 
      /* what command string? */ 
      document.execCommand(s); 
   } else if( s=='InsertHorizontalRule') { 
      /* if s=='InsertHorizontalRule then use this command */ 
      document.execCommand(s,false, null); 

      /* Note: 
      In your source view the <HR> has an ID like this 
      <HR id=null> 
      */ 
   } 
   reset(); 
} 


// link(): called to insert a hyperlink.  It will use the selected text
// if there is some, or the URL entered if not.  If clicked when over a
// link, that link is allowed to be edited.
function link(on)
{
   if (!RichEditor.txtView) return;      // Disabled in View Source mode

   var strURL = "http://";
   var strText;

   // First, pick up the current selection.
   doc.focus();
   var r = document.selection.createRange();
   var el = r.parentElement();

   // Is this aready a link?
   if (el && el.nodeName == "A") {
      r.moveToElementText(el);
      if (!on) {      // If removing the link, then replace all with
         r.pasteHTML(el.innerHTML);
         return;
      }
      strURL = el.href;
   }

   // Get the text associated with this link
   strText = r.text;

   // Prompt for the URL
   strURL = window.prompt("Enter URL", strURL);
   if (strURL) {
      // Default the TEXT to the url if non selected
      if (!strText || !strText.length) {
         strText = strURL;
      }

      // Replace with new URL
      r.pasteHTML('<A href=' + strURL + ' target=_new>' + strText + '</a>');
   }

   reset();
}

// sel(); similar to doStyle() but called from the dropdown list boxes
// for font and style commands.
function sel(el)
{
   if (!RichEditor.txtView) return;      // Disabled in View Source mode
   checkRange();
   switch(el.id)
   {
   case "ctlFont":
      document.execCommand('FontName', false, el[el.selectedIndex].value);
      break;
   case "ctlSize":
      document.execCommand('FontSize', false, el[el.selectedIndex].value);
      break;
   case "ctlStyle":
      document.execCommand('FormatBlock', false, el[el.selectedIndex].text);
      break;
   }
   doc.focus();
   reset();
}

// pickColor(): called when the text or fill color icons are clicked.  Displays
// the color chooser control.  The color setting is completed by the event
// handler of this control (see richedit.html)
function pickColor(fg)
{
   if (!RichEditor.txtView) return;      // Disabled in View Source mode
   checkRange();
   var el = window.event.srcElement;
   if (el && el.nodeName == "IMG") {
      setState(el, true);
   }
   color.style.top = window.event.clientY + 10;
   color.style.left = window.event.clientX - 250;
   color.style.display = 'block';
   color._fg = fg;
}

// setColor(): called from the fore/back color selection dialog event handler
// to set/reset the fore/background color.
function setColor(name, data)
{
   color.style.display = 'none';
   checkRange();
   if (!data) {
      removeFormat(document.selection.createRange(), color._fg);
   } else {
      document.execCommand(color._fg, false, data);
   }
   setState(btnText, false);
   setState(btnFill, false);
   doc.focus();
}

// removeFormat(): Called to remove specific formats from the selected text.
// The 'removeFormat' command removes all formatting.  The principle behind
// this routine is to have a list of the possible formats the selection may
// have, check the selection for the current formats, ignoreing the one we
// want to use, then remove all formatting and then re-apply all but the
// one we wanted to remove.
function removeFormat(r, name)
{
   var cmd = [ "Bold", "Italic", "Underline", "Strikethrough", "FontName", "FontSize", "ForeColor", "BackColor" ];
   var on = new Array(cmd.length);
   for (var i = 0; i < cmd.length; i++) {
      on[i] = name == cmd[i] ? null : r.queryCommandValue(cmd[i]);
   }
   r.execCommand('RemoveFormat');
   for (var i = 0; i < cmd.length; i++) {
      if (on[i]) r.execCommand(cmd[i], false, on[i]);
   }
}

// setValue(): called from reset() to make a select list show the current font
// or style attributes
function selValue(el, str)
{
   if (!RichEditor.txtView) return;      // Disabled in View Source mode
   for (var i = 0; i < el.length; i++) {
      if ((!el[i].value && el[i].text == str) || el[i].value == str) {
         el.selectedIndex = i;
         return;
      }
   }
   el.selectedIndex = 0;
}

// setState(): called from reset() to make a button represent the state
// of the current text.  Pressed is on, unpressed is off.
function setState(el, on)
{
   if (!RichEditor.txtView) return;      // Disabled in View Source mode
   if (!el.disabled) {
      if (on) {
         el.defaultState = el.className = "down";
      } else {
         el.defaultState = el.className = null;
      }
   }
}

// getStyle(): called to obtain the class or type of formatting applied to an element,
// This is used by reset() to set the state of the toolbar to indicate the class of
// the current element.
function getStyle() {
   var style = document.queryCommandValue('FormatBlock');
   if (style == "Normal") {
      doc.focus();
      var rng = document.selection.createRange();
      if (typeof(rng.parentElement) != "undefined") {
         var el = rng.parentElement();
         var tag = el.nodeName.toUpperCase();
         var str = el.className.toLowerCase();
         if (!(tag == "DIV" && el.id == "doc" && str == "textedit")) {
            if (tag == "SPAN") {
               style = "." + str;
            } else if (str == "") {
               style = tag;
            } else {
               style = tag + "." + str;
            }
         }
         return style;
      }
   }
   return style;
}

// getfontface(): called to obtain the face attribute applied to a font tag,
// This is used by reset() to set the state of the toolbar to indicate the class of
// the current element.
function getfontface()
{
var family = document.selection.createRange(); //create text range

// don't get font face for image or table
if (document.selection.type == 'Control') {
   return;
}

var el = family.parentElement(); //get parent element
var tag = el.nodeName.toUpperCase(); //convert tag element to upper case

	if (typeof(el.parentElement) != "undefined" && tag == "FONT") { //only do function if tag is font - this is for greater execution speed
		var elface = el.getAttribute('FACE'); //get the font tags FACE attribute
		return elface; //return the value of the face attribute to the reset() function
	}
}

// markSelectedElement(): called by onClick and onKeyup events
// on the contectEditable area
function markSelectedElement() {

   RichEditor.selectedImage = null;

   var r = document.selection.createRange();

   if (document.selection.type != 'Text') {
      if (r.length == 1) {
         if (r.item(0).tagName == "IMG") {
            RichEditor.selectedImage = r.item(0);
         }
      }
   }
}

// reset(): called from all over the place to make the toolbar
// represent the current text. If el specified, it was called from
// hover(off)
function reset(el)
{
   if (!RichEditor.txtView) return;      // Disabled in View Source mode
   if (!el) color.style.display = 'none';
   if (!el || el == ctlStyle)         selValue(ctlStyle, getStyle());
   if (!el || el == ctlFont)         selValue(ctlFont, getfontface());
   if (!el || el == ctlSize)         selValue(ctlSize, document.queryCommandValue('FontSize'));
   if (!el || el == btnBold)         setState(btnBold, document.queryCommandValue('Bold'));
   if (!el || el == btnItalic)         setState(btnItalic,   document.queryCommandValue('Italic'));
   if (!el || el == btnUnderline)      setState(btnUnderline, document.queryCommandValue('Underline'));
   if (!el || el == btnStrikethrough)   setState(btnStrikethrough, document.queryCommandValue('Strikethrough'));
   if (!el || el == btnLeftJustify)   setState(btnLeftJustify, document.queryCommandValue('JustifyLeft'));
   if (!el || el == btnCenter)         setState(btnCenter,   document.queryCommandValue('JustifyCenter'));
   if (!el || el == btnRightJustify)   setState(btnRightJustify, document.queryCommandValue('JustifyRight'));
   if (!el || el == btnFullJustify)   setState(btnFullJustify, document.queryCommandValue('JustifyFull'));
   if (!el || el == btnNumList)      setState(btnNumList, document.queryCommandValue('InsertOrderedList'));
   if (!el || el == btnBulList)      setState(btnBulList, document.queryCommandValue('InsertUnorderedList'));
}

// hover(): Handles mouse hovering over toolbar buttons
function hover(on)
{
   if (!RichEditor.txtView) return;      // Disabled in View Source mode
   var el = window.event.srcElement;
   if (el && !el.disabled && el.nodeName == "IMG" && el.className != "spacer") {
      if (on) {
         el.className = "hover";
      } else {
         el.className = el.defaultState ? el.defaultState : null;
      }
   }
}
// hover(): Handles mouse clicks on toolbar buttons
function press(on)
{
   if (!RichEditor.txtView) return;      // Disabled in View Source mode
   var el = window.event.srcElement;
   if (el && !el.disabled && el.nodeName == "IMG" && el.className != "spacer") {
      if (on) {
         el.className = "down";
      } else {
         el.className = el.className == "down" ? "hover" : el.defaultState ? el.defaultState : null;
      }
   }
}

// addTag(): This is the handler for the style dropdown.  This takes value
// selected and interprates it and makes the necessary changes to the HTML to
// apply this style.
function addTag(obj) {

   if (!RichEditor.txtView) return;      // Disabled in View Source mode

   // Determine the type of element we are dealing with.
   // TYPE 0 IS NORMAL-TAG, 1 IS CLASS, 2 IS SUBCLASS, 3 = Format Block command
   var value = obj[obj.selectedIndex].value;
   if (!value) {                        // Format Block
      sel(obj);
      return;
   }

   var type = 0;                        // TAG

   if (value.indexOf(".") == 0) {            // .className
      type = 1;
   } else if (value.indexOf(".") != -1) {      // TAG.className
      type = 2;
   }

   doc.focus();

   // Pick up the highlighted text
   var r = document.selection.createRange();
   r.select();
   var s = r.htmlText;

   // If we have some selected text, then ignore silly selections
   if (s == " " || s == "&nbsp;") {
      return;
   }

   // How we apply formatting is based upon the type of formitting being
   // done.
   switch(type)
   {
   case 1:
      // class: Wrap the selected text with a span of the specified
      // class name
      value = value.substring(1,value.length);
      r.pasteHTML("<span class="+value+">" + r.htmlText + "</span>")
      break;

   case 2:
      // subclass: split the value into tag + class
      value = value.split(".");
      r.pasteHTML('<' + value[0] + ' class="' + value[1] +'">'
               + r.htmlText
               + '</' + value[0] + '>'
            );
      break;

   default:
      // TAG: wrap up the highlighted text with the specified tag
      r.pasteHTML("<"+value+">"+r.htmlText+"</"+value+">")
      break;
   }
}

// initStyleDropdown(): This takes the passed styleList and generates the style
// dropdown list box from it.
function initStyleDropdown(styleList) {

   // Build the option list for the styles dropdown from the passed styles
   for (var i = 0; i < styleList.length; i++) {
      var oOption = document.createElement("OPTION");
      if (styleList[i][0]) oOption.value = styleList[i][0];
      oOption.text = styleList[i][1];
      oOption.style.backgroundColor = 'white';
      document.all.ctlStyle.add(oOption);
   }
}

// applyOptions(): This takes the passed options string and actions them.
// Called during the init process.
function applyOptions(str)
{
   var options = str.split(";");
   for (var i = 0; i < options.length; i++) {
      var eq = options[i].indexOf('=');
      var on = eq == -1 ? true : "yes;true;1".indexOf(options[i].substr(eq+1).toLowerCase()) != -1;
      var name = eq == -1 ? options[i] : options[i].substr(0,eq);
      var el = document.all("feature" + name);
      if (el) {
         el.runtimeStyle.display = (on ? 'inline' : 'none'); 
      } else {
         if (!RichEditor.aOptions) RichEditor.aOptions = new Array;
         RichEditor.aOptions[name] = on;
      }
   }
}

// getOption(): Get the value for a previously set option or return undefined if
// the option is not set.
function getOption(name)
{
   if (RichEditor.aOptions) return RichEditor.aOptions[name];
   return;   // Undefined
} 

// Handle drag and drop events into the editor window.  Until we
// work out how to handle these better (which requires co-operation
// from the code being dragged from as far as I can tell) we simply
// disable the functionality.
function handleDrag(n)
{
   // if drag and drop is disabled, then cancel the dragdrop
   // events
   if (!getOption("dragdrop"))
   {
      switch(n) {
      case 0:   // ondragenter
         window.event.dataTransfer.dropEffect = "none";
         break;
      }
      // Cancel the event
      window.event.returnValue = false;
   }
}

