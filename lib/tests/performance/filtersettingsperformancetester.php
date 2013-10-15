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
 * A simple test script that sets up test data in the database then
 * measures performance of filter_get_active_in_context.
 *
 * @copyright  2009 Tim Hunt
 * @author     N.D.Freear@open.ac.uk, T.J.Hunt@open.ac.uk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

die(); //TODO: this needs to be rewritten as standard advanced_testcase

require(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir . '/ddllib.php');

require_login();
$syscontext = context_system::instance();
require_capability('moodle/site:config', $syscontext);

$baseurl = new moodle_url('/lib/tests/performance/filtersettingsperformancetester.php');

$title = 'filter_get_active_in_context performance test';
$PAGE->set_url($baseurl);
$PAGE->set_context($syscontext);
$PAGE->navbar->add($title);
$PAGE->set_title($title);
$PAGE->set_heading($title);
echo $OUTPUT->header();

// Complain if we get this far and $CFG->unittestprefix is not set.
if (empty($CFG->unittestprefix)) {
    throw new coding_exception('This page requires $CFG->unittestprefix to be set in config.php.');
}

$requiredtables = array('context', 'filter_active', 'filter_config');
$realdb = $DB;
$testdb = moodle_database::get_driver_instance($CFG->dbtype, $CFG->dblibrary);
$testdb->connect($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname, $CFG->unittestprefix);
$DB = $testdb;
$dbman = $testdb->get_manager();
$issetup = 0;
foreach ($requiredtables as $table) {
    if ($dbman->table_exists(new xmldb_table($table))) {
        $issetup++;
    }
}

switch (optional_param('action', '', PARAM_ALPHANUMEXT)) {
    case 'setup':
        require_sesskey();
        if ($issetup == 0) {
            foreach ($requiredtables as $table) {
                $dbman->install_one_table_from_xmldb_file($CFG->dirroot . '/lib/db/install.xml', $table);
                $issetup++;
            }
            flush();
            populate_test_database($syscontext, 10, 100, 1000, 5000, 5000);
            echo $OUTPUT->notification('Test tables created.', 'notifysuccess');
        } else if ($issetup == count($requiredtables)) {
            echo $OUTPUT->notification('Test tables are already set up.', 'notifysuccess');
        } else {
            echo $OUTPUT->notification('Something is wrong, please delete the test tables and try again.');
        }
        break;

    case 'teardown':
        require_sesskey();
        foreach ($requiredtables as $tablename) {
            $table = new xmldb_table($tablename);
            if ($dbman->table_exists($table)) {
                $dbman->drop_table($table);
            }
        }
        $issetup = 0;
        echo $OUTPUT->notification('Test tables dropped.', 'notifysuccess');
        break;

    case 'test':
        require_sesskey();
        if ($issetup != count($requiredtables)) {
            echo $OUTPUT->notification('Something is wrong, please delete the test tables and try again.');
        } else {
            $contexts = $DB->get_records('context');
            $numcalls = 1000;
            $basetime = run_tests('noop', $contexts, $numcalls, 0);
            run_tests('simple_get_record_by_id', $contexts, $numcalls, $basetime);
            run_tests('filter_get_active_in_context', $contexts, $numcalls, $basetime);
        }
        break;
}

if ($issetup == count($requiredtables)) {
    echo '<p>Total of ' . $DB->count_records('context') . ' contexts, ' .
            $DB->count_records('filter_active') . ' filter_active and ' .
            $DB->count_records('filter_config') . ' filter_config rows in the database.</p>';
}

$DB = $realdb;

echo $OUTPUT->container_start();

$aurl = new moodle_url($baseurl, array('action' => 'setup', 'sesskey'=>sesskey()));
echo $OUTPUT->single_button($aurl, 'Set up test tables', 'get', array('disabled'=>($issetup > 0)));

$aurl = new moodle_url($baseurl, array('action' => 'teardown', 'sesskey'=>sesskey()));
echo $OUTPUT->single_button($aurl, 'Drop test tables', 'get', array('disabled'=>($issetup == 0)));

$aurl = new moodle_url($baseurl, array('action' => 'test', 'sesskey'=>sesskey()));
echo $OUTPUT->single_button($aurl, 'Run tests', 'get', array('disabled'=>($issetup != count($requiredtables))));

echo $OUTPUT->container_end();

echo $OUTPUT->footer();

function noop($context) {
}

function simple_get_record_by_id($context) {
    global $DB;
    $DB->get_record('context', array('id' => $context->id));
}

function run_tests($function, $contexts, $numcalls, $basetime) {
    core_php_time_limit::raise(120);
    $startime = microtime(true);
    for ($j = 0; $j < $numcalls; $j++) {
        $function($contexts[array_rand($contexts)]);
    }
    $duration = microtime(true) - $startime;
    print_result_line($duration, $basetime, $numcalls, 'calls to ' . $function);
    return $duration;
}

function print_result_line($duration, $basetime, $numcalls, $action1, $action2 = 'calls per second') {
    echo '<p>Time for ' . format_float($numcalls, 0) . ' ' . $action1 . ': <b>' .
            format_float($duration - $basetime, 3) . 's</b> (' . format_float($duration, 3) . ' - ' .
            format_float($basetime, 3) . 's) which is ' .
            format_float(($numcalls / ($duration - $basetime)), 0) . ' ' . $action2 . ".</p>\n";
    flush();
}

function populate_test_database($syscontext, $numcategories, $numcourses, $nummodules, $numoverrides, $numconfigs) {
    global $DB, $OUTPUT;
    core_php_time_limit::raise(600);
    $syscontext->id = $DB->insert_record('context', $syscontext);

    // Category contexts.
    $categoryparents = array($syscontext);
    $categories = array();
    for ($i = 0; $i < $numcategories; $i++) {
        $context = insert_context(CONTEXT_COURSECAT, $i, $categoryparents[array_rand($categoryparents)]);
        $categoryparents[] = $context;
        $categories[$context->id] = $context;
    }
    echo $OUTPUT->notification('Created ' . $numcategories . ' course category contexts.', 'notifysuccess'); flush();

    // Course contexts.
    $courses = array();
    for ($i = 0; $i < $numcourses; $i++) {
        $context = insert_context(CONTEXT_COURSE, $i, $categories[array_rand($categories)]);
        $courses[$context->id] = $context;
    }
    echo $OUTPUT->notification('Created ' . $numcourses . ' course contexts.', 'notifysuccess'); flush();

    // Activities contexts.
    $mods = array();
    $prog = new progress_bar('modbar', 500, true);
    $transaction = $DB->start_delegated_transaction();
    for ($i = 0; $i < $nummodules; $i++) {
        $context = insert_context(CONTEXT_MODULE, $i, $courses[array_rand($courses)]);
        $mods[$context->id] = $context;
        if ($i % 50) {
            $prog->update($i, $nummodules, '');
        }
    }
    $transaction->allow_commit();
    echo $OUTPUT->notification('Created ' . $nummodules . ' module contexts.', 'notifysuccess'); flush();

    $contexts = $categories + $courses + $mods;

    // Global settings.
    $installedfilters = filter_get_all_installed();
    $counts = array(TEXTFILTER_DISABLED => 0, TEXTFILTER_OFF => 0, TEXTFILTER_ON => 0);
    foreach ($installedfilters as $filter => $notused) {
        $state = array_rand($counts);
        filter_set_global_state($filter, $state);
        $counts[$state]++;
    }
    echo $OUTPUT->notification('Set global setting: ' . $counts[TEXTFILTER_DISABLED] . ' disabled, ' .
            $counts[TEXTFILTER_OFF] . ' off and ' . $counts[TEXTFILTER_ON] . ' on.', 'notifysuccess'); flush();

    // Local overrides.
    $localstates = array(TEXTFILTER_OFF => 0, TEXTFILTER_ON => 0);
    $prog = new progress_bar('locbar', 500, true);
    $transaction = $DB->start_delegated_transaction();
    for ($i = 0; $i < $numoverrides; $i++) {
        filter_set_local_state(array_rand($installedfilters), array_rand($contexts), array_rand($localstates));
        if ($i % 50) {
            $prog->update($i, $numoverrides, '');
        }
    }
    $transaction->allow_commit();
    echo $OUTPUT->notification('Set ' . $numoverrides . ' local overrides.', 'notifysuccess'); flush();

    // Local config.
    $variablenames = array('frog' => 0, 'toad' => 0, 'elver' => 0, 'eft' => 0, 'tadpole' => 0);
    $prog = new progress_bar('confbar', 500, true);
    $transaction = $DB->start_delegated_transaction();
    for ($i = 0; $i < $numconfigs; $i++) {
        filter_set_local_config(array_rand($installedfilters), array_rand($contexts),
                array_rand($variablenames), random_string(rand(20, 40)));
        if ($i % 50) {
            $prog->update($i, $numconfigs, '');
        }
    }
    $transaction->allow_commit();
    echo $OUTPUT->notification('Set ' . $numconfigs . ' local configs.', 'notifysuccess'); flush();
}

function insert_context($contextlevel, $instanceid, $parent) {
    global $DB;
    $context = new stdClass;
    $context->contextlevel = $contextlevel;
    $context->instanceid = $instanceid;
    $context->depth = $parent->depth + 1;
    $context->id = $DB->insert_record('context', $context);
    $context->path = $parent->path . '/' . $context->id;
    $DB->set_field('context', 'path', $context->path, array('id' => $context->id));
    return $context;
}

