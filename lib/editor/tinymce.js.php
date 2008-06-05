<?php

require_once('../../config.php');

/*
 *
 * ********************************************************************************************************
 *
 * This section configures the TinyMCE toolbar buttons on and off
 * depending on the Moodle settings
 *
 * The changes are applied on a global basis,
 * ..... but there is scope here to modify and restrict the config
 * on a role basis, course basis, user basis, etc. if so desired.
 *
 */
$editorhidebuttons = str_replace(' ', ',', $CFG->editorhidebuttons);

$editorhidebuttons1 = $editorhidebuttons . ',visualaid,styleselect';

$editorhidebuttons = 'theme_advanced_disable : "'.$editorhidebuttons1.'",';

$editorhidebuttons = str_replace('fontsize',             'fontsizeselect',       $editorhidebuttons);
$editorhidebuttons = str_replace('subscript',            'sub',                  $editorhidebuttons);
$editorhidebuttons = str_replace('superscript',          'sup',                  $editorhidebuttons);
$editorhidebuttons = str_replace('insertorderedlist',    'numlist',              $editorhidebuttons);
$editorhidebuttons = str_replace('insertunorderedlist',  'bullist',              $editorhidebuttons);
$editorhidebuttons = str_replace('createanchor',         'anchor',               $editorhidebuttons);
$editorhidebuttons = str_replace('createlink',           'link',                 $editorhidebuttons);
$editorhidebuttons = str_replace('htmlmode',             'code',                 $editorhidebuttons);
$editorhidebuttons = str_replace('insertchar',           'charmap',              $editorhidebuttons);
$editorhidebuttons = str_replace('insertimage',          'image',                $editorhidebuttons);
$editorhidebuttons = str_replace('inserthorizontalrule', 'hr',                   $editorhidebuttons);
$editorhidebuttons = str_replace('formatblock',          'formatselect',         $editorhidebuttons);
$editorhidebuttons = str_replace('clean',                'cleanup,removeformat', $editorhidebuttons);

// insertsmile,

$pieces = explode(",", $editorhidebuttons1);

$spellcheck = '';
if (! in_array("spellcheck", $pieces)) {
    $spellcheck = 'spellchecker,';
}
$inserttable = '';
if (! in_array("inserttable", $pieces)) {
    $inserttable = 'tablecontrols,separator,';
}
$search_replace = '';
if (! in_array("search_replace", $pieces)) {
    $search_replace = 'search,replace,separator,';
}
$lefttoright = '';
if (! in_array("lefttoright", $pieces)) {
    $lefttoright = 'ltr,separator,';
}
$righttoleft = '';
if (! in_array("righttoleft", $pieces)) {
    $righttoleft = 'rtl,separator,';
}
$cleanup = '';
if (! in_array("cleanup", $pieces)) {
    $cleanup = 'cleanup,removeformat,separator,';
}
$fontselect = '';
if (! in_array("fontname", $pieces)) {
    $fontselect = 'fontselect,';
}
$fontsize = '';
if (! in_array("fontsize", $pieces)) {
    $fontsize = 'fontsizeselect,';
}
$forecolor = '';
if (! in_array("forecolor", $pieces)) {
    $forecolor = 'forecolor,';
}
$hilitecolor = '';
if (! in_array("hilitecolor", $pieces)) {
    $hilitecolor = 'backcolor,';
}
$popupeditor = '';
if (! in_array("popupeditor", $pieces)) {
    $popupeditor = 'fullscreen,';
}

$editoraddbuttons3 = 'theme_advanced_buttons3_add : "'.$fontselect.$fontsize.$forecolor.$hilitecolor.'",';
$editoraddbuttons4 = 'theme_advanced_buttons4 : "'.$spellcheck.$search_replace.$inserttable.$lefttoright.$righttoleft.$popupeditor.$cleanup.'",';

/*
 *
 * ********************************************************************************************************
 *
 */


$temp = $_SERVER["REQUEST_URI"];
$temp = explode('/', $temp);
$root = $temp[1];

$configuration = <<<EOF

function createHTMLArea(id) {

    random       = Math.ceil(1000*Math.random())
    editor       = 'editor'+random;
    editorsubmit = 'editorsubmit'+random;

    tinyMCE.init({
        mode     : "exact",
        elements : id,
        theme    : "advanced",

        plugins : "safari,spellchecker,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,pagebreak,imagemanager,filemanager",

        spellchecker_languages : "+English=en,Danish=da,Dutch=nl,Finnish=fi,French=fr,German=de,Italian=it,Polish=pl,Portuguese=pt,Spanish=es,Swedish=sv",

        plugin_insertdate_dateFormat : "%Y-%m-%d",
        plugin_insertdate_timeFormat : "%H:%M:%S",

        content_css : "/$root/lib/editor/tinymce/examples/css/content.css",

        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_statusbar_location : "bottom",

        theme_advanced_resize_horizontal : true,
        theme_advanced_resizing : true,
        apply_source_formatting : true,

        $editorhidebuttons
        $editoraddbuttons3
        $editoraddbuttons4

    });

    script = "document.getElementById(id).form."+editorsubmit+" = document.getElementById(id).form.onsubmit;";
    script = script + "document.getElementById(id).form.onsubmit = function() { tinyMCE.triggerSave(); document.getElementById(id).form."+editorsubmit+"(); document.getElementById(id).form."+editorsubmit+" = null;}";
    eval(script);

}

EOF;

echo $configuration;

?>
