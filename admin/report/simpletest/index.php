<?php
/**
 * Run the unit tests.
 *
 * @copyright &copy; 2006 The Open University
 * @author N.D.Freear@open.ac.uk, T.J.Hunt@open.ac.uk
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @version $Id$
 * @package SimpleTestEx
 */

/** */
require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir . '/moodlelib.php');
require_once('ex_simple_test.php');
require_once('ex_reporter.php');

/* The UNITTEST constant can be checked elsewhere if you need to know
 * when your code is being run as part of a unit test. */
define('UNITTEST', true);
$langfile = 'simpletest';

require_login();
require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM, SITEID));
// CGI arguments
$path = optional_param('path', '', PARAM_PATH);
$showpasses = optional_param('showpasses', false, PARAM_BOOL);
$showsearch = optional_param('showsearch', false, PARAM_BOOL);
$thorough = optional_param('thorough', false, PARAM_BOOL);

// Create the group of tests.
$test =& new AutoGroupTest($showsearch, $thorough);

// OU specific. We use the _nonproject folder for stuff we want to 
// keep in CVS, but which is not really relevant. It does no harm
// to leave this here.
$test->addIgnoreFolder($CFG->dirroot . '/_nonproject');

// Make the reporter, which is what displays the results.
$reporter = new ExHtmlReporter($showpasses);

// Print the header.
$strtitle = get_string('unittests', $langfile);
$stradmin = get_string('administration');
print_header("$SITE->shortname: $strtitle", $SITE->fullname,
        '<a href="../../index.php">' . get_string('administration') . '</a> -> ' .
        '<a href="../../misc.php">' . get_string('miscellaneous') . '</a> -> ' .
        '<a href="../../report.php">' . get_string('reports') . '</a> -> ' .
        $strtitle, '', '<style type="text/css">' . $reporter->_getCss() . '</style>');
if ($showsearch) {
    print_heading('Searching for test cases');
}
flush();

// Work out what to test.
if (substr($path, 0, 1) == '/') {
    $path = substr($path, 1);
}
$path = $CFG->dirroot . '/' . $path;
if (substr($path, -1) == '/') {
    $path = substr($path, 0, -1);
}
$displaypath = substr($path, strlen($CFG->dirroot) + 1);
$ok = true;
if (is_file($path)) {
    $test->addTestFile($path);
} else if (is_dir($path)){
    $test->findTestFiles($path);
} else {
    print_simple_box(get_string('pathdoesnotexist', $langfile, $path), '', '', '', '', 'errorbox');
    $ok = false;
}

// If we have something to test, do it.
if ($ok) {
    if ($path == $CFG->dirroot) {
        $title = get_string('moodleunittests', $langfile, get_string('all', $langfile));
    } else {
        $title = get_string('moodleunittests', $langfile, $displaypath);
    }
    print_heading($title);
    $test->run($reporter);
}

// Print the form for adjusting options.
print_simple_box_start('center', '70%');
echo '<form method="GET" action="index.php">';
print_heading(get_string('retest', $langfile));
echo '<p>'; print_checkbox('showpasses', 1, $showpasses, get_string('showpasses', $langfile)); echo '</p>';
echo '<p>'; print_checkbox('showsearch', 1, $showsearch, get_string('showsearch', $langfile)); echo '</p>';
echo '<p>'; print_checkbox('thorough', 1, $thorough, get_string('thorough', $langfile)); echo '</p>';
echo '<p>';
    echo '<label for="path">', get_string('onlytest', $langfile), '</label> ';
    echo '<input type="text" id="path" name="path" value="', $displaypath, '" size="60" />';
echo '</p>';
echo '<input type="submit" value="' . get_string('runtests', $langfile) . '" />';
echo '</form>';
print_simple_box_end();

// Footer.
print_footer();

?>