<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Displays the TinyMCE popup window to insert a Moodle emoticon
 *
 * @package    tinymceplugin
 * @subpackage moodleemoticon
 * @copyright  2010 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_MOODLE_COOKIES', true); // Session not used here
define('NO_UPGRADE_CHECK', true);  // Ignore upgrade check

require_once(dirname(dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))))) . '/config.php');

$PAGE->set_context(get_system_context());

$emoticonmanager = get_emoticon_manager();
$stringmanager = get_string_manager();

$editor = get_texteditor('tinymce');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title><?php print_string('moodleemoticon:desc', 'editor_tinymce'); ?></title>
    <script type="text/javascript" src="../../tiny_mce_popup.js?v=<?php echo $editor->version ?>"></script>
    <script type="text/javascript" src="js/dialog.js?v=<?php echo $editor->version ?>"></script>
</head>
<body>

    <table border="0" align="center" style="width:100%;">
<?php

$emoticons = $emoticonmanager->get_emoticons();
// this is tricky - we must somehow include the information about the original
// emoticon text so that we can replace the image back with it on editor save.
// so we are going to encode the index of the emoticon. this will break when the
// admin changes the mapping table while the user has the editor opened
// but I am not able to come with better solution at the moment :-/
$index = 0;
foreach ($emoticons as $emoticon) {
    $txt = $emoticon->text;
    $img = $OUTPUT->render(
        $emoticonmanager->prepare_renderable_emoticon($emoticon, array('class' => 'emoticon emoticon-index-'.$index)));
    if ($stringmanager->string_exists($emoticon->altidentifier, $emoticon->altcomponent)) {
        $alt = get_string($emoticon->altidentifier, $emoticon->altcomponent);
    } else {
        $alt = '';
    }
    echo html_writer::tag('tr',
            html_writer::tag('td', $img, array('style' => 'width:20%;text-align:center;')) .
            html_writer::tag('td', s($txt), array('style' => 'width:40%;text-align:center;font-family:monospace;')) .
            html_writer::tag('td', $alt),
        array(
            'class' => 'emoticoninfo emoticoninfo-index-'.$index,
        )
    );
    $index++;
}

?>
    </table>

    <div class="mceActionPanel">
        <input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
    </div>

</body>
</html>
