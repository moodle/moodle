<?php // $Id$
/// configuration routines for HTMLArea editor

    require_once("../config.php");
    require_login();

    if (!isadmin()) {
        error("Only admins can access this page");
    }

    if (($data = data_submitted()) && confirm_sesskey()) {

        // do we want default values?
        if (isset($data->resettodefaults)) {
            if (!(reset_to_defaults())) {
                error("Editor settings could not be restored!");
            }
        } else {

            if (!(editor_update_config($data))) {
                error("Editor settings could not be updated!");
            }
        }
        redirect("$CFG->wwwroot/$CFG->admin/editor.php", get_string("changessaved"), 1);

    } else {
        // Generate edit form

        $fontlist = editor_convert_to_array($CFG->editorfontlist);
        $dicts    = editor_get_dictionaries();

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
        print_simple_box_start("center");
        include("editor.html");
        print_simple_box_end();
        print_footer();
    }


/// FUNCTIONS

function editor_convert_to_array ($string) {
/// Converts $CFG->editorfontlist to array

    if (empty($string) || !is_string($string)) {
        return false;
    }
    $fonts = array();

    $lines = explode(";", $string);
    foreach ($lines as $line) {
        if (!empty($line)) {
            list($fontkey, $fontvalue) = explode(":", $line);
            $fonts[$fontkey] = $fontvalue;
        }
    }

   return $fonts;
}

function editor_update_config ($data) {

/// Updates the editor config values.

    if (!is_object($data)) {
        return false;
    }

    // Make array for unwanted characters.
    $nochars = array(chr(33),chr(34),chr(35),chr(36),chr(37),
                     chr(38),chr(39),chr(40),chr(41),chr(42),
                     chr(43),chr(46),chr(47),chr(58),chr(59),
                     chr(60),chr(61),chr(62),chr(63),chr(64),
                     chr(91),chr(92),chr(93),chr(94),chr(95),
                     chr(96),chr(123),chr(124),chr(125),chr(126));

    $fontlist = '';

    // make font string
    $cnt = count($data->fontname);
    for ($i = 0; $i < $cnt; $i++) {
        if (!empty($data->fontname[$i])) {
            $fontlist .= str_replace($nochars, "", $data->fontname[$i]) .":";
            $fontlist .= str_replace($nochars, "", $data->fontnamevalue[$i]) .";";
        }
    }
    // strip last semicolon
    $fontlist = substr($fontlist, 0, strlen($fontlist) - 1);

    // make array of values to update
    $updatedata = array();
    $updatedata['htmleditor'] = !empty($data->htmleditor) ? $data->htmleditor : 0;
    $updatedata['editorbackgroundcolor'] = !empty($data->backgroundcolor) ? $data->backgroundcolor : "#ffffff";
    $updatedata['editorfontfamily'] = !empty($data->fontfamily) ? str_replace($nochars,"",$data->fontfamily) : "Times New Roman, Times";
    $updatedata['editorfontsize'] = !empty($data->fontsize) ? $data->fontsize : "";
    $updatedata['editorkillword'] = !empty($data->killword) ? $data->killword : 0;
    $updatedata['editorspelling'] = !empty($data->spelling) ? $data->spelling : 0;
    $updatedata['editorfontlist'] = $fontlist;
    $updatedata['editordictionary'] = !empty($data->dictionary) ? $data->dictionary : '';
    $updatedata['aspellpath'] = !empty($data->aspellpath) ? $data->aspellpath : '';

    $hidebuttons = '';
    if (!empty($data->buttons) && is_array($data->buttons)) {
        foreach ($data->buttons as $key => $value) {
            $hidebuttons .= $key . " ";
        }
    }
    $updatedata['editorhidebuttons'] = trim($hidebuttons);

    foreach ($updatedata as $name => $value) {
        if (!(set_config($name, $value))) {
            return false;
        }
    }

    return true;
}

function reset_to_defaults () {
/// Reset the values to default

    global $CFG;
    include_once($CFG->dirroot .'/lib/defaults.php');

    $updatedata = array();

    $updatedata['editorbackgroundcolor'] = $defaults['editorbackgroundcolor'];
    $updatedata['editorfontfamily'] = $defaults['editorfontfamily'];
    $updatedata['editorfontsize'] = $defaults['editorfontsize'];
    $updatedata['editorkillword'] = $defaults['editorkillword'];
    $updatedata['editorspelling'] = $defaults['editorspelling'];
    $updatedata['editorfontlist'] = $defaults['editorfontlist'];
    $updatedata['editorhidebuttons'] = $defaults['editorhidebuttons'];
    $updatedata['editordictionary'] = '';

    foreach ($updatedata as $name => $value) {
        if (!(set_config($name, $value))) {
            return false;
        }
    }
    return true;
}

function editor_get_dictionaries () {
/// Get all installed dictionaries in the system

    global $CFG;

    error_reporting(E_ALL); // for debug, final version shouldn't have this...
    clearstatcache();

    $strerror = '';

    // If aspellpath isn't set don't even bother ;-)
    if (empty($CFG->aspellpath)) {
        return $strerror = 'Empty aspell path!';
    }

    // Do we have access to popen function?
    if (!function_exists('popen')) {
        return $strerror = "Popen function disabled!";
        exit;
    }

    global $CFG;

    $cmd          = $CFG->aspellpath;
    $output       = '';
    $dictionaries = array();
    $dicts        = array();

    if(!($handle = @popen(escapeshellarg($cmd) .' dump dicts', 'r'))) {
        return $strerror = "Couldn't create handle!";
        exit;
    }

    while(!feof($handle)) {
        $output .= fread($handle, 1024);
    }
    @pclose($handle);

    $dictionaries = explode(chr(10), $output);

    // Get rid of possible empty values
    if (is_array($dictionaries)) {

        $cnt = count($dictionaries);

        for ($i = 0; $i < $cnt; $i++) {
            if (!empty($dictionaries[$i])) {
                $dicts[] = $dictionaries[$i];
            }
        }
    }

    if (count($dicts) >= 1) {
        return $dicts;
    }

    $strerror = "Error! Check your aspell installation!";
    return $strerror;

}

?>
