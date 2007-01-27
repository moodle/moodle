<?php

#################################################################################
##
## $Id$
##
#################################################################################

    require("../../../../config.php");

    $id = optional_param('id', SITEID, PARAM_INT);

    require_course_login($id);
    @header('Content-Type: text/html; charset=utf-8');

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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php print_string('insertsmile', 'editor'); ?></title>
<link rel="stylesheet" href="dialog.css" type="text/css" />
<script type="text/javascript" src="popup.js"></script>
<script type="text/javascript">
//<![CDATA[
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
            var imgString = "<img src=\"" + strImage +"\" alt=\"" + strAlt +"\" title=\"" + strAlt +"\" />";
    }
  // pass data back to the calling window
  __dlg_close(imgString);
  return false;
};

function cancel() {
  __dlg_close(null);
  return false;
};
//]]>
</script>
</head>
<body onload="Init()">
<table class="dlg" cellpadding="0" cellspacing="2" width="100%">
<tr><td><table width="100%"><tr><td class="title" nowrap="nowrap"><?php print_string('chooseicon', 'editor'); ?></td></tr></table></td></tr>
<tr>
<td>
    <table border="0" align="center" cellpadding="5">
      <tr valign="top">
        <td>
        <table border="0">
<?php
        $list = array('smiley', 'biggrin', 'wink', 'mixed', 'thoughtful',
                      'tongueout', 'cool', 'approve', 'wideeyes', 'surprise');
        foreach ($list as $image) {
            $name = $fullnames[$image];
            $icon = $emoticons[$image];
            echo '<tr>';
            echo "<td><img alt=\"$name\" class=\"icon\" src=\"$pixpath/$image.gif\" ".
                 " onclick=\"insert('$pixpath/$image.gif','$name')\" /></td>";
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
            echo "<td><img alt=\"$name\" class=\"icon\" src=\"$pixpath/$image.gif\" ".
                 " onclick=\"insert('$pixpath/$image.gif','$name')\" /></td>";
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
<tr><td><table width="100%"><tr><td valign="middle" width="90%"><hr width="100%" /></td></tr></table></td></tr>
<tr><td align="right">
    <button type="button" onclick="return cancel();"><?php print_string('close', 'editor'); ?></button></td></tr>
</table>
</body>
</html>
