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
 * On-the-fly conversion of Moodle lang strings to TinyMCE expected JS format.
 *
 * @package    moodlecore
 * @subpackage editor
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_MOODLE_COOKIES', true);
define('NO_UPGRADE_CHECK', true);

require_once('../../../../config.php');

$lang     = optional_param('elanguage', 'en_utf8', PARAM_SAFEDIR);
$theme    = optional_param('etheme', 'standard', PARAM_SAFEDIR);
$usehttps = optional_param('eusehttps', 0, PARAM_BOOL);

if (file_exists("$CFG->dataroot/lang/$lang") or file_exists("$CFG->dirroot/lang/$lang")) {
    $SESSION->lang = $lang;
} else if (file_exists("$CFG->dataroot/lang/{$lang}_utf8") or file_exists("$CFG->dirroot/lang/{$lang}_utf8")) {
    $SESSION->lang = $lang.'_utf8';
}

$xmlruleset = file_get_contents('xhtml_ruleset.txt');

$directionality = get_string('thisdirection');

$strtime = get_string('strftimetime');
$strdate = get_string('strftimedaydate');

$lang = str_replace('_utf8', '', $lang); // use more standard language codes

if ($usehttps) {
    $wwwbase = str_replace('http:', 'https:', $CFG->wwwroot);
} else {
    $wwwbase = $CFG->wwwroot;
}

// $contentcss should be customizable
$contentcss = "$CFG->themewww/$theme/styles.php";

//TODO: reimplement spellchecker support - the TinyMCE one is hardcoded for linux, has encoding problems, etc.

$output = <<<EOF
function mc_init_editors() {
    tinyMCE.init({
        mode: "textareas",
        relative_urls: false,
        editor_selector: "form-tinymce-legacy",
        document_base_url: "$wwwbase",
        content_css: "$contentcss",
        language: "$lang",
        directionality: "$directionality",
        plugin_insertdate_dateFormat : "$strdate",
        plugin_insertdate_timeFormat : "$strtime",
        theme: "advanced",
        skin: "o2k7",
        skin_variant: "silver",
        apply_source_formatting: true,
        remove_script_host: false,
        entity_encoding: "raw",
        plugins: "safari,table,style,layer,advhr,advimage,advlink,emotions,inlinepopups,media,searchreplace,paste,directionality,fullscreen,moodlenolink,dragmath,nonbreaking,contextmenu,insertdatetime,save,iespell,preview,print,noneditable,visualchars,xhtmlxtras,template,pagebreak",
        theme_advanced_font_sizes: "1,2,3,4,5,6,7",
        theme_advanced_layout_manager: "SimpleLayout",
        theme_advanced_toolbar_align : "left",
        theme_advanced_buttons1: "fontselect,fontsizeselect,formatselect,styleselect",
        theme_advanced_buttons1_add: "|,undo,redo,|,search,replace,|,fullscreen",
        theme_advanced_buttons2: "bold,italic,underline,strikethrough,sub,sup,|,justifyleft,justifycenter,justifyright,justifyfull,|,cite,abbr,acronym",
        theme_advanced_buttons2_add: "|,selectall,cleanup,removeformat,pastetext,pasteword,|,forecolor,backcolor,|,ltr,rtl",
        theme_advanced_buttons3: "bullist,numlist,outdent,indent,|,link,unlink,moodlenolink,anchor,|,insertdate,inserttime,|,emotions,image,dragmath,advhr,nonbreaking,charmap",
        theme_advanced_buttons3_add: "|,table,insertlayer,styleprops,visualchars,|,code,preview",
        theme_advanced_fonts: "Trebuchet=Trebuchet MS,Verdana,Arial,Helvetica,sans-serif;Arial=arial,helvetica,sans-serif;Courier New=courier new,courier,monospace;Georgia=georgia,times new roman,times,serif;Tahoma=tahoma,arial,helvetica,sans-serif;Times New Roman=times new roman,times,serif;Verdana=verdana,arial,helvetica,sans-serif;Impact=impact;Wingdings=wingdings",
        theme_advanced_resize_horizontal: true,
        theme_advanced_resizing: true,
        theme_advanced_toolbar_location : "top",
        theme_advanced_statusbar_location : "bottom",
        $xmlruleset
    });

    tinyMCE.init({
        mode: "textareas",
        relative_urls: false,
        editor_selector: "form-tinymce-advanced",
        document_base_url: "$wwwbase",
        content_css: "$contentcss",
        language: "$lang",
        directionality: "$directionality",
        plugin_insertdate_dateFormat : "$strdate",
        plugin_insertdate_timeFormat : "$strtime",
        theme: "advanced",
        skin: "o2k7",
        skin_variant: "silver",
        apply_source_formatting: true,
        remove_script_host: false,
        entity_encoding: "raw",
        plugins: "safari,table,style,layer,advhr,advimage,advlink,emotions,inlinepopups,media,searchreplace,paste,directionality,fullscreen,moodlenolink,dragmath,nonbreaking,contextmenu,insertdatetime,save,iespell,preview,print,noneditable,visualchars,xhtmlxtras,template,pagebreak",
        theme_advanced_font_sizes: "1,2,3,4,5,6,7",
        theme_advanced_layout_manager: "SimpleLayout",
        theme_advanced_toolbar_align : "left",
        theme_advanced_buttons1: "fontselect,fontsizeselect,formatselect,styleselect",
        theme_advanced_buttons1_add: "|,undo,redo,|,search,replace,|,fullscreen",
        theme_advanced_buttons2: "bold,italic,underline,strikethrough,sub,sup,|,justifyleft,justifycenter,justifyright,justifyfull,|,cite,abbr,acronym",
        theme_advanced_buttons2_add: "|,selectall,cleanup,removeformat,pastetext,pasteword,|,forecolor,backcolor,|,ltr,rtl",
        theme_advanced_buttons3: "bullist,numlist,outdent,indent,|,link,unlink,moodlenolink,anchor,|,insertdate,inserttime,|,emotions,image,media,dragmath,advhr,nonbreaking,charmap",
        theme_advanced_buttons3_add: "|,table,insertlayer,styleprops,visualchars,|,code,preview",
        theme_advanced_fonts: "Trebuchet=Trebuchet MS,Verdana,Arial,Helvetica,sans-serif;Arial=arial,helvetica,sans-serif;Courier New=courier new,courier,monospace;Georgia=georgia,times new roman,times,serif;Tahoma=tahoma,arial,helvetica,sans-serif;Times New Roman=times new roman,times,serif;Verdana=verdana,arial,helvetica,sans-serif;Impact=impact;Wingdings=wingdings",
        theme_advanced_resize_horizontal: true,
        theme_advanced_resizing: true,
        theme_advanced_toolbar_location : "top",
        theme_advanced_statusbar_location : "bottom",
        file_browser_callback : "mce_moodlefilemanager",
        $xmlruleset
    });
}

function mce_toggleEditor(id) {
    tinyMCE.execCommand('mceToggleEditor',false,id);
}

function mce_saveOnSubmit(id) {
    var prevOnSubmit = document.getElementById(id).form.onsubmit;
    document.getElementById(id).form.onsubmit = function() {
        tinyMCE.triggerSave();
        var ret = true;
        if (prevOnSubmit != undefined) {
          if (prevOnSubmit()) {
            ret = true;
            prevOnSubmit = null;
          } else {
            ret = false;
          }
        }
        return ret;
    };
}

function mce_moodlefilemanager(field_name, url, type, win) {
    var client_id = id2clientid[tinyMCE.selectedInstance.editorId];
    var picker = document.createElement('DIV');
    picker.className = "file-picker";
    picker.id = 'file-picker-'+client_id;
    document.body.appendChild(picker);
    var el = win.document.getElementById(field_name);
    eval('open_filepicker(client_id, {"env":"editor","target":el,"filetype":type})');
}

// finally init editors
mc_init_editors();

EOF;


$lifetime = '10'; // TODO: increase later
header('Content-type: text/javascript; charset=utf-8');
header('Content-length: '.strlen($output));
header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
header('Cache-control: max-age='.$lifetime);
header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .'GMT');
header('Pragma: ');

echo $output;
