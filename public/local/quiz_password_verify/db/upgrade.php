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
 * Quiz Password Verification Plugin upgrade script
 *
 * @package    local_quiz_password_verify
 * @copyright  2024 Author Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade the plugin
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_local_quiz_password_verify_upgrade($oldversion) {
    global $DB;
    $dbman = $DB->get_manager();

    if ($oldversion < 2024112504) {
        $table = new xmldb_table('local_quiz_password_verify');
        $field = new xmldb_field('attemptid', XMLDB_TYPE_INTEGER, '10', null, false, null, null, 'userid');
        $index = new xmldb_index('attemptid_idx', XMLDB_INDEX_NOTUNIQUE, ['attemptid']);
        $key = new xmldb_key('attemptid', XMLDB_KEY_FOREIGN, ['attemptid'], 'quiz_attempts', ['id']);

        // 1. Drop Foreign Key if exists.
        if ($dbman->find_key_name($table, $key)) {
            $dbman->drop_key($table, $key);
        }

        // 2. Drop Index if exists.
        if ($dbman->find_index_name($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // 3. Change field to nullable.
        if ($dbman->field_exists($table, $field)) {
            $dbman->change_field_notnull($table, $field);
        }

        // 4. Re-create Index.
        if (!$dbman->find_index_name($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // 5. Re-create Foreign Key.
        if (!$dbman->find_key_name($table, $key)) {
            $dbman->add_key($table, $key);
        }

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2024112504, 'local', 'quiz_password_verify');
    }

    return true;
}
