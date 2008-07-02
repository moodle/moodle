<script type="text/javascript"> 
    tinyMCE.init({
        relative_urls : false,
        remove_script_host : false,
        document_base_url : "<?php echo $CFG->httpswwwroot; ?>",
        editor_selector : "form-textarea",
        mode : "textareas",
        theme : "standard",
<?php 
if (!empty($USER->id)) {
    if ($CFG->defaulthtmleditor == 'tinymce') {
        echo 'skin : "o2k7",';
    } else {
        echo 'skin : "default",';
    }
}
?>
        entity_encoding : "raw",
        theme_standard_statusbar_location : "bottom",
        language : "<?php echo str_replace("_utf8", "", current_language()) ?>",
<?php 
    include_once('langlist.php');
    echo "\n";
    include_once('xhtml_ruleset.txt'); 
?> 
        plugins : "safari,spellchecker,table,style,advhr,advimage,advlink,emotions,emoticons,inlinepopups,searchreplace,standardmenu,paste,directionality,fullscreen,dragmath,nonbreaking",	
        theme_standard_buttons1_add : "styleselect,pastetext,pasteword,selectall",
        theme_standard_buttons2_add : "ltr,rtl,table,nonbreaking,advhr,emotions,emoticons,charmap,dragmath,search,code,fullscreen",
<?php 
$hidbut = $CFG->editorhidebuttons;
if ($hidbut) {
    $hidbut = str_replace(" ",",",$hidbut);
    echo 'theme_standard_disable : "'.$hidbut.'",';
} 
$tinyfts = $CFG->editorfontlist;
if ($tinyfts) {
    $tinyfts = str_replace(":","=",$tinyfts);
    echo 'theme_standard_fonts : "'.$tinyfts.'",';
} 
?>
        moodleimage_course_id: <?php echo $COURSE->id; ?>,
    	theme_standard_resize_horizontal : true,
    	theme_standard_resizing : true,
	    apply_source_formatting : true
    });
    function toggleEditor(id) {
	    var elm = document.getElementById(id);
    	if (tinyMCE.getInstanceById(id) == null)
	    	tinyMCE.execCommand('mceAddControl', false, id);
    	else
	    	tinyMCE.execCommand('mceRemoveControl', false, id);
    }
</script>
