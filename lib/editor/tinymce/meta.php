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
    global $THEME;
}

function use_admin_editor($name='', $editorhidebuttons='', $id='') {
    global $THEME;
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
        $toggle_ed = '<img width="50" height="17" src="'.$CFG->wwwroot.'/lib/editor/tinymce/images/toggle.gif" alt="'.get_string('toggleeditor','editor').'" title="'.get_string('toggleeditor','editor').'" />';
        $str .= "<a href=\"javascript:toggleEditor('".$id."');\">".$toggle_ed."</a> ";
        $str .= '<script type="text/javascript">
            document.write(\''.addslashes_js(editorshortcutshelpbutton()).'\');
        </script>';
    }
    else
    {
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
<script type="text/javascript" src="<?php echo $CFG->wwwroot ?>/lib/editor/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
<?php 
if (($COURSE->id < 2) and has_capability('moodle/site:doanything', get_context_instance(CONTEXT_COURSE, $COURSE->id))) {
    include_once('adminscr.php');
} else {
    if (!empty($COURSE->id) and has_capability('moodle/course:managefiles', get_context_instance(CONTEXT_COURSE, $COURSE->id))) {
        include_once('staff.php');
    } else {
        include_once('student.php');
    }
}
?>
