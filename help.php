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
 * @package moodlecore
 */
require_once('config.php');

// Get URL parameters.
$file = optional_param('file', '', PARAM_PATH);
$text = optional_param('text', 'No text to display', PARAM_CLEAN);
$module = optional_param('module', 'moodle', PARAM_ALPHAEXT);
$forcelang = optional_param('forcelang', '', PARAM_SAFEDIR);
$skiplocal = optional_param('skiplocal', 0, PARAM_INT);     // shall _local help files be skipped?
$fortooltip = optional_param('fortooltip', 0, PARAM_INT);

$PAGE->set_course($COURSE);

$url = new moodle_url('/help.php');
if ($file !== '')  {
    $url->param('file', $file);
}
if ($text !== 'No text to display')  {
    $url->param('text', $text);
}
if ($module !== 'moodle')  {
    $url->param('module', $module);
}
if ($forcelang !== '')  {
    $url->param('forcelang', $forcelang);
}
if ($skiplocal !== 0)  {
    $url->param('skiplocal', $skiplocal);
}
if ($fortooltip !== 0)  {
    $url->param('fortooltip', $fortooltip);
}
$PAGE->set_url($url);

// We look for the help to display in lots of different places, and
// only display an error at the end if we can't find the help file
// anywhere. This variable tracks that.
$helpfound = false;

// Buffer output so that we can examine it later to extract metadata (page title)
ob_start();

if (!empty($file)) {
    // The help to display is from a help file.
    list($filepath, $foundlang) = get_string_manager()->find_help_file($file, $module, $forcelang, $skiplocal);

    if ($filepath) {
        $helpfound = true;
        @include($filepath);   // The actual helpfile

        // Now, we process some special cases.
        if ($module == 'moodle' and ($file == 'index.html' or $file == 'mods.html')) {
            include_help_for_each_module($file, $forcelang, $skiplocal);
        }
        if ($module == 'question' && $file == 'types.html') {
            include_help_for_each_qtype();
        }

        // The remaining horrible hardcoded special cases should be delegated to modules somehow.
        if ($module == 'moodle' && $file == 'assignment/types.html') {  // ASSIGNMENTS
            include_help_for_each_assignment_type($forcelang, $skiplocal);
        }
    }
} else {
    // The help to display was given as an argument to this function.
    echo '<p>'.s($text).'</p>';   // This param was already cleaned
    $helpfound = true;
}

// Finish buffer
$output = ob_get_contents();

ob_end_clean();

if ($fortooltip) {
    echo shorten_text($output, 400, false, '<span class="readmore">' . get_string('clickhelpiconformoreinfo') . '</span>');
    die();
}

// Determine title
$title = get_string('help'); // Default is just 'Help'
$matches = array();
// You can include a <title> tag to override the standard behaviour:
// 'Help - title contents'. Otherwise it looks for the text of the first
// heading: 'Help - heading text'. If there aren't even any headings
// you just get 'Help'
if (preg_match('~^(.*?)<title>(.*?)</title>(.*)$~s', $output, $matches)) {
    // Extract title
    $title = $title.' - '.$matches[2];
    // Strip title from output
    $output = $matches[1].$matches[3];
} else if(preg_match('~<h[0-9]+(\s[^>]*)?>(.*?)</h[0-9]+>~s',$output,$matches)) {
    // Use first heading as title (obviously leave it in output too). Strip
    // any tags from inside
    $matches[2] = preg_replace('~<[^>]*>~s','',$matches[2]);
    $title = $title.' - '.$matches[2];
}

// use ##emoticons_html## to replace the emoticons documentation
if(preg_match('~(##emoticons_html##)~', $output, $matches)) {
    $output = preg_replace('~(##emoticons_html##)~', get_emoticons_list_for_help_file(), $output);
}

// Do the main output.
$PAGE->set_pagelayout('popup');
$PAGE->set_title($title);
echo $OUTPUT->header();
echo $OUTPUT->box_start();
print $output;
echo $OUTPUT->box_end();

// Display an error if necessary.
if (!$helpfound) {
    echo $OUTPUT->notification('Help file "'. $file .'" could not be found!');
}

// End of page.
echo $OUTPUT->close_window_button();
echo '<p class="helpindex"><a href="help.php?file=index.html">'. get_string('helpindex') .'</a></p>';

// Offer a link to the alternative help file language
$currentlang = current_language();
if ($file && $helpfound && ($foundlang != 'en_utf8' || ($forcelang == 'en_utf8' && current_language() != 'en_utf8'))) {
    $url = new moodle_url($PAGE->url);
    if ($foundlang != 'en_utf8') {
        $url->param('forcelang', 'en_utf8');
        $nextlangname = get_string('english');
    } else {
        $url->param('forcelang', $currentlang);
        $nextlangname = get_string('thislanguage');
    }
    echo '<p><a href="' . $url->out() . '">' . get_string('showthishelpinlanguage', 'moodle', $nextlangname) . '</a></p>';
}

$CFG->docroot = '';   // We don't want a doc link here
echo $OUTPUT->footer();

function file_exists_and_readable($filepath) {
    return file_exists($filepath) and is_file($filepath) and is_readable($filepath);
}

// Some functions for handling special cases ========================================

function include_help_for_each_module($file, $forcelang, $skiplocal) {
    global $CFG, $DB;

    if (!$modules = $DB->get_records('modules', array('visible'=> 1))) {
        print_error('nomodules', 'debug'); // Should never happen
    }

    // Horrible hack to show the help about grades here too.
    $grade = new stdClass();
    $grade->name = 'grade';
    $modules[] = $grade;

    foreach ($modules as $mod) {
        $strmodulename = get_string('modulename', $mod->name);
        $modulebyname[$strmodulename] = $mod;
    }
    ksort($modulebyname, SORT_LOCALE_STRING);

    foreach ($modulebyname as $mod) {
        list($filepath, $foundlang) = get_string_manager()->find_help_file($file, $mod->name, $forcelang, $skiplocal);
        if ($filepath) {
            echo '<hr />';
            include($filepath);
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

function include_help_for_each_assignment_type() {
    global $CFG;

    require_once($CFG->dirroot .'/mod/assignment/lib.php');
    $typelist = assignment_types();

    foreach ($typelist as $type => $name) {
        echo '<h2>'.$name.'</h2>';
        echo get_string('help'.$type, 'assignment');
        echo '<hr />';
    }
}
?>
