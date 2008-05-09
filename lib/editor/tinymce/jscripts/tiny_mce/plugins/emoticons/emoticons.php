<?php

    require("../../../../../../../config.php");

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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{#emoticons_dlg.title}</title>
	<script type="text/javascript" src="../../tiny_mce_popup.js?v=307"></script>
	<script type="text/javascript" src="js/emoticons.js?v=307"></script>
	<base target="_self" />
</head>
<body style="display: none">
	<div align="center">
<table class="dlg" cellpadding="0" cellspacing="2" width="100%">
<tr><td><table width="100%"><tr><td class="title" nowrap="nowrap"><?php print_string('chooseicon', 'editor'); ?></td></tr></table></td></tr>
<tr>
<td>
    <table border="0" align="center" cellpadding="5">
      <tr valign="top">
        <td>
        <table border="0">
<?php
        $list = array('smiley', 'biggrin', 'wink', 'mixed', 'thoughtful', 'tongueout', 'cool', 'approve', 'wideeyes', 'surprise');
        foreach ($list as $image) {
            $name = $fullnames[$image];
            $icon = $emoticons[$image];
            echo '<tr>';
echo "<td><a href=\"javascript:emoticonsDialog.insert('$pixpath/$image.gif','$name');\"><img alt=\"$name\" class=\"icon\" src=\"$pixpath/$image.gif\" width=\"15\" height=\"15\" border=\"0\" title=\"$name\" /></a></td>";
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
        $list = array('sad', 'shy', 'blush', 'kiss', 'clown', 'blackeye', 'angry', 'dead', 'sleepy', 'evil');
        foreach ($list as $image) {
            $name = $fullnames[$image];
            $icon = $emoticons[$image];
            echo '<tr>';
echo "<td><a href=\"javascript:emoticonsDialog.insert('$pixpath/$image.gif','$name');\"><img alt=\"$name\" class=\"icon\" src=\"$pixpath/$image.gif\" width=\"15\" height=\"15\" border=\"0\" title=\"$name\" /></a></td>";
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
</table>
	</div>
</body>
</html>