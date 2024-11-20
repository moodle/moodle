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
 * Upgrade logic.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2010 onwards, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Jesus Federico  (jesus [at] blindsidenetworks [dt] com)
 * @author    Fred Dixon  (ffdixon [at] blindsidenetworks [dt] com)
 */

use mod_bigbluebuttonbn\plugin;
use mod_bigbluebuttonbn\local\config;
use mod_bigbluebuttonbn\task\upgrade_recordings_task;

/**
 * Performs data migrations and updates on upgrade.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_bigbluebuttonbn_upgrade($oldversion = 0) {
    global $DB;
    $dbman = $DB->get_manager();

    // Automatically generated Moodle v4.1.0 release upgrade line.
    // Put any upgrade step following this.
    if ($oldversion < 2023011800) {
        // Define index course_bbbid_ix (not unique) to be added to bigbluebuttonbn_logs.
        $table = new xmldb_table('bigbluebuttonbn_logs');
        $index = new xmldb_index('course_bbbid_ix', XMLDB_INDEX_NOTUNIQUE, ['courseid', 'bigbluebuttonbnid']);

        // Conditionally launch add index course_bbbid_ix.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Bigbluebuttonbn savepoint reached.
        upgrade_mod_savepoint(true, 2023011800, 'bigbluebuttonbn');
    }
    if ($oldversion < 2023021300) {
        // Define field lockedlayout to be dropped from bigbluebuttonbn.
        $table = new xmldb_table('bigbluebuttonbn');
        $field = new xmldb_field('lockedlayout');

        // Conditionally launch drop field lockedlayout.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Bigbluebuttonbn savepoint reached.
        upgrade_mod_savepoint(true, 2023021300, 'bigbluebuttonbn');
    }

    // Automatically generated Moodle v4.2.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.3.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v4.4.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2024071900) {

        // Define field showpresentation to be added to bigbluebuttonbn.
        $table = new xmldb_table('bigbluebuttonbn');
        $field = new xmldb_field('showpresentation', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'guestpassword');

        // Conditionally launch add field showpresentation.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Bigbluebuttonbn savepoint reached.
        upgrade_mod_savepoint(true, 2024071900, 'bigbluebuttonbn');
    }

    // Automatically generated Moodle v4.5.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}

/**
 * Generic helper function for adding or changing a field in a table.
 *
 * @param database_manager $dbman
 * @param string $tablename
 * @param string $fieldname
 * @param array $fielddefinition
 * @deprecated  please do not use this anymore (historical migrations)
 */
function xmldb_bigbluebuttonbn_add_change_field(
    database_manager $dbman,
    string $tablename,
    string $fieldname,
    array $fielddefinition
) {
    $table = new xmldb_table($tablename);
    $field = new xmldb_field($fieldname);
    $field->set_attributes(
        $fielddefinition['type'],
        $fielddefinition['precision'],
        $fielddefinition['unsigned'],
        $fielddefinition['notnull'],
        $fielddefinition['sequence'],
        $fielddefinition['default'],
        $fielddefinition['previous']
    );
    if (!$dbman->field_exists($table, $field)) {
        $dbman->add_field($table, $field, true, true);
        return;
    }
    // Drop key before if needed.
    $fieldkey = new xmldb_key($fieldname, XMLDB_KEY_FOREIGN, [$fieldname], 'user', ['id']);
    if ($dbman->find_key_name($table, $fieldkey)) {
        $dbman->drop_key($table, $fieldkey);
    }
    $dbman->change_field_type($table, $field, true, true);
    $dbman->change_field_precision($table, $field, true, true);
    $dbman->change_field_notnull($table, $field, true, true);
    $dbman->change_field_default($table, $field, true, true);
}

/**
 * Generic helper function for adding index to a table.
 *
 * @param database_manager $dbman
 * @param string $tablename
 * @param string $indexname
 * @param array $indexfields
 * @param string|false|null $indextype
 * @deprecated please do not use this anymore (historical migrations)
 */
function xmldb_bigbluebuttonbn_index_table(
    database_manager $dbman,
    string $tablename,
    string $indexname,
    array $indexfields,
    $indextype = XMLDB_INDEX_NOTUNIQUE
) {
    $table = new xmldb_table($tablename);
    if (!$dbman->table_exists($table)) {
        return;
    }
    $index = new xmldb_index($indexname, $indextype, $indexfields);
    if ($dbman->index_exists($table, $index)) {
        $dbman->drop_index($table, $index);
    }
    $dbman->add_index($table, $index, true, true);
}
