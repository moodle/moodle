<?php  // $Id$
/**
 * Run database functional tests.
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

$showpasses = optional_param('showpasses', 0, PARAM_BOOL);
$selected   = optional_param('selected', array(), PARAM_INT);

global $UNITTEST;
$UNITTEST = new object();

if (!data_submitted()) {
    $selected = array();
    for ($i=0; $i<=10; $i++) {
        $selected[$i] = 1;
    }
}

// Print the header.
admin_externalpage_setup('reportdbtest');
admin_externalpage_print_header();

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
    list($library, $driver, $dbhost, $dbuser, $dbpass, $dbname, $dbpersist, $prefix, $dboptions) = $CFG->$name;
    $dbinfos[$i] = array('name'=>"External database $i ($library/$driver/$dbhost/$dbname/$prefix)", 'installed'=>false, 'configured'=>false);

    $classname = "{$driver}_{$library}_moodle_database";
    require_once("$CFG->libdir/dml/$classname.php");
    $d = new $classname();
    if (!$d->driver_installed()) {
        continue;
    }
    $dbinfos[$i]['installed'] = true;

    if ($d->connect($dbhost, $dbuser, $dbpass, $dbname, $dbpersist, $prefix, $dboptions)) {
        $dbinfos[$i]['configured'] = true;
        if (data_submitted() and !empty($selected[$i])) {
            $tests[$i] = $d;
        } else {
            $d->dispose();
        }
    }
}

if (!empty($tests)) {
    @ob_implicit_flush(true);
    while(@ob_end_flush());

    foreach ($tests as $i=>$database) {
        $dbinfo = $dbinfos[$i];

        $UNITTEST->func_test_db = $database; // pass the db to the tests through global

        print_heading('Running tests on: '.$dbinfo['name'], '', 3); // TODO: localise

        // Create the group of tests.
        $test = new AutoGroupTest(false, true);

        $test->addTestFile($CFG->libdir.'/dml/simpletest/testdml.php');
        $test->addTestFile($CFG->libdir.'/ddl/simpletest/testddl.php');

        // Look for DB-specific tests (testing sql_ helper functions)
        $dbfilename = $CFG->libdir.'/dml/simpletest/test_'.get_class($database).'.php';
        if (file_exists($dbfilename)) {
            $test->addTestFile($dbfilename);
        }

        // Make the reporter, which is what displays the results.
        $reporter = new ExHtmlReporter($showpasses);

        set_time_limit(300);
        $test->run($reporter);

        unset($UNITTEST->func_test_db);

        echo '<hr />';
    }

}

// Print the form for adjusting options.
print_simple_box_start('center', '70%');
echo '<form method="post" action="dbtest.php">';
echo '<div>';
print_heading("Run functional database tests"); // TODO: localise
echo '<p>'; print_checkbox('showpasses', 1, $showpasses, get_string('showpasses', 'simpletest')); echo '</p>';
echo '<p><strong>'."Databases:".'</strong>';
echo '<ul>';
foreach ($dbinfos as $i=>$dbinfo) {
    $name = $dbinfo['name'];
    if ($dbinfo['installed']) {
        if (!$dbinfo['configured']) {
            $name = "$name (misconfigured)"; // TODO: localise
        }
        echo '<li>'; print_checkbox('selected['.$i.']', 1, intval(!empty($selected[$i])), $name); echo '</li>';
    } else {
        echo '<li>'."$name: driver not installed".'</li'; // TODO: localise
    }
}
echo '</ul></p>';
echo '<p>External databases are configured in config.php, add lines:
<pre>
$CFG->func_test_db_1 = array("adodb", "postgres7", "localhost", "moodleuser", "password", "moodle", false, "test", null);
$CFG->func_test_db_2 = array("adodb", "mssql", "localhost", "moodleuser", "password", "moodle", false, "test", null);
</pre>
where order of parameters is: dblibrary, dbtype, dbhost, dbuser, dbpass, dbname, dbpersist, prefix, dboptions
</p>';
echo '<p><input type="submit" value="' . get_string('runtests', 'simpletest') . '" /></p>';
echo '</div>';
echo '</form>';
print_simple_box_end();

// Footer.
admin_externalpage_print_footer();

?>
