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
 * @package   block_use_stats
 * @category  blocks
 * @copyright 2006 Valery Fremaux
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Standard uprade callback.
 * This function does anything necessary to upgrade
 * older versions to match current functionality
 */
function xmldb_block_use_stats_upgrade($oldversion = 0) {
    global $CFG, $DB;

    $result = true;

    $dbman = $DB->get_manager();

    if ($result && $oldversion < 2013040900) { // New version in version.php.

        $lasttime = 0;

        // Pre Moodle 2.

        $table = new xmldb_table('use_stats_log');
        if ($dbman->table_exists($table)) {
            $dbman->rename_table($table, 'block_use_stats_log');

            $table = new xmldb_table('use_stats');
            $dbman->rename_table($table, 'block_use_stats');

            $table = new xmldb_table('use_stats_userdata');
            if ($dbman->table_exists($table)) {
                $dbman->drop_table($table);
            }
        } else {

            // Define table use_stats_log to be created.
            $table = new xmldb_table('block_use_stats_log');

            if (!$dbman->table_exists($table)) {
                // Adding fields to table use_stats.
                $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
                $table->add_field('logid', XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, null, null, null, null, '0');
                $table->add_field('gap', XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, null, null, null, null, '0');
                $table->add_field('userid', XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, null, null, null, null, '0');
                $table->add_field('time', XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, null, null, null, null, '0');
                $table->add_field('course', XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, null, null, null, null, '0');
                $table->add_field('customtag1', XMLDB_TYPE_CHAR, '20', null, null, null, null, null, '');
                $table->add_field('customtag2', XMLDB_TYPE_CHAR, '20', null, null, null, null, null, '');
                $table->add_field('customtag3', XMLDB_TYPE_CHAR, '20', null, null, null, null, null, '');
                $table->add_field('customtag4', XMLDB_TYPE_CHAR, '30', null, null, null, null, null, '');
                $table->add_field('customtag5', XMLDB_TYPE_CHAR, '30', null, null, null, null, null, '');
                $table->add_field('customtag6', XMLDB_TYPE_CHAR, '30', null, null, null, null, null, '');

                // Adding keys to table use_stats.
                $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));
                $table->add_key('ix_logid_unique', XMLDB_KEY_UNIQUE, array('logid'));

                // Launch create table for use_stats.
                $dbman->create_table($table);
            }
        }

        // Feed the table with log gaps.
        $previouslog = array();
        $rs = $DB->get_recordset('log', array(), 'time', 'id,time,userid,course');
        if ($rs) {

            $r = 0;

            $starttime = time();

            while ($rs->valid()) {
                $log = $rs->current();
                $gaprec = new StdClass;
                $gaprec->logid = $log->id;
                $gaprec->time = $log->time;
                $gaprec->course = $log->course;

                for ($ci = 1; $ci <= 6; $ci++) {
                    $key = "customtag".$ci;
                    $gaprec->$key = '';
                    // Beware : at this epoch, the configuration is still centric.
                    if (!empty($CFG->block_use_stats_enablecompilecube)) {
                        $customselectkey = "block_use_stats_customtag{$ci}select";
                        if (!empty($CFG->$customselectkey)) {
                            $customsql = str_replace('<%%LOGID%%>', $log->id, stripslashes($CFG->$customselectkey));
                            $customsql = str_replace('<%%USERID%%>', $log->userid, $customsql);
                            $customsql = str_replace('<%%COURSEID%%>', $log->course, $customsql);
                            $customsql = str_replace('<%%CMID%%>', $log->cmid, $customsql);
                            $gaprec->$key = $DB->get_field_sql($customsql, array());
                        }
                    }
                }

                $gaprec->gap = 0;
                if (!$DB->record_exists('block_use_stats_log', array('logid' => $log->id))) {
                    $DB->insert_record('block_use_stats_log', $gaprec);
                }
                if (array_key_exists($log->userid, $previouslog)) {
                    $value = $log->time - $previouslog[$log->userid]->time;
                    $DB->set_field('block_use_stats_log', 'gap', $value, array('logid' => $previouslog[$log->userid]->id));
                }
                $previouslog[$log->userid] = $log;
                $lasttime = $log->time;
                $r++;
                if ($r % 10 == 0) {
                    $processtime = time();
                    if (($processtime > $starttime + HOURSECS) || $r > 100000) {
                        break; // If compilation is too long, let cron continue processing untill all done.
                    }
                }
                $rs->next();
            }
            $rs->close();

            // Register las logtime for cron further updates.
            mtrace("$r logs gapped");
            // Beware : at this epoch, the configuration is still centric.
            $CFG->use_stats_last_log = $lasttime;
        }

        // Use_stats savepoint reached.
        upgrade_block_savepoint($result, 2013040900, 'use_stats');
    }

    // Moodle 2.

    if ($result && $oldversion < 2013060900) {

        // Transfer the last compile time in new config variable.
        // Beware : at this epoch, the configuration is still centric.
        set_config('block_use_stats_lastcompiled', $CFG->use_stats_last_log);
        set_config('use_stats_last_log', null);

        // Use_stats savepoint reached.
        upgrade_block_savepoint($result, 2013060900, 'use_stats');
    }

    // Moodle 2.7.
    if ($result && $oldversion < 2015062500) {

        // Transfer old settings values to component scope.
        $settingkeys = preg_grep('/^block_use_stats_/', array_keys((array)$CFG));
        foreach ($settingkeys as $settingkey) {
            $newkey = str_replace('block_use_stats_', '', $settingkey);
            set_config($newkey, $CFG->$settingkey, 'block_use_stats');
            // Remove from central config.
            set_config($settingkey, null);
        }

        // Use_stats savepoint reached.
        upgrade_block_savepoint($result, 2015062500, 'use_stats');
    }

    if ($oldversion < 2016012100) {

        // Define index ix_logid (unique) to be added to block_use_stats_log.
        $table = new xmldb_table('block_use_stats_log');
        $index = new xmldb_index('ix_logid', XMLDB_INDEX_UNIQUE, array('logid'));

        // Conditionally launch add index ix_logid.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        $index = new xmldb_index('ix_userid', XMLDB_INDEX_NOTUNIQUE, array('userid'));

        // Conditionally launch add index ix_userid.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        $index = new xmldb_index('ix_course', XMLDB_INDEX_NOTUNIQUE, array('course'));

        // Conditionally launch add index ix_course.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        $index = new xmldb_index('ix_customtag1', XMLDB_INDEX_NOTUNIQUE, array('customtag1'));

        // Conditionally launch add index ix_customtag1.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        $index = new xmldb_index('ix_customtag2', XMLDB_INDEX_NOTUNIQUE, array('customtag2'));

        // Conditionally launch add index ix_customtag2.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        $index = new xmldb_index('ix_customtag3', XMLDB_INDEX_NOTUNIQUE, array('customtag3'));

        // Conditionally launch add index ix_customtag3.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Use_stats savepoint reached.
        upgrade_block_savepoint(true, 2016012100, 'use_stats');
    }

    if ($oldversion < 2016020600) {
        $table = new xmldb_table('block_use_stats');

        $field = new xmldb_field('compiletime');
        $field->set_attributes(XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0', 'events');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }
        // Use_stats savepoint reached.
        upgrade_block_savepoint(true, 2016020600, 'use_stats');
    }

    if ($oldversion < 2016111100) {
        // Define table use_stats_session to be created.
        $table = new xmldb_table('block_use_stats_session');

        if (!$dbman->table_exists($table)) {
            // Adding fields to table use_stats.
            $table->add_field('id', XMLDB_TYPE_INTEGER, '10', XMLDB_UNSIGNED, XMLDB_NOTNULL, XMLDB_SEQUENCE, null, null, null);
            $table->add_field('userid', XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->add_field('sessionstart', XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, XMLDB_NOTNULL, null, null, null, '0');
            $table->add_field('sessionend', XMLDB_TYPE_INTEGER, '11', XMLDB_UNSIGNED, null, null, null, null, '0');
            $table->add_field('courses', XMLDB_TYPE_TEXT, 'small', null, null, null, null, null, null);

            // Adding keys to table use_stats.
            $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

            // Launch create table for use_stats.
            $dbman->create_table($table);
        }

        // Use_stats savepoint reached.
        upgrade_block_savepoint(true, 2016111100, 'use_stats');
    }

    if ($oldversion < 2017021600) {
        // Define table use_stats_session to be created.
        $table = new xmldb_table('block_use_stats_session');

        $field = new xmldb_field('courses', XMLDB_TYPE_TEXT, 'small', null, null, null);
        $dbman->change_field_precision($table, $field);

        // Use_stats savepoint reached.
        upgrade_block_savepoint(true, 2017021600, 'use_stats');
    }

    return $result;
}
