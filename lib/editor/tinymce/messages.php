<?php 
function print_editor_config($editorhidebuttons='', $return=false) {
    global $CFG;

    $str = "config.pageStyle = \"body {";

    if (!(empty($CFG->editorbackgroundcolor))) {
        $str .= " background-color: $CFG->editorbackgroundcolor;";
    }

    if (!(empty($CFG->editorfontfamily))) {
        $str .= " font-family: $CFG->editorfontfamily;";
    }

    if (!(empty($CFG->editorfontsize))) {
        $str .= " font-size: $CFG->editorfontsize;";
    }

    $str .= " }\";\n";
    $str .= "config.killWordOnPaste = ";
    $str .= (empty($CFG->editorkillword)) ? "false":"true";
    $str .= ';'."\n";
    $str .= 'config.fontname = {'."\n";

    $fontlist = isset($CFG->editorfontlist) ? explode(';', $CFG->editorfontlist) : array();
    $i = 1;                     // Counter is used to get rid of the last comma.

    foreach ($fontlist as $fontline) {
        if (!empty($fontline)) {
            if ($i > 1) {
                $str .= ','."\n";
            }
            list($fontkey, $fontvalue) = split(':', $fontline);
            $str .= '"'. $fontkey ."\":\t'". $fontvalue ."'";

            $i++;
        }
    }
    $str .= '};';

    if (!empty($editorhidebuttons)) {
        $str .= "\nconfig.hideSomeButtons(\" ". $editorhidebuttons ." \");\n";
    } else if (!empty($CFG->editorhidebuttons)) {
        $str .= "\nconfig.hideSomeButtons(\" ". $CFG->editorhidebuttons ." \");\n";
    }

    if (!empty($CFG->editorspelling) && !empty($CFG->aspellpath)) {
        $str .= print_speller_code($CFG->htmleditor, true);
    }

    if ($return) {
        return $str;
    }
    echo $str;
}

function use_html_editor($name='', $editorhidebuttons='', $id='') {
}

function use_admin_editor($name='', $editorhidebuttons='', $id='') {
    echo '<script type="text/javascript">tsetup();</script>';
}

function print_textarea($usehtmleditor, $rows, $cols, $width, $height, $name, $value='', $courseid=0, $return=false, $id='') {
    global $CFG, $COURSE, $HTTPSPAGEREQUIRED;
    $str = '';
    if ($id === '') {
        $id = 'edit-'.$name;
    }
    if (empty($courseid)) {
        $courseid = $COURSE->id;
    }
    if ($usehtmleditor) {
        $str .= '<textarea class="form-textarea" id="'. $id .'" name="'. $name .'" rows="'. $rows .'" cols="'. $cols .'">';
        $str .= htmlspecialchars($value); 
        $str .= '</textarea><br />'."\n";
        $toggle_ed = '<img width="50" height="17" src="'.$CFG->wwwroot.'/lib/editor/tinymce/images/toggle.gif" '.
            'alt="'.get_string('toggleeditor','editor').'" title="'.get_string('toggleeditor','editor').'" />';
        $str .= "<a href=\"javascript:toggleEditor('".$id."');\">".$toggle_ed."</a> ";
        $str .= '<script type="text/javascript">'."\n".
            'document.write(\''.addslashes_js(editorshortcutshelpbutton()).'\');'."\n".
            '</script>';
    } else {
        $str .= '<textarea class="alltext" id="'. $id .'" name="'. $name .'" rows="'. $rows .'" cols="'. $cols .'">';  
        $str .= s($value);
        $str .= '</textarea>'."\n";
    }
    if ($return) {
        return $str;
    }
    echo $str;
}
?>
<script type="text/javascript" src="<?php echo $CFG->httpswwwroot ?>/lib/editor/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
function tsetup() { 
<?php
if (!empty($COURSE->id) and has_capability('moodle/course:managefiles', get_context_instance(CONTEXT_COURSE, $COURSE->id))) {
?>
	tinyMCE.init({
		relative_urls : false,
        remove_script_host : false,
        document_base_url : "<?php echo $CFG->wwwroot; ?>",
		editor_selector : "form-textarea",
		mode : "textareas",
		theme : "standard",
<?php 
if (!empty($USER->id)) {
    if ($CFG->usehtmleditor == 'tinymce') {
        echo 'skin : "o2k7",';
    } else {
        echo 'skin : "default",';
    }
} 
?>
        entity_encoding : "raw",
		plugins : "safari,emoticons,searchreplace,fullscreen,advimage,advlink,moodleimage,moodlelink",
		
		theme_standard_buttons1 : "fontselect,fontsizeselect,formatselect",
		theme_standard_buttons2 : "bold,italic,underline,forecolor,backcolor,link,unlink,image,emoticons,charmap,code,fullscreen",
        theme_standard_buttons3 : "",
		theme_standard_toolbar_location : "top",
		theme_standard_toolbar_align : "left",
		theme_standard_statusbar_location : "bottom",
		moodleimage_course_id: <?php echo $COURSE->id; ?>,
		theme_standard_resize_horizontal : true,
		theme_standard_resizing : true,
		file_browser_callback : "moodlefilemanager",
		apply_source_formatting : true		
	
	});
    function moodlefilemanager(field_name, url, type, win) {
			
  	    tinyMCE.activeEditor.windowManager.open({
            file : "<?php echo $CFG->httpswwwroot ?>/lib/editor/tinymce/jscripts/tiny_mce/plugins/moodlelink/link.php?id=<?php echo $COURSE->id; ?>",
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
<?php
} else {
?>
    tinyMCE.init({
        relative_urls : false,
        remove_script_host : false,
        document_base_url : "<?php echo $CFG->httpswwwroot; ?>",
        editor_selector : "form-textarea",
        mode : "textareas",
        entity_encoding : "raw",
        theme : "standard",
        plugins : "safari,emoticons,searchreplace,fullscreen,advimage,advlink",
        theme_standard_buttons1 : "fontselect,fontsizeselect,formatselect",
        theme_standard_buttons2 : "bold,italic,underline,forecolor,backcolor,link,unlink,image,emoticons,charmap,code,fullscreen",
        theme_standard_buttons3 : "",
        theme_standard_toolbar_location : "top",
        theme_standard_toolbar_align : "left",
        theme_standard_statusbar_location : "bottom",
        moodleimage_course_id: <?php echo $COURSE->id; ?>,
        theme_standard_resize_horizontal : true,
        theme_standard_resizing : true,
        apply_source_formatting : true
    });

<?php
}
?>
} /* end of tsetup() */
function toggleEditor(id) {
	var elm = document.getElementById(id);

	if (tinyMCE.getInstanceById(id) == null)
		tinyMCE.execCommand('mceAddControl', false, id);
	else
		tinyMCE.execCommand('mceRemoveControl', false, id);
}
</script>
