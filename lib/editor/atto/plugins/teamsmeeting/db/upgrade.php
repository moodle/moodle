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
 * Atto text editor Teams Meeting integration upgrade file.
 *
 * @package    atto_teamsmeeting
 * @copyright  2020 Enovation Solutions
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Update plugin.
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_atto_teamsmeeting_upgrade($oldversion) {
    global $DB, $USER, $SITE;

    $dbman = $DB->get_manager();
    $result = true;

    if ($oldversion < 2020032700) {
        // Define table atto_teamsmeeting to be created.
        $table = new xmldb_table('atto_teamsmeeting');

        // Adding fields to table atto_teamsmeeting.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('title', XMLDB_TYPE_CHAR, '255', null, false, null, null);
        $table->add_field('link', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('options', XMLDB_TYPE_TEXT, null, null, false, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table atto_teamsmeeting.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        // Conditionally launch create table for atto_teamsmeeting.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Teamsmeeting savepoint reached.
        upgrade_plugin_savepoint(true, 2020032700, 'atto', 'teamsmeeting');
    }

    if ($oldversion < 2020032705) {
        // Update legacy meetings app URL.
        $meetingsapplink = get_config('atto_teamsmeeting', 'meetingapplink');

        if ($meetingsapplink && is_string($meetingsapplink) && strtolower($meetingsapplink) == 'https://enovation.ie/msteams') {
            set_config('meetingapplink', 'https://enomsteams.z16.web.core.windows.net', 'atto_teamsmeeting');
        }

        // Teamsmeeting savepoint reached.
        upgrade_plugin_savepoint(true, 2020032705, 'atto', 'teamsmeeting');
    }

    return $result;
}
