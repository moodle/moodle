<?php
/**
 * Run database functional tests.
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

require_login();
require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

// CGI arguments
$showpasses = optional_param('showpasses', 0, PARAM_BOOL);
$dbinstance = optional_param('dbinstance', -1, PARAM_INT);

$langfile = 'simpletest';

// Print the header.
admin_externalpage_setup('reportdbtest');
$strtitle = get_string('unittests', $langfile);
admin_externalpage_print_header();

$dbinstances = array();
$dbinstances[0] = $DB;

for ($i=1; $i<=10; $i++) {
    $name = 'ext_test_db_'.$i;
    if (!isset($CFG->$name)) {
        continue;
    }
    list($library, $driver, $dbhost, $dbuser, $dbpass, $dbname, $dbpersist, $prefix, $dboptions) = $CFG->$name;

    $classname = "{$driver}_{$library}_moodle_database";
    require_once("$CFG->libdir/dml/$classname.php");
    $d = new $classname();
    if (!$d->driver_installed()) {
        continue;
    }

    if ($d->connect($dbhost, $dbuser, $dbpass, $dbname, $dbpersist, $prefix, $dboptions)) {
        $dbinstances[$i] = $d;
    }
}

if (!isset($dbinstances[$dbinstance])) {
    $dbinstance = -1;
} else {
    global $EXT_TEST_DB;
    $EXT_TEST_DB = $dbinstances[$dbinstance];
}

if ($dbinstance >= 0) {

    // Create the group of tests.
    $test =& new AutoGroupTest(false, true);

    // Make the reporter, which is what displays the results.
    $reporter = new ExHtmlReporter($showpasses);

    $test->addTestFile($CFG->libdir . '/dml/simpletest/testdmllib.php');
    $test->addTestFile($CFG->libdir . '/ddl/simpletest/testddllib.php');

    // If we have something to test, do it.
    print_heading(get_string('moodleunittests', $langfile, get_string('all', $langfile)));

    /* The UNITTEST constant can be checked elsewhere if you need to know
     * when your code is being run as part of a unit test. */
    define('UNITTEST', true);
    $test->run($reporter);

    $formheader = get_string('retest', $langfile);

} else {
    $formheader = get_string('rununittests', $langfile);
}

// Print the form for adjusting options.
print_simple_box_start('center', '70%');
echo '<form method="get" action="dbtest.php">';
echo '<fieldset class="invisiblefieldset">';
print_heading($formheader);
echo '<p>'; print_checkbox('showpasses', 1, $showpasses, get_string('showpasses', $langfile)); echo '</p>';
echo '<input type="hidden" value="0" name="dbinstance" />';
echo '<input type="submit" value="' . get_string('runtests', $langfile) . '" />';
echo '</fieldset>';
echo '</form>';
print_simple_box_end();

// Footer.
admin_externalpage_print_footer();

?>
