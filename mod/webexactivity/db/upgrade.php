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
 * An activity to interface with WebEx.
 *
 * @package    mod_webexactvity
 * @author     Eric Merrill <merrill@oakland.edu>
 * @copyright  2014 Oakland University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Runs the upgrade between versions.
 *
 * @param int      $oldversion Version we are starting from.
 * @return bool    True on success, false on failure.
 */
function xmldb_webexactivity_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Moodle v2.6.0 release upgrade line.
    // Put any upgrade step following this.
    if ($oldversion < 2014022500) {
        echo "mod_webexactivity must be upgraded to at version 2014022500 before continuing.";
        return false;
    }

    if ($oldversion < 2014030300) {

        // Define field manual to be added to webexactivity_user.
        $table = new xmldb_table('webexactivity_user');
        $field = new xmldb_field('manual', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'webexuserid');

        // Conditionally launch add field manual.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // WebEx Activity savepoint reached.
        upgrade_mod_savepoint(true, 2014030300, 'webexactivity');
    }

    if ($oldversion < 2014030602) {

        // Define field hostwebexid to be added to webexactivity.
        $table = new xmldb_table('webexactivity');
        $field = new xmldb_field('hostwebexid', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'creatorwebexid');

        // Conditionally launch add field hostwebexid.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $sql = 'UPDATE {webexactivity} SET hostwebexid = creatorwebexid WHERE hostwebexid IS NULL';
        $DB->execute($sql);

        // WebEx Activity savepoint reached.
        upgrade_mod_savepoint(true, 2014030602, 'webexactivity');
    }

    if ($oldversion < 2014031000) {
        // WebEx Activity savepoint reached.
        upgrade_mod_savepoint(true, 2014031000, 'webexactivity');
    }

    if ($oldversion < 2014032602) {
        // Reducing the length of a number of char fields.
        // Webexactivity table.
        $table = new xmldb_table('webexactivity');
        $field = new xmldb_field('creatorwebexid', XMLDB_TYPE_CHAR, '64', null, null, null, null, 'introformat');
        // Launch change of precision for field creatorwebexid.
        $dbman->change_field_precision($table, $field);

        $field = new xmldb_field('hostwebexid', XMLDB_TYPE_CHAR, '64', null, null, null, null, 'creatorwebexid');
        // Launch change of precision for field hostwebexid.
        $dbman->change_field_precision($table, $field);

        // Webexactivity_user table.
        $table = new xmldb_table('webexactivity_user');
        $field = new xmldb_field('webexid', XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL, null, null, 'manual');
        // Launch change of precision for field webexid.
        $dbman->change_field_precision($table, $field);

        $field = new xmldb_field('firstname', XMLDB_TYPE_CHAR, '64', null, null, null, null, 'password');
        // Launch change of precision for field firstname.
        $dbman->change_field_precision($table, $field);

        $field = new xmldb_field('lastname', XMLDB_TYPE_CHAR, '64', null, null, null, null, 'firstname');
        // Launch change of precision for field lastname.
        $dbman->change_field_precision($table, $field);

        $field = new xmldb_field('email', XMLDB_TYPE_CHAR, '64', null, null, null, null, 'lastname');
        // Launch change of precision for field email.
        $dbman->change_field_precision($table, $field);

        // Webexactivity_recording table.
        $table = new xmldb_table('webexactivity_recording');
        $field = new xmldb_field('hostid', XMLDB_TYPE_CHAR, '64', null, XMLDB_NOTNULL, null, null, 'recordingid');
        // Launch change of precision for field hostid.
        $dbman->change_field_precision($table, $field);

        // Dropping unneeded fields.
        // Define field hosts to be dropped from webexactivity.
        $table = new xmldb_table('webexactivity');
        $field = new xmldb_field('hosts');
        // Conditionally launch drop field hosts.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // WebEx Activity savepoint reached.
        upgrade_mod_savepoint(true, 2014032602, 'webexactivity');
    }

    if ($oldversion < 2014042701) {
        // Define field password to be added to webexactivity.
        $table = new xmldb_table('webexactivity');
        $field = new xmldb_field('password', XMLDB_TYPE_CHAR, '16', null, null, null, null, 'hostkey');

        // Conditionally launch add field password.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // WebEx Activity savepoint reached.
        upgrade_mod_savepoint(true, 2014042701, 'webexactivity');
    }

    if ($oldversion < 2014043002) {
        $sitename = get_config('webexactivity', 'url');
        if ($sitename) {
            set_config('sitename', $sitename, 'webexactivity');
            unset_config('url', 'webexactivity');
        }

        // WebEx Activity savepoint reached.
        upgrade_mod_savepoint(true, 2014043002, 'webexactivity');
    }

    if ($oldversion < 2016020402) {
        unset_config('loadedallrecordingstime', 'webexactivity');
        unset_config('loadedpastrecordingstime', 'webexactivity');

        // WebEx Activity savepoint reached.
        upgrade_mod_savepoint(true, 2016020402, 'webexactivity');
    }

    if ($oldversion < 2019051300) {

        // Define field meetinglink to be added to webexactivity.
        $table = new xmldb_table('webexactivity');
        $field = new xmldb_field('meetinglink', XMLDB_TYPE_TEXT, null, null, null, null, null, 'password');

        // Conditionally launch add field meetinglink.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Webexactivity savepoint reached.
        upgrade_mod_savepoint(true, 2019051300, 'webexactivity');
    }

    if ($oldversion < 2019082700) {
        // Define field calpublish to be added to webexactivity.
        $table = new xmldb_table('webexactivity');
        $field = new xmldb_field('calpublish', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'duration');

        // Conditionally launch add field calpublish.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Set extended availability meetings to not calender publish.
        $DB->set_field_select('webexactivity', 'calpublish', 0, 'endtime IS NOT NULL');

        // Webexactivity savepoint reached.
        upgrade_mod_savepoint(true, 2019082700, 'webexactivity');
    }

    if ($oldversion < 2019082701) {
        // Create adhoc task to create the calendar events.
        // Doing it this way should avoid problems with calling the manager during upgrades.
        $record = new \stdClass();
        $record->classname = '\mod_webexactivity\task\upgrade_calendars';
        $record->component = 'mod_webexactivity';

        // Next run time based from nextruntime computation in \core\task\manager::queue_adhoc_task().
        $nextruntime = time() - 1;
        $record->nextruntime = $nextruntime;
        $record->customdata = json_encode('core_course-mycourse');

        $DB->insert_record('task_adhoc', $record);

        // Webexactivity savepoint reached.
        upgrade_mod_savepoint(true, 2019082701, 'webexactivity');
    }


    return true;
}
