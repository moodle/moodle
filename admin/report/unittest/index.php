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
require_once(dirname(__FILE__).'/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/simpletestlib.php');
require_once('ex_simple_test.php');
require_once('ex_reporter.php');

// CGI arguments
$path = optional_param('path', null, PARAM_PATH);
$showpasses = optional_param('showpasses', false, PARAM_BOOL);
$showsearch = optional_param('showsearch', false, PARAM_BOOL);
$thorough = optional_param('thorough', false, PARAM_BOOL);

// Print the header.
admin_externalpage_setup('reportunittest', '', array('showpasses' => $showpasses,
        'showsearch' => $showsearch, 'thorough' => $thorough));
admin_externalpage_print_header();

/* The UNITTEST constant can be checked elsewhere if you need to know
 * when your code is being run as part of a unit test. */
define('UNITTEST', true);
$langfile = 'simpletest';

$strtitle = get_string('unittests', $langfile);

if (!is_null($path)) {
    // Create the group of tests.
    $test =& new AutoGroupTest($showsearch, $thorough);

    // OU specific. We use the _nonproject folder for stuff we want to
    // keep in CVS, but which is not really relevant. It does no harm
    // to leave this here.
    $test->addIgnoreFolder($CFG->dirroot . '/_nonproject');

    // Make the reporter, which is what displays the results.
    $reporter = new ExHtmlReporter($showpasses);

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

    $formheader = get_string('retest', $langfile);
} else {
    $displaypath = '';
    $formheader = get_string('rununittests', $langfile);
}
// Print the form for adjusting options.
print_simple_box_start('center', '70%');
echo '<form method="get" action="index.php">';
echo '<fieldset class="invisiblefieldset">';
print_heading($formheader);
echo '<p>'; print_checkbox('showpasses', 1, $showpasses, get_string('showpasses', $langfile)); echo '</p>';
echo '<p>'; print_checkbox('showsearch', 1, $showsearch, get_string('showsearch', $langfile)); echo '</p>';
echo '<p>'; print_checkbox('thorough', 1, $thorough, get_string('thorough', $langfile)); echo '</p>';
echo '<p>';
    echo '<label for="path">', get_string('onlytest', $langfile), '</label> ';
    echo '<input type="text" id="path" name="path" value="', $displaypath, '" size="40" />';
echo '</p>';
echo '<input type="submit" value="' . get_string('runtests', $langfile) . '" />';
echo '</fieldset>';
echo '</form>';
print_simple_box_end();

// Footer.
admin_externalpage_print_footer();

?>
