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
 * upgrade processes for this module.
 *
 * @package   mod_attendance
 * @copyright 2011 Artem Andreev <andreev.artem@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
require_once(dirname(__FILE__) . '/upgradelib.php');

/**
 * upgrade this attendance instance - this function could be skipped but it will be needed later
 * @param int $oldversion The old version of the attendance module
 * @return bool
 */
function xmldb_attendance_upgrade($oldversion=0) {

    global $DB;
    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    $result = true;

    if ($oldversion < 2014112000) {
        $table = new xmldb_table('attendance_sessions');

        $field = new xmldb_field('studentscanmark');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2014112000, 'attendance');
    }

    if ($oldversion < 2014112001) {
        // Replace values that reference old module "attforblock" to "attendance".
        $sql = "UPDATE {grade_items}
                   SET itemmodule = 'attendance'
                 WHERE itemmodule = 'attforblock'";

        $DB->execute($sql);

        $sql = "UPDATE {grade_items_history}
                   SET itemmodule = 'attendance'
                 WHERE itemmodule = 'attforblock'";

        $DB->execute($sql);

        /*
         * The user's custom capabilities need to be preserved due to the module renaming.
         * Capabilities with a modifierid = 0 value are installed by default.
         * Only update the user's custom capabilities where modifierid is not zero.
         */
        $sql = $DB->sql_like('capability', '?').' AND modifierid <> 0';
        $rs = $DB->get_recordset_select('role_capabilities', $sql, array('%mod/attforblock%'));
        foreach ($rs as $cap) {
            $renamedcapability = str_replace('mod/attforblock', 'mod/attendance', $cap->capability);
            $exists = $DB->record_exists('role_capabilities', array('roleid' => $cap->roleid, 'capability' => $renamedcapability));
            if (!$exists) {
                $DB->update_record('role_capabilities', array('id' => $cap->id, 'capability' => $renamedcapability));
            }
        }

        // Delete old role capabilities.
        $sql = $DB->sql_like('capability', '?');
        $DB->delete_records_select('role_capabilities', $sql, array('%mod/attforblock%'));

        // Delete old capabilities.
        $DB->delete_records_select('capabilities', 'component = ?', array('mod_attforblock'));

        upgrade_mod_savepoint(true, 2014112001, 'attendance');
    }

    if ($oldversion < 2015040501) {
        // Define table attendance_tempusers to be created.
        $table = new xmldb_table('attendance_tempusers');

        // Adding fields to table attendance_tempusers.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('studentid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('fullname', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('email', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('created', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table attendance_tempusers.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for attendance_tempusers.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Conditionally launch add index courseid.
        $index = new xmldb_index('courseid', XMLDB_INDEX_NOTUNIQUE, array('courseid'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Conditionally launch add index studentid.
        $index = new xmldb_index('studentid', XMLDB_INDEX_UNIQUE, array('studentid'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Attendance savepoint reached.
        upgrade_mod_savepoint(true, 2015040501, 'attendance');
    }

    if ($oldversion < 2015040502) {

        // Define field setnumber to be added to attendance_statuses.
        $table = new xmldb_table('attendance_statuses');
        $field = new xmldb_field('setnumber', XMLDB_TYPE_INTEGER, '5', null, XMLDB_NOTNULL, null, '0', 'deleted');

        // Conditionally launch add field setnumber.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field statusset to be added to attendance_sessions.
        $table = new xmldb_table('attendance_sessions');
        $field = new xmldb_field('statusset', XMLDB_TYPE_INTEGER, '5', null, XMLDB_NOTNULL, null, '0', 'descriptionformat');

        // Conditionally launch add field statusset.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Attendance savepoint reached.
        upgrade_mod_savepoint(true, 2015040502, 'attendance');
    }

    if ($oldversion < 2015040503) {

        // Changing type of field grade on table attendance_statuses to number.
        $table = new xmldb_table('attendance_statuses');
        $field = new xmldb_field('grade', XMLDB_TYPE_NUMBER, '5, 2', null, XMLDB_NOTNULL, null, '0', 'description');

        // Launch change of type for field grade.
        $dbman->change_field_type($table, $field);

        // Attendance savepoint reached.
        upgrade_mod_savepoint(true, 2015040503, 'attendance');
    }

    if ($oldversion < 2016052202) {
        // Adding field to store calendar event ids.
        $table = new xmldb_table('attendance_sessions');
        $field = new xmldb_field('caleventid', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', null);

        // Conditionally launch add field statusset.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Creating events for all existing sessions.
        attendance_upgrade_create_calendar_events();

        // Attendance savepoint reached.
        upgrade_mod_savepoint(true, 2016052202, 'attendance');
    }

    if ($oldversion < 2016082900) {

        // Define field timemodified to be added to attendance.
        $table = new xmldb_table('attendance');
        $field = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'grade');

        // Conditionally launch add field timemodified.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Attendance savepoint reached.
        upgrade_mod_savepoint(true, 2016082900, 'attendance');
    }
    if ($oldversion < 2016112100) {
        $table = new xmldb_table('attendance');
        $newfield = $table->add_field('subnet', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'timemodified');
        if (!$dbman->field_exists($table, $newfield)) {
            $dbman->add_field($table, $newfield);
        }
        upgrade_mod_savepoint(true, 2016112100, 'attendance');
    }

    if ($oldversion < 2016121300) {
        $table = new xmldb_table('attendance');
        $field = new xmldb_field('sessiondetailspos', XMLDB_TYPE_CHAR, '5', null, null, null, 'left', 'subnet');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('showsessiondetails', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1', 'subnet');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2016121300, 'attendance');
    }

    if ($oldversion < 2017020700) {
        // Define field timemodified to be added to attendance.
        $table = new xmldb_table('attendance');

        $fields = [];
        $fields[] = new xmldb_field('intro', XMLDB_TYPE_TEXT, null, null, null, null, null, 'timemodified');
        $fields[] = new xmldb_field('introformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, 0, 'intro');

        // Conditionally launch add field.
        foreach ($fields as $field) {
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }
        }

        // Attendance savepoint reached.
        upgrade_mod_savepoint(true, 2017020700, 'attendance');
    }

    if ($oldversion < 2017042800) {
        $table = new xmldb_table('attendance_sessions');

        $field = new xmldb_field('studentpassword');
        $field->set_attributes(XMLDB_TYPE_CHAR, '50', null, false, null, '', 'studentscanmark');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2017042800, 'attendance');
    }

    if ($oldversion < 2017051101) {

        // Define field studentavailability to be added to attendance_statuses.
        $table = new xmldb_table('attendance_statuses');
        $field = new xmldb_field('studentavailability', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'grade');

        // Conditionally launch add field studentavailability.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Attendance savepoint reached.
        upgrade_mod_savepoint(true, 2017051101, 'attendance');
    }

    if ($oldversion < 2017051103) {
        $table = new xmldb_table('attendance_sessions');
        $newfield = $table->add_field('subnet', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'studentpassword');
        if (!$dbman->field_exists($table, $newfield)) {
            $dbman->add_field($table, $newfield);
        }
        upgrade_mod_savepoint(true, 2017051103, 'attendance');
    }

    if ($oldversion < 2017051104) {
        // The meaning of the subnet in the attendance table has changed - it is now the "default" value - find all existing
        // Attendance with subnet set and set the session subnet for these.
        $attendances = $DB->get_recordset_select('attendance', 'subnet IS NOT NULL');
        foreach ($attendances as $attendance) {
            if (!empty($attendance->subnet)) {
                // Get all sessions for this attendance.
                $sessions = $DB->get_recordset('attendance_sessions', array('attendanceid' => $attendance->id));
                foreach ($sessions as $session) {
                    $session->subnet = $attendance->subnet;
                    $DB->update_record('attendance_sessions', $session);
                }
                $sessions->close();
            }
        }
        $attendances->close();

        upgrade_mod_savepoint(true, 2017051104, 'attendance');
    }

    if ($oldversion < 2017051900) {
        // Define field setunmarked to be added to attendance_statuses.
        $table = new xmldb_table('attendance_statuses');
        $field = new xmldb_field('setunmarked', XMLDB_TYPE_INTEGER, '2', null, null, null, null, 'studentavailability');

        // Conditionally launch add field studentavailability.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Attendance savepoint reached.
        upgrade_mod_savepoint(true, 2017051900, 'attendance');
    }

    if ($oldversion < 2017052201) {
        // Define field setunmarked to be added to attendance_statuses.
        $table = new xmldb_table('attendance_sessions');
        $field = new xmldb_field('automark', XMLDB_TYPE_INTEGER, '1', null, true, null, '0', 'subnet');

        // Conditionally launch add field automark.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('automarkcompleted', XMLDB_TYPE_INTEGER, '1', null, true, null, '0', 'automark');

        // Conditionally launch add field automarkcompleted.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Attendance savepoint reached.
        upgrade_mod_savepoint(true, 2017052201, 'attendance');
    }

    if ($oldversion < 2017060900) {
        // Automark values changed.
        $default = get_config('attendance', 'automark_default');
        if (!empty($default)) { // Change default if set.
            set_config('automark_default', 2, 'attendance');
        }
        // Update any sessions set to use automark = 1.
        $sql = "UPDATE {attendance_sessions} SET automark = 2 WHERE automark = 1";
        $DB->execute($sql);

        // Update automarkcompleted to 2 if already complete.
        $sql = "UPDATE {attendance_sessions} SET automarkcompleted = 2 WHERE automarkcompleted = 1";
        $DB->execute($sql);

        upgrade_mod_savepoint(true, 2017060900, 'attendance');
    }

    if ($oldversion < 2017062000) {

        // Define table attendance_warning_done to be created.
        $table = new xmldb_table('attendance_warning_done');

        // Adding fields to table attendance_warning_done.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('notifyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timesent', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table attendance_warning_done.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table attendance_warning_done.
        $table->add_index('notifyid_userid', XMLDB_INDEX_UNIQUE, array('notifyid', 'userid'));

        // Conditionally launch create table for attendance_warning_done.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Attendance savepoint reached.
        upgrade_mod_savepoint(true, 2017062000, 'attendance');
    }

    if ($oldversion < 2017071305) {

        // Define table attendance_warning to be created.
        $table = new xmldb_table('attendance_warning');

        if (!$dbman->table_exists($table)) {
            // Adding fields to table attendance_warning.
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
            $table->add_field('idnumber', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_field('warningpercent', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_field('warnafter', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
            $table->add_field('emailuser', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null);
            $table->add_field('emailsubject', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
            $table->add_field('emailcontent', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
            $table->add_field('emailcontentformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, null);
            $table->add_field('thirdpartyemails', XMLDB_TYPE_TEXT, null, null, null, null, null);

            // Adding keys to table attendance_warning.
            $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
            $table->add_key('level_id', XMLDB_KEY_UNIQUE, array('idnumber', 'warningpercent', 'warnafter'));

            // Conditionally launch create table for attendance_warning.
            $dbman->create_table($table);

        } else {
            // Key definition is probably incorrect so fix it - drop_key dml function doesn't seem to work.
            $indexes = $DB->get_indexes('attendance_warning');
            foreach ($indexes as $name => $index) {
                if ($DB->get_dbfamily() === 'mysql') {
                    $DB->execute("ALTER TABLE {attendance_warning} DROP INDEX ". $name);
                } else {
                    $DB->execute("DROP INDEX ". $name);
                }
            }
            $index = new xmldb_key('level_id', XMLDB_KEY_UNIQUE, array('idnumber', 'warningpercent', 'warnafter'));
            $dbman->add_key($table, $index);
        }
        // Attendance savepoint reached.
        upgrade_mod_savepoint(true, 2017071305, 'attendance');
    }

    if ($oldversion < 2017071800) {
        // Define field setunmarked to be added to attendance_statuses.
        $table = new xmldb_table('attendance_warning');
        $field = new xmldb_field('maxwarn', XMLDB_TYPE_INTEGER, '10', null, true, null, '1', 'warnafter');

        // Conditionally launch add field automark.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Attendance savepoint reached.
        upgrade_mod_savepoint(true, 2017071800, 'attendance');
    }

    if ($oldversion < 2017071802) {
        // Define field setunmarked to be added to attendance_statuses.
        $table = new xmldb_table('attendance_warning_done');

        $index = new xmldb_index('notifyid_userid', XMLDB_INDEX_UNIQUE, array('notifyid', 'userid'));
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        $index = new xmldb_index('notifyid', XMLDB_INDEX_NOTUNIQUE, array('notifyid', 'userid'));
        $dbman->add_index($table, $index);

        // Attendance savepoint reached.
        upgrade_mod_savepoint(true, 2017071802, 'attendance');
    }

    if ($oldversion < 2017082200) {
        // Warnings idnumber field should use attendanceid instead of cmid.
        $sql = "SELECT cm.id, cm.instance
                  FROM {course_modules} cm
                  JOIN {modules} md ON md.id = cm.module AND md.name = 'attendance'";
        $idnumbers = $DB->get_records_sql_menu($sql);
        $warnings = $DB->get_recordset('attendance_warning');
        foreach ($warnings as $warning) {
            if (!empty($warning->idnumber) && !empty($idnumbers[$warning->idnumber])) {
                $warning->idnumber = $idnumbers[$warning->idnumber];
                $DB->update_record("attendance_warning", $warning);
            }
        }
        $warnings->close();

        // Attendance savepoint reached.
        upgrade_mod_savepoint(true, 2017082200, 'attendance');
    }

    if ($oldversion < 2017120700) {
        $table = new xmldb_table('attendance_sessions');

        $field = new xmldb_field('absenteereport');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '1', 'statusset');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2017120700, 'attendance');
    }

    if ($oldversion < 2017120801) {
        $table = new xmldb_table('attendance_sessions');

        $field = new xmldb_field('autoassignstatus');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, '0', 'studentscanmark');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2017120801, 'attendance');
    }

    if ($oldversion < 2018022204) {
        $table = new xmldb_table('attendance');
        $field = new xmldb_field('showextrauserdetails', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED,
            XMLDB_NOTNULL, null, '1', 'showsessiondetails');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2018022204, 'attendance');
    }

    if ($oldversion < 2018050100) {
        $table = new xmldb_table('attendance_sessions');
        $field = new xmldb_field('preventsharedip', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED,
            XMLDB_NOTNULL, null, '0', 'absenteereport');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        $field = new xmldb_field('preventsharediptime', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED,
            null, null, null, 'preventsharedip');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $table = new xmldb_table('attendance_log');
        $field = new xmldb_field('ipaddress', XMLDB_TYPE_CHAR, '45', null,
            null, null, '', 'remarks');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_mod_savepoint(true, 2018050100, 'attendance');
    }

    if ($oldversion < 2018072700) {
        $table = new xmldb_table('attendance_sessions');
        $field = new xmldb_field('calendarevent', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED,
            XMLDB_NOTNULL, null, '1', 'caleventid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
            if (empty(get_config('attendance', 'enablecalendar'))) {
                // Calendar disabled on this site, set calendarevent for existing records to 0.
                $DB->execute("UPDATE {attendance_sessions} set calendarevent = 0");
            }
        }
        upgrade_mod_savepoint(true, 2018072700, 'attendance');
    }

    if ($oldversion < 2018082605) {
        $table = new xmldb_table('attendance_sessions');
        $field = new xmldb_field('includeqrcode', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED,
            XMLDB_NOTNULL, null, '0', 'calendarevent');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        upgrade_mod_savepoint(true, 2018082605, 'attendance');
    }

    if ($oldversion < 2019012500) {

        // Changing precision of field statusset on table attendance_log to (1333).
        $table = new xmldb_table('attendance_log');
        $field = new xmldb_field('statusset', XMLDB_TYPE_CHAR, '1333', null, null, null, null, 'statusid');

        // Launch change of precision for field statusset.
        $dbman->change_field_precision($table, $field);

        // Attendance savepoint reached.
        upgrade_mod_savepoint(true, 2019012500, 'attendance');
    }

    if ($oldversion < 2019061800) {

        // Make sure default value  to '0'.
        $table = new xmldb_table('attendance_sessions');
        $field = new xmldb_field('preventsharedip', XMLDB_TYPE_INTEGER, '1', XMLDB_UNSIGNED,
            XMLDB_NOTNULL, null, '0', 'absenteereport');

        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_default($table, $field);
        }

        // Attendance savepoint reached.
        upgrade_mod_savepoint(true, 2019061800, 'attendance');
    }

    if ($oldversion < 2019062000) {
        // Make sure sessiondetailspos is not null.
        $table = new xmldb_table('attendance');
        $field = new xmldb_field('sessiondetailspos', XMLDB_TYPE_CHAR, '5', null, XMLDB_NOTNULL, null, 'left', 'subnet');

        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_notnull($table, $field);
        }

        // Make sure maxwarn has default value of '1'.
        $table = new xmldb_table('attendance_warning');
        $field = new xmldb_field('maxwarn', XMLDB_TYPE_INTEGER, '10', null, true, null, '1', 'warnafter');

        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_default($table, $field);
        }

        // Attendance savepoint reached.
        upgrade_mod_savepoint(true, 2019062000, 'attendance');
    }

    if ($oldversion < 2019062200) {

        // Define table attendance_rotate_passwords to be created.
        $table = new xmldb_table('attendance_rotate_passwords');

        // Adding fields to table attendance_rotate_passwords.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('attendanceid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('password', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('expirytime', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table attendance_rotate_passwords.
        $table->add_key('id', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for attendance_rotate_passwords.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define field rotateqrcode to be added to attendance_sessions.
        $table = new xmldb_table('attendance_sessions');
        $field = new xmldb_field('rotateqrcode', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'includeqrcode');

        // Conditionally launch add field rotateqrcode.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field rotateqrcodesecret to be added to attendance_sessions.
        $table = new xmldb_table('attendance_sessions');
        $field = new xmldb_field('rotateqrcodesecret', XMLDB_TYPE_CHAR, '10', null, null, null, null, 'rotateqrcode');

        // Conditionally launch add field rotateqrcodesecret.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Attendance savepoint reached.
        upgrade_mod_savepoint(true, 2019062200, 'attendance');

    }

    if ($oldversion < 2020072900) {
        $table = new xmldb_table('attendance_sessions');

        // Conditionally launch add index caleventid.
        $index = new xmldb_index('caleventid', XMLDB_INDEX_NOTUNIQUE, array('caleventid'));
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }
        // Attendance savepoint reached.
        upgrade_mod_savepoint(true, 2020072900, 'attendance');
    }

    if ($oldversion < 2021050700) {
        // Restore process sometimes creates orphan attendance calendar events - clean them up.
        $sql = "modulename = 'attendance' AND id NOT IN (SELECT caleventid
                                                           FROM {attendance_sessions})";
        $DB->delete_records_select('event', $sql);

        // Attendance savepoint reached.
        upgrade_mod_savepoint(true, 2021050700, 'attendance');
    }

    return $result;
}
