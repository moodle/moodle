<?php // $Id$
/// configuration routines for HTMLArea editor

    require_once("../config.php");
    require_login();

    if (!isadmin()) {
        error("Only admins can access this page");
    }

    if($data = data_submitted()) {

        if(!(editor_update_config($data))) {
            error("Editor settings could not be updated!");
        }
        redirect("$CFG->wwwroot/$CFG->admin/editor.php", get_string("changessaved"), 1);

    } else {
        // Generate edit form

        $fontlist = editor_convert_to_array($CFG->editorfontlist);

        $stradmin = get_string("administration");
        $strconfiguration = get_string("configuration");
        $streditorsettings = get_string("editorsettings");
        $streditorsettingshelp = get_string("adminhelpeditorsettings");
        print_header("Editor settings","Editor settings",
                     "<a href=\"index.php\">$stradmin</a> -> ".
                     "<a href=\"configure.php\">$strconfiguration</a> -> $streditorsettings");
        print_heading($streditorsettings);
        print_simple_box("<center>$streditorsettingshelp</center>","center","50%");
        print("<br />\n");
        print_simple_box_start("center", "", "$THEME->cellheading");
        include("editor.html");
        print_simple_box_end();
        print_footer();
    }


/// FUNCTIONS

function editor_convert_to_array ($string) {
/// Converts $CFG->editorfontlist to array

    if(empty($string) || !is_string($string)) {
        return false;
    }
    $fonts = array();

    $lines = explode(";", $string);
    foreach($lines as $line) {
        if(!empty($line)) {
            list($fontkey, $fontvalue) = explode(":", $line);
            $fonts[$fontkey] = $fontvalue;
        }
    }

   return $fonts;
}

function editor_update_config ($data) {
/// Updates the editor config values.

    if(!is_object($data)) {
        return false;
    }

    // make font string
    for($i = 0; $i < count($data->fontname); $i++) {
        if(!empty($data->fontname[$i])) {
            $fontlist .= $data->fontname[$i] .":";
            $fontlist .= $data->fontnamevalue[$i] .";";
        }
    }
    // strip last semicolon
    $fontlist = substr($fontlist, 0, strlen($fontlist) - 1);

    // make array of values to update
    $updatedata = array();
    $updatedata['editorbackgroundcolor'] = $data->backgroundcolor;
    $updatedata['editorfontfamily'] = $data->fontfamily;
    $updatedata['editorfontsize'] = $data->fontsize;
    $updatedata['editorkillword'] = $data->killword;
    $updatedata['editorspelling'] = $data->spelling;
    $updatedata['editorfontlist'] = $fontlist;

    foreach($updatedata as $name => $value) {
        if(!(set_config($name, $value))) {
            return false;
        }
    }

    return true;
}
?>