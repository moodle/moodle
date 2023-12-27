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
 * This file keeps track of upgrades to the h5pactivity module
 *
 * Sometimes, changes between versions involve
 * alterations to database structures and other
 * major things that may break installations.
 *
 * The upgrade function in this file will attempt
 * to perform all the necessary actions to upgrade
 * your older installation to the current version.
 *
 * If there's something it cannot do itself, it
 * will tell you what you need to do.
 *
 * The commands in here will all be database-neutral,
 * using the methods of database_manager class
 *
 * Please do not forget to use upgrade_set_timeout()
 * before any action that may take longer time to finish.
 *
 * @package   mod_h5pactivity
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Function to upgrade mod_h5pactivity.
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_h5pactivity_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager(); // Loads ddl manager and xmldb classes.

    if ($oldversion < 2020032300) {

        // Changing the default of field timecreated on table h5pactivity to drop it.
        $table = new xmldb_table('h5pactivity');
        $field = new xmldb_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'name');

        // Launch change of default for field timecreated.
        $dbman->change_field_default($table, $field);

        // Changing the default of field timemodified on table h5pactivity to drop it.
        $field = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null, 'timecreated');

        // Launch change of default for field timemodified.
        $dbman->change_field_default($table, $field);

        // Define table h5pactivity_attempts to be created.
        $table = new xmldb_table('h5pactivity_attempts');

        // Adding fields to table h5pactivity_attempts.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('h5pactivityid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('attempt', XMLDB_TYPE_INTEGER, '6', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('rawscore', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');
        $table->add_field('maxscore', XMLDB_TYPE_INTEGER, '10', null, null, null, '0');

        // Adding keys to table h5pactivity_attempts.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('fk_h5pactivityid', XMLDB_KEY_FOREIGN, ['h5pactivityid'], 'h5pactivity', ['id']);
        $table->add_key('uq_activityuserattempt', XMLDB_KEY_UNIQUE, ['h5pactivityid', 'userid', 'attempt']);

        // Adding indexes to table h5pactivity_attempts.
        $table->add_index('timecreated', XMLDB_INDEX_NOTUNIQUE, ['timecreated']);
        $table->add_index('h5pactivityid-timecreated', XMLDB_INDEX_NOTUNIQUE, ['h5pactivityid', 'timecreated']);
        $table->add_index('h5pactivityid-userid', XMLDB_INDEX_NOTUNIQUE, ['h5pactivityid', 'userid']);

        // Conditionally launch create table for h5pactivity_attempts.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Define table h5pactivity_attempts_results to be created.
        $table = new xmldb_table('h5pactivity_attempts_results');

        // Adding fields to table h5pactivity_attempts_results.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('attemptid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('subcontent', XMLDB_TYPE_CHAR, '128', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('interactiontype', XMLDB_TYPE_CHAR, '128', null, null, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('correctpattern', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('response', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('additionals', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('rawscore', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('maxscore', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');

        // Adding keys to table h5pactivity_attempts_results.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('fk_attemptid', XMLDB_KEY_FOREIGN, ['attemptid'], 'h5pactivity_attempts', ['id']);

        // Adding indexes to table h5pactivity_attempts_results.
        $table->add_index('attemptid-timecreated', XMLDB_INDEX_NOTUNIQUE, ['attemptid', 'timecreated']);

        // Conditionally launch create table for h5pactivity_attempts_results.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // H5pactivity savepoint reached.
        upgrade_mod_savepoint(true, 2020032300, 'h5pactivity');
    }

    if ($oldversion < 2020041400) {

        // Define field duration to be added to h5pactivity_attempts.
        $table = new xmldb_table('h5pactivity_attempts');
        $field = new xmldb_field('duration', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', 'maxscore');

        // Conditionally launch add field duration.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field completion to be added to h5pactivity_attempts.
        $field = new xmldb_field('completion', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'duration');

        // Conditionally launch add field completion.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field success to be added to h5pactivity_attempts.
        $field = new xmldb_field('success', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'completion');

        // Conditionally launch add field success.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field duration to be added to h5pactivity_attempts_results.
        $table = new xmldb_table('h5pactivity_attempts_results');
        $field = new xmldb_field('duration', XMLDB_TYPE_INTEGER, '10', null, null, null, '0', 'maxscore');

        // Conditionally launch add field duration.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field completion to be added to h5pactivity_attempts_results.
        $field = new xmldb_field('completion', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'duration');

        // Conditionally launch add field completion.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field success to be added to h5pactivity_attempts_results.
        $field = new xmldb_field('success', XMLDB_TYPE_INTEGER, '1', null, null, null, null, 'completion');

        // Conditionally launch add field success.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // H5pactivity savepoint reached.
        upgrade_mod_savepoint(true, 2020041400, 'h5pactivity');
    }

    if ($oldversion < 2020041401) {

        // Define field enabletracking to be added to h5pactivity.
        $table = new xmldb_table('h5pactivity');
        $field = new xmldb_field('enabletracking', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'displayoptions');

        // Conditionally launch add field enabletracking.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field grademethod to be added to h5pactivity.
        $field = new xmldb_field('grademethod', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '1', 'enabletracking');

        // Conditionally launch add field grademethod.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Define field scaled to be added to h5pactivity_attempts.
        $table = new xmldb_table('h5pactivity_attempts');
        $field = new xmldb_field('scaled', XMLDB_TYPE_NUMBER, '10, 5', null, XMLDB_NOTNULL, null, '0', 'maxscore');

        // Conditionally launch add field scaled.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Calculate all scaled values from current attempts.
        $rs = $DB->get_recordset('h5pactivity_attempts');
        foreach ($rs as $record) {
            if (empty($record->maxscore)) {
                continue;
            }
            $record->scaled = $record->rawscore / $record->maxscore;
            $DB->update_record('h5pactivity_attempts', $record);
        }
        $rs->close();

        // H5pactivity savepoint reached.
        upgrade_mod_savepoint(true, 2020041401, 'h5pactivity');
    }

    if ($oldversion < 2020042202) {

        // Define field reviewmode to be added to h5pactivity.
        $table = new xmldb_table('h5pactivity');
        $field = new xmldb_field('reviewmode', XMLDB_TYPE_INTEGER, '4', null, null, null, '1', 'grademethod');

        // Conditionally launch add field reviewmode.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // H5pactivity savepoint reached.
        upgrade_mod_savepoint(true, 2020042202, 'h5pactivity');
    }

    // Automatically generated Moodle v3.9.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.0.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.1.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.2.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2023042401) {

        // Remove any orphaned attempt/result records (pointing to non-existing activities).
        $DB->delete_records_select('h5pactivity_attempts', 'NOT EXISTS (
            SELECT 1 FROM {h5pactivity} h5p WHERE h5p.id = {h5pactivity_attempts}.h5pactivityid
        )');

        $DB->delete_records_select('h5pactivity_attempts_results', 'NOT EXISTS (
            SELECT 1 FROM {h5pactivity_attempts} attempt WHERE attempt.id = {h5pactivity_attempts_results}.attemptid
        )');

        // H5pactivity savepoint reached.
        upgrade_mod_savepoint(true, 2023042401, 'h5pactivity');
    }

    // Automatically generated Moodle v4.3.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
