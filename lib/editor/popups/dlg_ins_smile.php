<!--
#################################################################################
##
## $Id$
##
#################################################################################
-->
<?php 
    include('../../../config.php'); 
    $pixpath = "$CFG->pixpath/s";

    $fullnames = get_list_of_pixnames();

    $emoticons = array ( 'smiley'     => ':-)',
                         'biggrin'    => ':-D',
                         'wink'       => ';-)',
                         'mixed'      => ':-/',
                         'thoughtful' => 'V-.',
                         'tongueout'  => ':-P',
                         'cool'       => 'B-)',
                         'approve'    => '^-)',
                         'wideeyes'   => '8-)',
                         'clown'      => ':o)',
                         'sad'        => ':-(',
                         'shy'        => '8-.',
                         'blush'      => ':-I',
                         'kiss'       => ':-X',
                         'surprise'   => '8-o',
                         'blackeye'   => 'P-|',
                         'angry'      => '8-[',
                         'dead'       => 'xx-P',
                         'sleepy'     => '|-.',
                         'evil'       => '}-]' );
    
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=<?php print_string("thischarset");?>" />
<meta name=vs_targetSchema content="HTML 4.0">
<meta name="GENERATOR" content="Microsoft Visual Studio 7.0">
<LINK rel="stylesheet" type="text/css" href="dialog.css">
<title><?php print_string('insertsmile', 'editor') ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</title>
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
            var strImage = img;
            var strAlt = text;
            var imgString = "<img src=\"" + strImage +"\" alt=\"" + strAlt +"\" />";
    }
  // pass data back to the calling window
  __dlg_close(imgString);
  return false;
};

function cancel() {
  __dlg_close(null);
  return false;
};
</script>
<style type="text/css">
body {
  width: 330;
  height: 360;
}
</style>
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
<?php 
        $list = array('smiley', 'biggrin', 'wink', 'mixed', 'thoughtful', 
                      'tongueout', 'cool', 'approve', 'wideeyes', 'surprise');
        foreach ($list as $image) {
            $name = $fullnames[$image];
            $icon = $emoticons[$image];
            echo '<tr>';
            echo "<td><img alt=\"$name\" border=\"0\" hspace=\"10\" src=\"$pixpath/$image.gif\" ".
                 " onclick=\"insert('$pixpath/$image.gif','$name')\" width=\"15\" height=\"15\"></td>";
            echo "<td>$name</td>";
            echo "<td class=\"smile\">$icon</td>";
            echo "</tr>";
        }
?>
        </table>
        </td>
        <td>
        <table border="0" align="center">

<?php 
        $list = array('sad', 'shy', 'blush', 'kiss', 'clown', 'blackeye',
                      'angry', 'dead', 'sleepy', 'evil');
        foreach ($list as $image) {
            $name = $fullnames[$image];
            $icon = $emoticons[$image];
            echo '<tr>';
            echo "<td><img alt=\"$name\" border=\"0\" hspace=\"10\" src=\"$pixpath/$image.gif\" ".
                 " onclick=\"insert('$pixpath/$image.gif','$name')\" width=\"15\" height=\"15\"></td>";
            echo "<td>$name</td>";
            echo "<td class=\"smile\">$icon</td>";
            echo "</tr>";
        }
?>
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
