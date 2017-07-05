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
 * Filter form.
 *
 * @package    logstore_database
 * @copyright  2014 onwards Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../../../config.php');
require_once($CFG->dirroot . '/lib/adminlib.php');

require_login();
$context = context_system::instance();
require_capability('moodle/site:config', $context);
require_sesskey();

navigation_node::override_active_url(new moodle_url('/admin/settings.php', array('section' => 'logsettingdatabase')));
admin_externalpage_setup('logstoredbtestsettings');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('testingsettings', 'logstore_database'));

// NOTE: this is not localised intentionally, admins are supposed to understand English at least a bit...

raise_memory_limit(MEMORY_HUGE);
$dbtable = get_config('logstore_database', 'dbtable');
if (empty($dbtable)) {
    echo $OUTPUT->notification('External table not specified.', 'notifyproblem');
    die();
}

$dbdriver = get_config('logstore_database', 'dbdriver');
list($dblibrary, $dbtype) = explode('/', $dbdriver);
if (!$db = \moodle_database::get_driver_instance($dbtype, $dblibrary, true)) {
    echo $OUTPUT->notification("Unknown driver $dblibrary/$dbtype", "notifyproblem");
    die();
}

$olddebug = $CFG->debug;
$olddisplay = ini_get('display_errors');
ini_set('display_errors', '1');
$CFG->debug = DEBUG_DEVELOPER;
error_reporting($CFG->debug);

$dboptions = array();
$dboptions['dbpersist'] = get_config('logstore_database', 'dbpersist');
$dboptions['dbsocket'] = get_config('logstore_database', 'dbsocket');
$dboptions['dbport'] = get_config('logstore_database', 'dbport');
$dboptions['dbschema'] = get_config('logstore_database', 'dbschema');
$dboptions['dbcollation'] = get_config('logstore_database', 'dbcollation');
$dboptions['dbhandlesoptions'] = get_config('logstore_database', 'dbhandlesoptions');

try {
    $db->connect(get_config('logstore_database', 'dbhost'), get_config('logstore_database', 'dbuser'),
        get_config('logstore_database', 'dbpass'), get_config('logstore_database', 'dbname'), false, $dboptions);
} catch (\moodle_exception $e) {
    echo $OUTPUT->notification('Cannot connect to the database.', 'notifyproblem');
    $CFG->debug = $olddebug;
    ini_set('display_errors', $olddisplay);
    error_reporting($CFG->debug);
    ob_end_flush();
    echo $OUTPUT->footer();
    die();
}
echo $OUTPUT->notification('Connection made.', 'notifysuccess');
$tables = $db->get_tables();
if (!in_array($dbtable, $tables)) {
    echo $OUTPUT->notification('Cannot find the specified table ' . $dbtable, 'notifyproblem');
    $CFG->debug = $olddebug;
    ini_set('display_errors', $olddisplay);
    error_reporting($CFG->debug);
    ob_end_flush();
    echo $OUTPUT->footer();
    die();
}
echo $OUTPUT->notification('Table ' . $dbtable . ' found.', 'notifysuccess');

$cols = $db->get_columns($dbtable);
if (empty($cols)) {
    echo $OUTPUT->notification('Can not read external table.', 'notifyproblem');
} else {
    $columns = array_keys((array)$cols);
    echo $OUTPUT->notification('External table contains following columns:<br />' . implode(', ', $columns), 'notifysuccess');
}

$db->dispose();

$CFG->debug = $olddebug;
ini_set('display_errors', $olddisplay);
error_reporting($CFG->debug);
ob_end_flush();
echo $OUTPUT->footer();
