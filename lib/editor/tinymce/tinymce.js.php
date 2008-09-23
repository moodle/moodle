<?php  //$Id$

define('NO_MOODLE_COOKIES', true);

require_once('../../../config.php');

$editorlanguage = optional_param('editorlanguage', 'en_utf8', PARAM_ALPHANUMEXT);

$SESSION->lang = $editorlanguage;
$directionality = get_string('thisdirection');

$strtime = get_string('strftimetime');
$strdate = get_string('strftimedaydate');

// $contentcss should be customizable, but default to this.
$contentcss = $CFG->themewww .'/'. current_theme() .'/styles.php';

$output = <<<EOF
    tinyMCE.init({
        mode: "textareas",
        relative_urls: false,
        editor_selector: "form-textarea-simple",
        document_base_url: "$CFG->httpswwwroot",
        content_css: "$contentcss",
        theme: "simple",
        skin: "o2k7",
        skin_variant: "silver",
        apply_source_formatting: true, 
        remove_script_host: false,
        entity_encoding: "raw",
        language: "$editorlanguage",
        directionality: "$directionality",
        plugins: "spellchecker,emoticons,paste,directionality,contextmenu"
    });
    tinyMCE.init({
        mode: "textareas",
        relative_urls: false,
        editor_selector: "form-textarea-advanced",
        document_base_url: "$CFG->httpswwwroot",
        content_css: "$contentcss",
        theme: "advanced",
        skin: "o2k7",
        skin_variant: "silver",
        apply_source_formatting: true, 
        remove_script_host: false,
        entity_encoding: "raw",
        language: "$editorlanguage",
        directionality: "$directionality",
        plugins: "safari,spellchecker,table,style,layer,advhr,advimage,advlink,emoticons,inlinepopups,media,searchreplace,paste,directionality,fullscreen,moodlenolink,dragmath,nonbreaking,contextmenu,insertdatetime,save,iespell,preview,print,noneditable,visualchars,xhtmlxtras,template,pagebreak",
        plugin_insertdate_dateFormat : "$strdate",
        plugin_insertdate_timeFormat : "$strtime",
        theme_advanced_font_sizes: "1,2,3,4,5,6,7",
        theme_advanced_layout_manager: "SimpleLayout",
        theme_advanced_toolbar_align : "left",
        theme_advanced_buttons1: "fontselect,fontsizeselect,formatselect,styleselect",
        theme_advanced_buttons1_add: "|,undo,redo,|,search,replace,spellchecker,|,fullscreen",
        theme_advanced_buttons2: "bold,italic,underline,strikethrough,sub,sup,|,justifyleft,justifycenter,justifyright,justifyfull,|,cite,abbr,acronym",
        theme_advanced_buttons2_add: "|,selectall,cleanup,removeformat,pastetext,pasteword,|,forecolor,backcolor,|,ltr,rtl",
        theme_advanced_buttons3: "bullist,numlist,outdent,indent,|,link,unlink,moodlenolink,anchor,|,insertdate,inserttime,|,emoticons,image,media,dragmath,advhr,nonbreaking,charmap",
        theme_advanced_buttons3_add: "|,table,insertlayer,styleprops,visualchars,|,code,preview",
        theme_advanced_fonts: "Trebuchet=Trebuchet MS,Verdana,Arial,Helvetica,sans-serif;Arial=arial,helvetica,sans-serif;Courier New=courier new,courier,monospace;Georgia=georgia,times new roman,times,serif;Tahoma=tahoma,arial,helvetica,sans-serif;Times New Roman=times new roman,times,serif;Verdana=verdana,arial,helvetica,sans-serif;Impact=impact;Wingdings=wingdings", 
        theme_advanced_resize_horizontal: true,
        theme_advanced_resizing: true,
        theme_advanced_toolbar_location : "top",
        theme_advanced_statusbar_location : "bottom",
        file_browser_callback : "moodlefilemanager",

EOF;
// the xhtml ruleset must be the last one - no comma at the end of the file
$output .= file_get_contents('xhtml_ruleset.txt');
$output .= <<<EOF
    });

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
    function moodlefilemanager(field_name, url, type, win) {
        //alert(id2suffix[tinyMCE.selectedInstance.editorId]);
        var suffix = id2suffix[tinyMCE.selectedInstance.editorId];
        document.body.className += ' yui-skin-sam';
        var picker = document.createElement('DIV');
        picker.className = "file-picker";
        picker.id = 'file-picker-'+suffix;
        document.body.appendChild(picker);
        var el = win.document.getElementById(field_name);
        eval('openpicker_'+suffix+'({"env":"editor","target":el})');
    }
EOF;

$lifetime = '86400';
@header('Content-type: text/javascript; charset=utf-8');
@header('Content-length: '.strlen($output));
@header('Last-Modified: '. gmdate('D, d M Y H:i:s', time()) .' GMT');
@header('Cache-control: max-age='.$lifetime);
@header('Expires: '. gmdate('D, d M Y H:i:s', time() + $lifetime) .'GMT');
@header('Pragma: ');

echo $output;

?>
