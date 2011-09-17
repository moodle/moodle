<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Run the unit tests.
 *
 * @package    tool
 * @subpackage unittest
 * @copyright  &copy; 2006 The Open University
 * @author     N.D.Freear@open.ac.uk, T.J.Hunt@open.ac.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_OUTPUT_BUFFERING', true);

require(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('simpletestlib.php');
require_once('simpletestcoveragelib.php');
require_once('ex_simple_test.php');
require_once('ex_reporter.php');

// Always run the unit tests in developer debug mode.
$CFG->debug = DEBUG_DEVELOPER;
error_reporting($CFG->debug);
raise_memory_limit(MEMORY_EXTRA);

// page parameters
$path         = optional_param('path', null, PARAM_PATH);
$showpasses   = optional_param('showpasses', false, PARAM_BOOL);
$codecoverage = optional_param('codecoverage', false, PARAM_BOOL);
$showsearch   = optional_param('showsearch', false, PARAM_BOOL);

admin_externalpage_setup('toolsimpletest', '', array('showpasses'=>$showpasses, 'showsearch'=>$showsearch));

$unittest = true;

global $UNITTEST;
$UNITTEST = new stdClass();

// This limit is the time allowed per individual test function. Please do not
// increase this value. If you get a PHP time limit when running unit tests,
// find the unit test which is running slowly, and either make it faster,
// split it into multiple tests, or call set_time_limit within that test.
define('TIME_ALLOWED_PER_UNIT_TEST', 60);

// Print the header.
$strtitle = get_string('unittests', 'tool_unittest');

if (!is_null($path)) {
    //trim so user doesn't get an error if they include a space on the end of the path (ie by pasting path)
    $path = trim($path);

    // Turn off xmlstrictheaders during the unit test run.
    $origxmlstrictheaders = !empty($CFG->xmlstrictheaders);
    $CFG->xmlstrictheaders = false;
    echo $OUTPUT->header();
    $CFG->xmlstrictheaders = $origxmlstrictheaders;
    unset($origxmlstrictheaders);

    // Create the group of tests.
    $test = new autogroup_test_coverage($showsearch, true, $codecoverage, 'Moodle Unit Tests Code Coverage Report', 'unittest');

    // OU specific. We use the _nonproject folder for stuff we want to
    // keep in CVS, but which is not really relevant. It does no harm
    // to leave this here.
    $test->addIgnoreFolder($CFG->dirroot . '/_nonproject');

    // Make the reporter, which is what displays the results.
    $reporter = new ExHtmlReporter($showpasses);

    if ($showsearch) {
        echo $OUTPUT->heading('Searching for test cases');
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
        echo $OUTPUT->box(get_string('pathdoesnotexist', 'tool_unittest', $path), 'errorbox');
        $ok = false;
    }

    // If we have something to test, do it.
    if ($ok) {
        if ($path == $CFG->dirroot) {
            $title = get_string('moodleunittests', 'tool_unittest', get_string('all', 'tool_unittest'));
        } else {
            $title = get_string('moodleunittests', 'tool_unittest', $displaypath);
        }
        echo $OUTPUT->heading($title);
        $test->run($reporter);
    }

    $formheader = get_string('retest', 'tool_unittest');
} else {
    $displaypath = '';
    echo $OUTPUT->header();
    $formheader = get_string('rununittests', 'tool_unittest');
}
// Print the form for adjusting options.
echo $OUTPUT->box_start('generalbox boxwidthwide boxaligncenter');
echo $OUTPUT->heading($formheader);
echo '<form method="get" action="index.php">';
echo '<fieldset class="invisiblefieldset">';
echo '<p>'.html_writer::checkbox('showpasses', 1, $showpasses, get_string('showpasses', 'tool_unittest')).'</p>';
echo '<p>'.html_writer::checkbox('showsearch', 1, $showsearch, get_string('showsearch', 'tool_unittest')).'</p>';
if (moodle_coverage_recorder::can_run_codecoverage()) {
    echo '<p>'.html_writer::checkbox('codecoverage', 1, $codecoverage, get_string('codecoverageanalysis', 'tool_unittest')).'</p>';
} else {
    echo '<p>'; print_string('codecoveragedisabled', 'tool_unittest'); echo '<input type="hidden" name="codecoverage" value="0" /></p>';
}
echo '<p>';
    echo '<label for="path">', get_string('onlytest', 'tool_unittest'), '</label> ';
    echo '<input type="text" id="path" name="path" value="', $displaypath, '" size="40" />';
echo '</p>';
echo '<input type="submit" value="' . get_string('runtests', 'tool_unittest') . '" />';
echo '</fieldset>';
echo '</form>';
echo $OUTPUT->box_end();

$otherpages = array();
$otherpages['PDF lib test'] = new moodle_url('/admin/tool/unittest/other/pdflibtestpage.php');
if (debugging('', DEBUG_DEVELOPER)) {
    $otherpages['TODO checker'] = new moodle_url('/admin/tool/unittest/other/todochecker.php');
}

// print list of extra test pages that are not simpletests,
// not everything there is good enough to show to our users
if ($otherpages) {
    echo $OUTPUT->box_start('generalbox boxwidthwide boxaligncenter');
    echo $OUTPUT->heading(get_string('othertestpages', 'tool_unittest'));
    echo '<ul>';
    foreach ($otherpages as $name=>$url) {
        echo '<li>'.html_writer::link($url, $name).'</li>';
    }
    echo '</ul>';
    echo $OUTPUT->box_end();
}


// Print link to latest code coverage for this report type
if (is_null($path) || !$codecoverage) {
    moodle_coverage_reporter::print_link_to_latest('unittest');
}

// Footer.
echo $OUTPUT->footer();
