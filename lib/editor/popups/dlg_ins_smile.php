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
<?php 
	include("../../../config.php"); 
	$pix = $CFG->wwwroot . "/pix/s";	
?>
<html style="width: 320px; height: 350px;">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name=vs_targetSchema content="HTML 4.0">
<meta name="GENERATOR" content="Microsoft Visual Studio 7.0">
<LINK rel="stylesheet" type="text/css" href="dialog.css">
<title>Insert Smiley Icon &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</title>
<script type="text/javascript" src="popup.js"></script>

<script language="JavaScript" type="text/javascript">
function Init() {
  __dlg_init();
}
function attr(name, value) {
	if (!value || value == "") return "";
	return ' ' + name + '="' + value + '"';
}
function insert(img,text) {
	if (img) {
			var src = img;
			var alt = text;
	}
  // pass data back to the calling window
  var fields = ["f_url","f_alt"];
  var param = new Object();
  param['f_url'] = src;
  param['f_alt'] = alt;
 
  __dlg_close(param);
  return false;
};

function cancel() {
  __dlg_close(null);
  return false;
};
</script>
</head>
<body onload="Init()">
<table class="dlg" cellpadding="0" cellspacing="2" width="100%" height="100%">
<tr><td><table width="100%"><tr><td class="title" nowrap><?php print_string("chooseicon","editor") ?></td></tr></table></td></tr>
<tr>
<td>
    <table border="0" align="center" cellpadding="5">
      <tr valign="top">
        <td>
        <table border="0" align="center">
          <tr>
            <td><img alt border="0" hspace="10" src="<?php echo $pix ?>/smiley.gif" onclick="insert('<?php echo $pix ?>/smiley.gif',':-)')" width="15" height="15"></td>
            <td>smile</td>
            <td class="smile">:-)</td>
          </tr>
          <tr>
            <td><img alt border="0" hspace="10" src="<?php echo $pix ?>/biggrin.gif" onclick="insert('<?php echo $pix ?>/biggrin.gif',':-D')" width="15" height="15"></td>
            <td>big grin</td>
            <td class="smile">:-D</td>
          </tr>
          <tr>
            <td><img alt border="0" hspace="10" src="<?php echo $pix ?>/wink.gif" onclick="insert('<?php echo $pix ?>/wink.gif',';-)')" width="15" height="15"></td>
            <td>wink</td>
            <td class="smile">;-)</td>
          </tr>
          <tr>
            <td><img alt= border="0" hspace="10" src="<?php echo $pix ?>/mixed.gif" onclick="insert('<?php echo $pix ?>/mixed.gif',':-/')" width="15" height="15"></td>
            <td>mixed</td>
            <td class="smile">:-/</td>
          </tr>
            <tr>
              <td><img alt border="0" hspace="10" src="<?php echo $pix ?>/thoughtful.gif" onclick="insert('<?php echo $pix ?>/thoughtful.gif','V-.')" width="15" height="15"></td>
              <td>thoughtful</td>
              <td class="smile">V-.</td>
            </tr>
          <tr>
            <td><img alt border="0" hspace="10" src="<?php echo $pix ?>/tongueout.gif" onclick="insert('<?php echo $pix ?>/tongueout.gif',':-P')" width="15" height="15"></td>
            <td>tongue out</td>
            <td class="smile">:-P</td>
         </tr>
          <tr>
            <td><img alt border="0" hspace="10" src="<?php echo $pix ?>/cool.gif" onclick="insert('<?php echo $pix ?>/cool.gif','B-)')" width="15" height="15"></td>
            <td>cool</td>
            <td class="smile">B-)</td>
          </tr>
            <tr>
              <td><img alt border="0" hspace="10" src="<?php echo $pix ?>/approve.gif" onclick="insert('<?php echo $pix ?>/approve.gif','^-)')" width="15" height="15"></td>
              <td>approve</td>
              <td class="smile">^-)</td>
            </tr>
            <tr>
              <td><img alt border="0" hspace="10" src="<?php echo $pix ?>/wideeyes.gif" onclick="insert('<?php echo $pix ?>/wideeyes.gif','8-)')" width="15" height="15"></td>
              <td>wide eyes</td>
              <td class="smile">8-)</td>
            </tr>
            <tr>
              <td><img alt border="0" hspace="10" src="<?php echo $pix ?>/surprise.gif" onclick="insert('<?php echo $pix ?>/surprise.gif','8-o')" width="15" height="15"></td>
              <td>surprise</td>
              <td class="smile">8-o</td>
            </tr>
        </table>
        </td>
        <td>
        <table border="0" align="center">
            <tr>
              <td><img alt border="0" hspace="10" src="<?php echo $pix ?>/sad.gif" onclick="insert('<?php echo $pix ?>/sad.gif',':-(')" width="15" height="15"></td>
              <td>sad</td>
              <td class="smile">:-(</td>
            </tr>
            <tr>
              <td><img alt border="0" hspace="10" src="<?php echo $pix ?>/shy.gif" onclick="insert('<?php echo $pix ?>/shy.gif','8-.')" width="15" height="15"></td>
              <td>shy</td>
              <td class="smile">8-.</td>
            </tr>
          <tr>
            <td><img alt border="0" hspace="10" src="<?php echo $pix ?>/blush.gif" onclick="insert('<?php echo $pix ?>/blush.gif',':-I')" width="15" height="15"></td>
            <td>blush</td>
            <td class="smile">:-I</td>
          </tr>
            <tr>
              <td><img alt border="0" hspace="10" src="<?php echo $pix ?>/kiss.gif" onclick="insert('<?php echo $pix ?>/kiss.gif',':-X')" width="15" height="15"></td>
              <td>kisses</td>
              <td class="smile">:-X</td>
            </tr>
          <tr>
            <td><img alt border="0" hspace="10" src="<?php echo $pix ?>/clown.gif" onclick="insert('<?php echo $pix ?>/clown.gif',':o)')" width="15" height="15"></td>
            <td>clown</td>
            <td class="smile">:o)</td>
          </tr>
          <tr>
            <td><img alt border="0" hspace="10" src="<?php echo $pix ?>/blackeye.gif" onclick="insert('<?php echo $pix ?>/blackeye.gif','P-|')" width="15" height="15"></td>
            <td>black eye</td>
            <td class="smile">P-|</td>
          </tr>
            <tr>
              <td><img alt border="0" hspace="10" src="<?php echo $pix ?>/angry.gif" onclick="insert('<?php echo $pix ?>/angry.gif','8-[')" width="15" height="15"></td>
              <td>angry</td>
              <td class="smile">8-[</td>
            </tr>
            <tr>
              <td><img alt border="0" hspace="10" src="<?php echo $pix ?>/dead.gif" onclick="insert('<?php echo $pix ?>/dead.gif','xx-P')" width="15" height="15"></td>
              <td>dead</td>
              <td class="smile">xx-P</td>
            </tr>
            <tr>
              <td><img alt border="0" hspace="10" src="<?php echo $pix ?>/sleepy.gif" onclick="insert('<?php echo $pix ?>/sleepy.gif','|-.')" width="15" height="15"></td>
              <td>sleepy</td>
              <td class="smile">|-.</td>
            </tr>
          <tr>
            <td><img alt border="0" hspace="10" src="<?php echo $pix ?>/evil.gif" onclick="insert('<?php echo $pix ?>/evil.gif','}-]')" width="15" height="15"></td>
            <td>evil</td>
            <td class="smile">}-]</td>
          </tr>
        </table>
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
</html>