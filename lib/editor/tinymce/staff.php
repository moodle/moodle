<script type="text/javascript"> 
    tinyMCE.init({
    	relative_urls : false,
        remove_script_host : false,
        document_base_url : "<?php echo $CFG->wwwroot; ?>",
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
        plugins : "safari,spellchecker,table,style,layer,advhr,advimage,advlink,emotions,emoticons,inlinepopups,media,searchreplace,paste,standardmenu,directionality,fullscreen,moodleimage,moodlelink,dragmath,nonbreaking",
	    theme_standard_buttons1_add : "styleselect,selectall,pastetext,pasteword,insertlayer",
        theme_standard_buttons2_add : "styleprops,ltr,rtl,table,nonbreaking,media,advhr,emotions,emoticons,charmap,dragmath,spellchecker,search,code,fullscreen",
<?php 
$hiddenbuttons = $CFG->editorhidebuttons;
if (!empty($hiddenbuttons)) {
    $hiddenbuttons = str_replace(" ", ",", $hiddenbuttons);
    echo 'theme_standard_disable : "'. $hiddenbuttons .'",';
} 
$tinyfts = $CFG->editorfontlist;
if ($tinyfts) {
    $tinyfts = str_replace(":", "=", $tinyfts);
    echo 'theme_standard_fonts : "'. $tinyfts .'",';
} 
?> 
        spellchecker_languages : "+English=en,Danish=da,Dutch=nl,Finnish=fi,French=fr,German=de,Italian=it,Polish=pl,Portuguese=pt,Spanish=es,Swedish=sv",
	    moodleimage_course_id: <?php echo $COURSE->id; ?>,
    	theme_standard_resize_horizontal : true,
	    theme_standard_resizing : true,
        file_browser_callback : "moodlefilemanager",
    	apply_source_formatting : true
    });
    function moodlefilemanager(field_name, url, type, win) {
			
        tinyMCE.activeEditor.windowManager.open({
            file : "<?php echo $CFG->wwwroot ?>/lib/editor/tinymce/jscripts/tiny_mce/plugins/moodlelink/link.php?id=<?php echo $COURSE->id; ?>",
            width : 480,  
            height : 380,
            resizable : "yes",
            inline : "yes",  
            close_previous : "no"
        }, {
            window : win,
            input : field_name
        });
        return false;
    }
    function toggleEditor(id) {
	    var elm = document.getElementById(id);
    	if (tinyMCE.getInstanceById(id) == null)
	    	tinyMCE.execCommand('mceAddControl', false, id);
    	else
	    	tinyMCE.execCommand('mceRemoveControl', false, id);
    }
</script>
