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
// $Id: insert_cloze.php,v 1.4 2013/18/03


/**
 * Dialog for cloze editor for tinymce editor.
 *
 * @package    tinymce_colzeeditor
 * @copyright  2013 Andreas Glombitza/Achim Skuta
 * @copyright  2018 onward Germán Valero <gvalero@unam.mx>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_MOODLE_COOKIES', true); // Session not used here.
require(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . '/config.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/lib/editor/tinymce/plugins/clozeeditor/dialog.php');
$stringmanager = get_string_manager();
$editor = get_texteditor('tinymce');
$plugin = $editor->get_plugin('clozeeditor');
$htmllang = get_html_lang();
header('Content-Type: text/html; charset=utf-8');
header('X-UA-Compatible: IE=edge');
?>
<!DOCTYPE html>
<html <?php echo $htmllang ?>
<head>
    <title><?php print_string('clozeeditor:desc', 'tinymce_clozeeditor'); ?></title>
    <script type="text/javascript" src="<?php echo $editor->get_tinymce_base_url(); ?>/tiny_mce_popup.js"></script>
    <script type="text/javascript" src="<?php echo $plugin->get_tinymce_file_url('js/dialog1.js'); ?>"></script>

<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="<?php echo $plugin->get_tinymce_file_url('js/encode.js'); ?>"></script>
  <script type="text/javascript" src="<?php echo $plugin->get_tinymce_file_url('js/parse.js'); ?>"></script>
  <script type="text/javascript" src="<?php echo $plugin->get_tinymce_file_url('js/parseHelper.js'); ?>"></script>
  <script type="text/javascript" src="<?php echo $plugin->get_tinymce_file_url('js/parseAnswer.js'); ?>"></script>
  <script type="text/javascript" src="<?php echo $plugin->get_tinymce_file_url('js/parseFeedback.js'); ?>"></script>
  <script type="text/javascript" src="<?php echo $plugin->get_tinymce_file_url('js/parsePercentage.js'); ?>"></script>
  <script type="text/javascript" src="<?php echo $plugin->get_tinymce_file_url('js/parseThrottle.js'); ?>"></script>
  <script type="text/javascript" src="<?php echo $plugin->get_tinymce_file_url('js/popup.js'); ?>"></script>

  <link rel="stylesheet" type="text/css" href="dialog.css">
</head>
<body onload="Init(); ">

<form name="Formular">
  <fieldset >
    <legend class="title">{#clozeeditor.titleclozeeditor}</legend>
    <label for="quiz_type">{#clozeeditor.chooseclozeformat}</label><br />
    <select name="quizType" onchange="toggleThrottle(); " >
            <option value="SHORTANSWER"><?php echo get_string('shortanswer', 'quiz'); ?></option>
            <option value="SHORTANSWER_C"><?php echo get_string('shortanswer', 'quiz')." (".get_string('casesensitive', 'quiz').")"; ?></option>
            <option value="MULTICHOICE" ><?php echo get_string('layoutselectinline', 'qtype_multianswer'); ?></option>
            <option value="MULTICHOICE_V"><?php echo get_string('layoutvertical', 'qtype_multianswer'); ?></option>
            <option value="MULTICHOICE_H"><?php echo get_string('layouthorizontal', 'qtype_multianswer'); ?></option>
            <option value="NUMERICAL"><?php echo get_string('numerical', 'quiz'); ?></option>
  </select>
  <br />
<label for="weighting"><?php echo get_string('defaultgrade', 'quiz'); ?></label>
  <input size=4 type="text" name="weighting" style="margin-top: 8px; margin-bottom: 4px; " />
  <br />
  
  <table id="main_table">
    <tbody>
      <tr>
        <td class="table_value"></td>
        <td class="table_value"><?php echo get_string('answer', 'moodle'); ?></td>
        <td class="table_value_throttle"><?php echo get_string('tolerance', 'qtype_calculated'); ?></td>
        <td class="table_value"><?php echo get_string('correct', 'quiz'); ?></td>
        <td class="table_value"><?php echo get_string('percentcorrect', 'quiz'); ?></td>
        <td class="table_value"><?php echo get_string('feedback', 'qtype_multichoice'); ?></td>
      </tr>
    </tbody>
  </table>  
  
  <input type="button" name="addline"    value="<?php echo get_string('addfields', 'form', 1); ?>" onclick="addRow('main_table');" style="margin-top: 5px; " />
 <!-- <input type="button" name="formaction" value="encode" onclick="encode()" style="margin-left: 8px; margin-top: 5px;  " />  -->
  <br />
 <input type="text" name="output" style="display: none; width: 456px; margin-top: 8px; " />
      
</form>

<form onsubmit="clozeeditorDialog.insert();return false;" action="#">
        <div class="mceActionPanel">
                <input type="button" id="insert" name="insert" value="{#insert}" onclick="clozeeditorDialog.insert();" />
                <input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
        </div>
</form>

</body>
</html>
