<!--
################################################################################
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
##  Austin David France
##  Ramesys (Contracting Services) Limited
##  Mentor House
##  Ainsworth Street
##  Blackburn
##  Lancashire
##  BB1 6AY
##  United Kingdom
##  email: Austin.France@Ramesys.com
##
## Home Page:    http://richtext.sourceforge.net/
## Support:      http://richtext.sourceforge.net/
##
################################################################################
-->
<?php
    require("../../../../config.php");

    $id = optional_param('id', SITEID, PARAM_INT);

    require_course_login($id);
    @header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<style type="text/css">
<!--
body { background: ButtonFace; color: ButtonText; font: 11px Tahoma,Verdana,sans-serif;
margin: 0px; padding: 0px; }
form p { margin-top: 5px; margin-bottom: 5px; }
select, input, button { font: 11px Tahoma,Verdana,sans-serif; }
button { width: 70px; }
.space { padding: 2px; }
.title { background: #ddf; color: #000; font-weight: bold; font-size: 14px; padding: 3px 10px; margin-bottom: 10px;
border-bottom: 1px solid black; letter-spacing: 2px; }
form { padding: 0px; margin: 0px; }
.chr { background-color: transparent; border: 1px solid #dcdcdc; font-family: "Times New Roman", times;
font-size: small; }
// -->
</style>
<script type="text/javascript" src="popup.js"></script>
<script type="text/javascript">
//<![CDATA[
function Init() {
  __dlg_init();
}
var chars = ["!","&quot;","#","$","%","&","'","(",")","*","+","-",".","/","0","1","2","3","4","5","6","7","8","9",":",";","&lt;","=","&gt;","?","@","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","[","]","^","_","`","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","{","|","}","~","&euro;","ƒ","„","…","†","‡","ˆ","\‰","Š","‹","Œ","&lsquo;","&rsquo;","&rsquo;","&ldquo;","&rdquo;","•","&ndash;","&mdash;","˜","™","š","›","œ","Ÿ","&iexcl;","&cent;","&pound;","&pound;","&curren;","&yen;","&brvbar;","&sect;","&uml;","&copy;","&ordf;","&laquo;","&not;","­","&reg;","&macr;","&deg;","&plusmn;","&sup2;","&sup3;","&acute;","&micro;","&para;","&middot;","&cedil;","&sup1;","&ordm;","&raquo;","&frac14;","&frac12;","&frac34;","&iquest;","&Agrave;","&Aacute;","&Acirc;","&Atilde;","&Auml;","&Aring;","&AElig;","&Ccedil;","&Egrave;","&Eacute;","&Ecirc;","&Euml;","&Igrave;","&Iacute;","&Icirc;","&Iuml;","&ETH;","&Ntilde;","&Ograve;","&Oacute;","&Ocirc;","&Otilde;","&Ouml;","&times;","&Oslash;","&Ugrave;","&Uacute;","&Ucirc;","&Uuml;","&Yacute;","&THORN;","&szlig;","&agrave;","&aacute;","&acirc;","&atilde;","&auml;","&aring;","&aelig;","&ccedil;","&egrave;","&eacute;","&ecirc;","&euml;","&igrave;","&iacute;","&icirc;","&iuml;","&eth;","&ntilde;","&ograve;","&oacute;","&ocirc;","&otilde;","&ouml;","&divide;","&oslash;","&ugrave;","&uacute;","&ucirc;","&uuml;","&uuml;","&yacute;","&thorn;","&yuml;"]

function tab(w,h) {
    var strtab = ["<table border='0' cellspacing='0' cellpadding='0' align='center' bordercolor='#dcdcdc' bgcolor='#C0C0C0'>"]
    var k = 0;
    for(var i = 0; i < w; i++) {
        strtab[strtab.length] = "<tr>";
        for(var j = 0; j < h; j++) {
            strtab[strtab.length] = "<td class='chr' width='14' align='center' onClick='getchar(this)' onMouseOver='hover(this,true)' onMouseOut='hover(this,false)'>"+(chars[k]||'')+"</td>";
            k++;
        }
        strtab[strtab.length]="</tr>";
    }
    strtab[strtab.length] = "</table>";
    return strtab.join("\n");
}

function hover(obj,val) {
    if (!obj.innerHTML) {
        obj.style.cursor = "default";
        return;
    }
    obj.style.border = val ? "1px solid black" : "1px solid #dcdcdc";
    //obj.style.backgroundColor = val ? "black" : "#C0C0C0"
    //obj.style.color = val ? "white" : "black";
}
function getchar(obj) {
    if(!obj.innerHTML) return;
    var sChar = obj.innerHTML || "";
    __dlg_close(sChar);
    return false;
}
function cancel() {
    __dlg_close(null);
    return false;
}
//]]>
</script>
<title><?php print_string("choosechar","editor");?></title>
</head>
<body onload="Init()">
<table class="dlg" cellpadding="0" cellspacing="2">
<tr><td><table width="100%"><tr><td class="title" nowrap><?php print_string("choosechar","editor") ?></td></tr></table></td></tr>
<tr>
<td>
    <table border="0" align="center" cellpadding="5">
      <tr valign="top">
        <td>

       <script type="text/javascript">
       //<![CDATA[
       document.write(tab(7,32))
       //]]>
       </script>

        </td>
      </tr>
    </table>
    </td>
  </tr>
<tr><td><table width="100%"><tr><td valign="middle" width="90%"><hr width="100%"></td></tr></table></td></tr>
<tr><td align="right">
    <button type="button" onclick="cancel()"><?php print_string("close","editor") ?></button></td></tr>
</table>
</body>
</body>
</html>
