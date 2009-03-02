<?php
/**
 * help.php - Displays help page.
 *
 * Prints a very simple page and includes
 * page content or a string from elsewhere.
 * Usually this will appear in a popup
 * See {@link helpbutton()} in {@link lib/moodlelib.php}
 *
 * @author Martin Dougiamas
 * @version $Id$
 * @package moodlecore
 */
require_once('config.php');

// Get URL parameters.
$file   = optional_param('file', '', PARAM_PATH);
$text   = optional_param('text', 'No text to display', PARAM_CLEAN);
$module = optional_param('module', 'moodle', PARAM_ALPHAEXT);
$forcelang = optional_param('forcelang', '', PARAM_SAFEDIR);
$skiplocal = optional_param('skiplocal', 0, PARAM_INT);     // shall _local help files be skipped?

// We look for the help to display in lots of different places, and
// only display an error at the end if we can't find the help file
// anywhere. This variable tracks that.
$helpfound = false;

// Buffer output so that we can examine it later to extract metadata (page title)
ob_start();

if (!empty($file)) {
    // The help to display is from a help file.

    // Get the list of parent languages.
    if (empty($forcelang)) {
        $langs = array(current_language(), get_string('parentlanguage'), 'en_utf8');  // Fallback
    } else {
        $langs = array($forcelang, 'en_utf8');
    }
    
    if (!$skiplocal) {
        // _local language packs take precedence with both forced language and non-forced language settings
        $xlangs = array();
        foreach ($langs as $lang) {
            if (!empty($lang)) {
                $xlangs[] = $lang . '_local';
                $xlangs[] = $lang;
            }
        }
        $langs = $xlangs;
        unset($xlangs);
    }

// Define possible locations for help file similar to locations for language strings
// Note: Always retain module directory as before
    $locations = array();
    if ($module == 'moodle') {
        $locations[$CFG->dataroot.'/lang/'] = $file;
        $locations[$CFG->dirroot.'/lang/'] = $file;
    } else {
        $modfile = $module.'/'.$file;
        $locations[$CFG->dataroot.'/lang/'] = $modfile;
        $locations[$CFG->dirroot.'/lang/'] = $modfile;

        $rules = places_to_search_for_lang_strings();
        $exceptions = $rules['__exceptions'];
        unset($rules['__exceptions']);
        
        if (!in_array($module, $exceptions)) {
            $dividerpos = strpos($module, '_');
            if ($dividerpos === false) {
                $type = '';
                $plugin = $module;
            } else {
                $type = substr($module, 0, $dividerpos + 1);
                $plugin = substr($module, $dividerpos + 1);
            }
            if (!empty($rules[$type])) {
                foreach ($rules[$type] as $location) {
                    $locations[$CFG->dirroot . "/$location/$plugin/lang/"] = "$plugin/$file";
                }
            }
        }
    }

    // Work through the possible languages, starting with the most specific.
    while (!$helpfound && (list(,$lang) = each($langs)) && !empty($lang)) {

        while (!$helpfound && (list($locationprefix,$locationsuffix) = each($locations))) {
            $filepath = $locationprefix.$lang.'/help/'.$locationsuffix;

            // Now, try to include the help text from this file, if we can.
            if (file_exists_and_readable($filepath)) {
                $helpfound = true;
                @include($filepath);   // The actual helpfile

                // Now, we process some special cases.
                $helpdir = $locationprefix.$lang.'/help';
                if ($module == 'moodle' and ($file == 'index.html' or $file == 'mods.html')) {
                    include_help_for_each_module($file, $langs, $helpdir);
                }
                if ($module == 'question' and ($file == 'types.html')) {
                    include_help_for_each_qtype();
                }

                // The remaining horrible hardcoded special cases should be delegated to modules somehow.
                if ($module == 'moodle' and ($file == 'resource/types.html')) {  // RESOURCES
                    include_help_for_each_resource($file, $langs, $helpdir);
                }
                if ($module == 'moodle' and ($file == 'assignment/types.html')) {  // ASSIGNMENTS
                    include_help_for_each_assignment_type();
                }
            }
        }
        reset($locations);
    }
} else {
    // The help to display was given as an argument to this function.
    echo '<p>'.s($text).'</p>';   // This param was already cleaned
    $helpfound = true;
}

// Finish buffer
$output=ob_get_contents();
ob_end_clean();

// Determine title
$title=get_string('help'); // Default is just 'Help'
$matches=array();
// You can include a <title> tag to override the standard behaviour:
// 'Help - title contents'. Otherwise it looks for the text of the first
// heading: 'Help - heading text'. If there aren't even any headings
// you just get 'Help'
if(preg_match('~^(.*?)<title>(.*?)</title>(.*)$~s',$output,$matches)) {
    // Extract title
    $title=$title.' - '.$matches[2]; 
    // Strip title from output
    $output=$matches[1].$matches[3];
} else if(preg_match('~<h[0-9]+(\s[^>]*)?>(.*?)</h[0-9]+>~s',$output,$matches)) {
    // Use first heading as title (obviously leave it in output too). Strip
    // any tags from inside
    $matches[2]=preg_replace('~<[^>]*>~s','',$matches[2]);
    $title=$title.' - '.$matches[2];
}

// use ##emoticons_html## to replace the emoticons documentation
if(preg_match('~(##emoticons_html##)~', $output, $matches)) {
    $output = preg_replace('~(##emoticons_html##)~', get_emoticons_list_for_help_file(), $output);
}

// Do the main output.
print_header($title);
print_simple_box_start();
print $output;
print_simple_box_end();

// Display an error if necessary.
if (!$helpfound) {
    notify('Help file "'. $file .'" could not be found!');
}

// End of page.
close_window_button();
echo '<p class="helpindex"><a href="help.php?file=index.html">'. get_string('helpindex') .'</a>';

// Offer a link to the alternative help file language
if (($helpfound) and (((current_language() != 'en_utf8') and $lang != 'en_utf8') or ($forcelang === 'en_utf8'))) {
    $linklang = "{$CFG->wwwroot}/help.php?";
    $linklang .= !empty($module)    ? "module=$module&amp;" : '';
    $linklang .= !empty($file)      ? "file=$file&amp;" : '';
    $linklang .= !empty($skiplocal) ? "skiplocal=$skiplocal&amp;" : '';

    if (empty($forcelang) or $forcelang === current_language()) {
        $nextlang = 'en_utf8';
        $nextlangname = 'English';
    } else {
        $nextlang = current_language();
        $nextlangname = get_string('thislanguage');
    }

    $linklang .= "forcelang=$nextlang";
    echo "<br /><a href=\"$linklang\">" . get_string('showthishelpinlanguage', 'moodle', $nextlangname) . '</a>';
}
echo '</p>';

$CFG->docroot = '';   // We don't want a doc link here
print_footer('none');

function file_exists_and_readable($filepath) {
    return file_exists($filepath) and is_file($filepath) and is_readable($filepath);
}

// Some functions for handling special cases ========================================

function include_help_for_each_module($file, $langs, $helpdir) {
    global $CFG, $DB;

    if (!$modules = $DB->get_records('modules', array('visible'=>1))) {
        print_error('nomodules', 'debug');        // Should never happen
    }
    
    $grade = new stdClass();
    $grade->name = 'grade';
    $modules[] = $grade;

    foreach ($modules as $mod) {
        $strmodulename = get_string('modulename', $mod->name);
        $modulebyname[$strmodulename] = $mod;
    }
    ksort($modulebyname, SORT_LOCALE_STRING);

    foreach ($modulebyname as $mod) {
        foreach ($langs as $lang) {
            if (empty($lang)) {
                continue;
            }

            $filepath = "$helpdir/$mod->name/$file";

            // If that does not exist, try a fallback into the module code folder.
            if (!file_exists($filepath)) {
                $filepath = "$CFG->dirroot/mod/$mod->name/lang/$lang/help/$mod->name/$file";
            }

            if (file_exists_and_readable($filepath)) {
                echo '<hr />';
                @include($filepath); // The actual helpfile
                break; // Out of loop over languages.
            }
        }
    }
}

function include_help_for_each_qtype() {
    global $CFG;
    require_once($CFG->libdir . '/questionlib.php');
    global $QTYPES;
    $types = question_type_menu();
    $fakeqtypes = array();
    foreach ($types as $qtype => $localizedname) {
        if ($QTYPES[$qtype]->is_real_question_type()) {
            include_help_for_qtype($qtype, $localizedname);
        } else {
            $fakeqtypes[$qtype] = $localizedname;
        }
    }
    foreach ($fakeqtypes as $qtype => $localizedname) {
        include_help_for_qtype($qtype, $localizedname);
    }
}
function include_help_for_qtype($qtype, $localizedname) {
    echo '<h2>' . $localizedname . "</h2>\n\n";
    echo '<p>' . get_string($qtype . 'summary', 'qtype_' . $qtype) . "</p>\n\n";
}

function include_help_for_each_resource($file, $langs, $helpdir) {
    global $CFG;

    require_once($CFG->dirroot .'/mod/resource/lib.php');
    $typelist = resource_get_types();
    
    //add label type
    $labelType = new object();
    $labelType->modclass = MOD_CLASS_RESOURCE;
    $resourcetype = 'label';
    $labelType->name = $resourcetype;
    $labelType->type = "resource&amp;type=$resourcetype";
    $labelType->typestr = get_string("resourcetype$resourcetype", 'resource');
    $typelist[] = $labelType;

    foreach ($typelist as $type) {
   
        foreach ($langs as $lang) {
            if (empty($lang)) {
                continue;
            }

            $filepath = "$helpdir/resource/type/".$type->name.".html";

            if (file_exists_and_readable($filepath)) {
                echo '<hr />';
                @include($filepath); // The actual helpfile
                break; // Out of loop over languages.
            }
        }
    }
}

function include_help_for_each_assignment_type() {
    global $CFG;

    require_once($CFG->dirroot .'/mod/assignment/lib.php');
    $typelist = assignment_types();

    foreach ($typelist as $type => $name) {
        echo '<p><b>'.$name.'</b></p>';
        echo get_string('help'.$type, 'assignment');
        echo '<hr size="1" />';
    }
}
?>
