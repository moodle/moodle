<%@language="JavaScript"%>
<!--
#################################################################################
##
## HTML Text Editing Component for hosting in Web Pages
## Copyright (C) 2001  Ramesys (Contracting Services) Limited
##
## This library is free software; you can redistribute it and/or
## modify it under the terms of the GNU Lesser General Public
## License as published by the Free Software Foundation; either
## version 2.1 of the License, or (at your option) any later version.
##
## This library is distributed in the hope that it will be useful,
## but WITHOUT ANY WARRANTY; without even the implied warranty of
## MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
## Lesser General Public License for more details.
##
## You should have received a copy of the GNU LesserGeneral Public License
## along with this program; if not a copy can be obtained from
##
##    http://www.gnu.org/copyleft/lesser.html
##
## or by writing to:
##
##    Free Software Foundation, Inc.
##    59 Temple Place - Suite 330,
##    Boston,
##    MA  02111-1307,
##    USA.
##
## Original Developer:
##
##	Austin David France
##	Ramesys (Contracting Services) Limited
##	Mentor House
##	Ainsworth Street
##	Blackburn
##	Lancashire
##	BB1 6AY
##	United Kingdom
##  email: Austin.France@Ramesys.com
##
## Home Page:    http://richtext.sourceforge.net/
## Support:      http://richtext.sourceforge.net/
##
#################################################################################
-->
<%
var strHTML = Request.Form("text").Item();
if (strHTML)
{
    // Update your database here
    // ...

    // Confirmation
    Response.Write("<P>Database Updated</P>");
    Response.Write("<P>HTML:-<hr>" + Server.HTMLEncode(strHTML) + "</P>");
    Response.End();
}

// Get your HTML from the datbase here
strHTML = '<H1>Heading 1</H1>'
		+ '<H2>Heading 2</H2>'
		+ '<H3>Heading 3</H3>'
		+ '<P>Normal</P>'
		+ '<P>Welcome to the richtext text editor, the HTML text editor which works inside a web page.</P>'
		+ '<P>This is <a href="http://www.bakedbeanandtomatosoup.co.uk/">Link</a>'
	;
%>
<HTML>
<HEAD>
<TITLE>Edit Text</TITLE>
<META content="HTML 4.0" name=vs_targetSchema />
<META content="Microsoft FrontPage 4.0" name=GENERATOR />
</HEAD>
<BODY leftMargin=0 topMargin=0 scroll="no" style="border:0">

<object id="richedit" style="BACKGROUND-COLOR: buttonface" data="richedit.html"
width="100%" height="75%" type="text/x-scriptlet" VIEWASTEXT>
	</object>
<div id="debug">
</div>

<form id="theForm" method="post">
<textarea name="text" style="display:none" rows="1" cols="20"><%=strHTML%></textarea>
</form>

<SCRIPT language="JavaScript" event="onload" for="window">
	var win = window.open('about:debugWindow',
						'_blank',
						'width=1,height=1,top=-100,left=-100,resizable=yes,scrollbars=yes,status=no,menubar=no,toolbar=no,location=no'
						);
	win.document.write('<html>'
						+ '<head>'
						+ '<title>Editor Debug Window</title>'
						+ '<style type="text/css">'
						+ 'div#debug {'
							+ 'border: 1px inset activeborder;'
							+ 'overflow: auto;'
							+ 'font-family: "Lucida Sans Unicode", "Verdana", "Arial";'
							+ 'height: 100%;'
						+ '}'
						+ 'div#debug td {'
							+ 'font-size: 8pt;'
							+ 'vertical-align: top;'
						+ '}'
						+ 'div#debug th {'
							+ 'font-size: 8pt;'
							+ 'text-align: left;'
							+ 'color: white;'
							+ 'background-color: activecaption;'
						+ '}'
						+ '</style>'
						+ '</head>'
						+ '<body topmargin="0" leftmargin="0">'
						+ '<table width="100%" height="100%">'
						+ '<tr><td valign="top" width="100%" height="100%"><div id="debug">'
						+ '</div></td></tr>'
						+ '<tr valign="bottom"><td align="right">'
							+ '<input type="button" value="close" onclick="window.close()" />'
							+ '</td></tr>'
						+ '</table>'
						+ '</body>'
						+ '<s'+'cript>'
						+ 'var w = window.screen.availWidth/4, h = window.screen.availHeight/2;'
						+ 'window.resizeTo(w,h);'
						+ 'window.moveTo((window.screen.availWidth-w)/8,(window.screen.availHeight-h)/4);'
						+ '</sc'+'ript>'
						+ '</html>'
					);
	richedit.debugWindow = win.document.all("debug");
	richedit.options = "history=no;source=yes";
	richedit.addField("to", "To", 128, "someone@somewhere.com");
	richedit.addField("cc", "Cc", 128, "someone@else.com");
	richedit.addField("subject", "Subject", 128, "Something about Nothing");
	richedit.docHtml = theForm.text.innerText;
</SCRIPT>

<SCRIPT language="JavaScript" event="onscriptletevent(name, eventData)" for="richedit">
    if (name == "post") {
    	richedit.getValue("subject");
        theForm.text.value = eventData;
        theForm.submit();
    }
</SCRIPT>

</BODY>
</HTML>
