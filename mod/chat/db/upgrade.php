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
 * Upgrade code for the chat activity
 *
 * @package   mod_chat
 * @copyright 2006 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function xmldb_chat_upgrade($oldversion) {
    global $CFG,$DB;

    // Moodle v3.1.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.2.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.3.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.4.0 release upgrade line.
    // Put any upgrade step following this.


    if ($oldversion < 2017113000) {
        // Correction to not break my sql MDL-60793.
        $dbman = $DB->get_manager();
        $table = new xmldb_table('chat_messages');
        $field = new xmldb_field('system', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0','groupid');
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'systemmessage');
        }

        $table = new xmldb_table('chat_messages_current');
        if ($dbman->field_exists($table, $field)) {
            $dbman->rename_field($table, $field, 'systemmessage');
        }

        upgrade_plugin_savepoint(true, 2017113000, 'mod', 'chat');
    }


    return true;
}
