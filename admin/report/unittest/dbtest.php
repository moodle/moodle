<?php
/**
 * Run database functional tests.
 * @package SimpleTestEx
 */

define('NO_OUTPUT_BUFFERING', true);

require_once(dirname(__FILE__).'/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/simpletestcoveragelib.php');
require_once('ex_simple_test.php');
require_once('ex_reporter.php');

$showpasses   = optional_param('showpasses', false, PARAM_BOOL);
$codecoverage = optional_param('codecoverage', false, PARAM_BOOL);
$selected     = optional_param('selected', array(), PARAM_INT);

// Print the header and check access.
admin_externalpage_setup('reportdbtest');
echo $OUTPUT->header();

global $UNITTEST;
$UNITTEST = new stdClass();

if (!data_submitted()) {
    $selected = array();
    for ($i=0; $i<=10; $i++) {
        $selected[$i] = 1;
    }
}


$dbinfos     = array();
$tests       = array();

$dbinfos[0]     = array('name'=>"Current database ($CFG->dblibrary/$CFG->dbtype)", 'installed'=>true, 'configured'=>true); // TODO: localise
if (data_submitted() and !empty($selected[0])) {
    $tests[0] = $DB;
}

for ($i=1; $i<=10; $i++) {
    $name = 'func_test_db_'.$i;
    if (!isset($CFG->$name)) {
        continue;
    }
    list($library, $driver, $dbhost, $dbuser, $dbpass, $dbname, $prefix, $dboptions) = $CFG->$name;
    $dbinfos[$i] = array('name'=>"External database $i ($library/$driver/$dbhost/$dbname/$prefix)", 'installed'=>false, 'configured'=>false);

    $classname = "{$driver}_{$library}_moodle_database";
    require_once("$CFG->libdir/dml/$classname.php");
    $d = new $classname();
    if (!$d->driver_installed()) {
        continue;
    }
    $dbinfos[$i]['installed'] = true;

    try {
        $d->connect($dbhost, $dbuser, $dbpass, $dbname, $prefix, $dboptions);
        $dbinfos[$i]['configured'] = true;
        if (data_submitted() and !empty($selected[$i])) {
            $tests[$i] = $d;
        } else {
            $d->dispose();
        }
    } catch (dml_connection_exception $e) {
        $dbinfos[$i]['configured'] = false;
    }
}

if (!empty($tests)) {
    $covreporter = new moodle_coverage_reporter('Functional DB Tests Code Coverage Report', 'dbtest');
    $covrecorder = new moodle_coverage_recorder($covreporter);

    foreach ($tests as $i=>$database) {
        $dbinfo = $dbinfos[$i];

        $UNITTEST->func_test_db = $database; // pass the db to the tests through global

        echo $OUTPUT->heading('Running tests on: '.$dbinfo['name'], 3); // TODO: localise

        // Create the group of tests.
        $test = new autogroup_test_coverage(false, true, $codecoverage);


        $test->addTestFile($CFG->libdir.'/dml/simpletest/testdml.php');
        $test->addTestFile($CFG->libdir.'/ddl/simpletest/testddl.php');

        // Make the reporter, which is what displays the results.
        $reporter = new ExHtmlReporter($showpasses);

        set_time_limit(300); // 5 mins per DB should be enough
        $test->run_with_external_coverage($reporter, $covrecorder);

        unset($UNITTEST->func_test_db);

        echo '<hr />';
    }
    if ($codecoverage) {
        $covrecorder->generate_report();
        moodle_coverage_reporter::print_summary_info('dbtest');
    }

}

// Print the form for adjusting options.
echo $OUTPUT->box_start('generalbox boxwidthwide boxaligncenter');
echo '<form method="post" action="dbtest.php">';
echo '<div>';
echo $OUTPUT->heading("Run functional database tests"); // TODO: localise
echo '<p>'.html_writer::checkbox('showpasses', 1, $showpasses, get_string('showpasses', 'simpletest')).'</p>';
if (moodle_coverage_recorder::can_run_codecoverage()) {
    echo '<p>'.html_writer::checkbox('codecoverage', 1, $codecoverage, get_string('codecoverageanalysis', 'simpletest')).'</p>';
} else {
    echo '<p>'; print_string('codecoveragedisabled', 'simpletest'); echo '<input type="hidden" name="codecoverage" value="0" /></p>';
}
echo '<p><strong>'."Databases:".'</strong></p>';
echo '<ul>';
foreach ($dbinfos as $i=>$dbinfo) {
    $name = $dbinfo['name'];
    if ($dbinfo['installed']) {
        if (!$dbinfo['configured']) {
            $name = "$name (misconfigured)"; // TODO: localise
        }
        echo '<li>'.html_writer::checkbox('selected['.$i.']', 1, intval(!empty($selected[$i])), $name).'</li>';
    } else {
        echo '<li>'."$name: driver not installed".'</li>'; // TODO: localise
    }
}
echo '</ul>';
echo '<p>External databases are configured in config.php, add lines:</p>
<pre>
$CFG->func_test_db_1 = array("native", "pgsql", "localhost", "moodleuser", "password", "moodle", "test", null);
$CFG->func_test_db_2 = array("native", "mssql", "localhost", "moodleuser", "password", "moodle", "test", null);
</pre>
<p>where order of parameters is: dblibrary, dbtype, dbhost, dbuser, dbpass, dbname, prefix, dboptions</p>';
echo '<p><input type="submit" value="' . get_string('runtests', 'simpletest') . '" /></p>';
echo '</div>';
echo '</form>';
echo $OUTPUT->box_end();

// Print link to latest code coverage for this report type
if (!data_submitted() || !$codecoverage) {
    moodle_coverage_reporter::print_link_to_latest('dbtest');
}

// Footer.
echo $OUTPUT->footer();
