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
 * DragMath equation editor popup.
 *
 * @package   tinymce_dragmath
 * @copyright 2008 Mauno Korpelainen
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_MOODLE_COOKIES', true);

require('../../../../../config.php');

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/lib/editor/tinymce/plugins/dragmath/dragmath.php');

if (isset($SESSION->lang)) {
    // Language is set via page url param.
    $lang = $SESSION->lang;
} else {
    $lang = 'en';
}

// Find DragMath language.
$langmapping = array('cs'=>'cz', 'pt_br'=>'pt-br');

// Fix non-standard lang names.
if (array_key_exists($lang, $langmapping)) {
    $lang = $langmapping[$lang];
}

if (!file_exists("$CFG->dirroot/lib/dragmath/applet/lang/$lang.xml")) {
    $lang = 'en';
}

$editor = get_texteditor('tinymce');
$plugin = $editor->get_plugin('dragmath');

// Prevent https security problems.
$relroot = preg_replace('|^http.?://[^/]+|', '', $CFG->wwwroot);

$htmllang = get_html_lang();
header('Content-Type: text/html; charset=utf-8');
header('X-UA-Compatible: IE=edge');
?>
<!DOCTYPE html>
<html <?php echo $htmllang ?>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?php print_string('title', 'tinymce_dragmath')?></title>
<script type="text/javascript" src="<?php echo $editor->get_tinymce_base_url(); ?>tiny_mce_popup.js"></script>
<script type="text/javascript" src="<?php echo $plugin->get_tinymce_file_url('js/dragmath.js'); ?>"></script>
</head>
<body>

<object type="application/x-java-applet" id="dragmath" width="520" height="300">
    <param name="java_codebase" value="<?php echo $relroot.'/lib/dragmath/applet/' ?>" />
    <param name="java_code" value="Display/MainApplet.class" />
    <param name="java_archive" value="DragMath.jar,lib/AbsoluteLayout.jar,lib/swing-layout-1.0.jar,lib/jdom.jar,lib/jep.jar" />
    <param name="language" value="<?php echo $lang; ?>" />
    <param name="outputFormat" value="MoodleTex" />
    <?php print_string('javaneeded', 'tinymce_dragmath', '<a href="http://www.java.com">Java.com</a>')?>
</object>
<form name="form" action="#">
    <div class="mceActionPanel">
        <input type="submit" id="insert" name="insert" value="{#insert}" onclick="return DragMathDialog.insert();" />
        <input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="return tinyMCEPopup.close();" />
    </div>
</form>

</body>
</html>
