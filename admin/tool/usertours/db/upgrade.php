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
 * Upgrade code for install
 *
 * @package   tool_usertours
 * @copyright 2016 Ryan Wyllie <ryan@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use tool_usertours\manager;
use tool_usertours\tour;

/**
 * Upgrade the user tours plugin.
 *
 * @param int $oldversion The old version of the user tours plugin
 * @return bool
 */
function xmldb_tool_usertours_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    // Automatically generated Moodle v3.6.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.7.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.8.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.9.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2021052501) {
        // Clean up user preferences of deleted tours.
        $select = $DB->sql_like('name', ':lastcompleted') . ' OR ' . $DB->sql_like('name', ':requested');
        $params = [
            'lastcompleted' => tour::TOUR_LAST_COMPLETED_BY_USER . '%',
            'requested' => tour::TOUR_REQUESTED_BY_USER . '%',
        ];

        $preferences = $DB->get_records_select('user_preferences', $select, $params, '', 'DISTINCT name');
        foreach ($preferences as $preference) {
            // Match tour ID at the end of the preference name, remove all of that preference type if tour ID doesn't exist.
            if (preg_match('/(?<tourid>\d+)$/', $preference->name, $matches) &&
                    !$DB->record_exists('tool_usertours_tours', ['id' => $matches['tourid']])) {

                $DB->delete_records('user_preferences', ['name' => $preference->name]);
            }
        }

        upgrade_plugin_savepoint(true, 2021052501, 'tool', 'usertours');
    }

    if ($oldversion < 2021092300) {
        // Define field endtourlabel to be added to tool_usertours_tours.
        $table = new xmldb_table('tool_usertours_tours');
        $field = new xmldb_field('endtourlabel', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'sortorder');

        // Conditionally launch add field endtourlabel.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Usertours savepoint reached.
        upgrade_plugin_savepoint(true, 2021092300, 'tool', 'usertours');
    }

    if ($oldversion < 2021100700) {

        // Define field displaystepnumbers to be added to tool_usertours_tours.
        $table = new xmldb_table('tool_usertours_tours');
        $field = new xmldb_field('displaystepnumbers', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'configdata');

        // Conditionally launch add field displaystepnumbers.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Usertours savepoint reached.
        upgrade_plugin_savepoint(true, 2021100700, 'tool', 'usertours');
    }

    if ($oldversion < 2022040601) {
        // Define field contentformat to be added to tool_usertours_steps.
        $table = new xmldb_table('tool_usertours_steps');
        $field = new xmldb_field('contentformat', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, FORMAT_MOODLE, 'content');

        // Conditionally launch add field contentformat.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        } else {
            // Field was added by previous upgrade step with the default value is FORMAT_HTML.
            // Need to drop the field and re-create with the new structure to make sure all the existing tours use FORMAT_MOODLE.
            // FORMAT_MOODLE will force the external_format_text method to use nl2br to convert the new line to line break tag.
            $dbman->drop_field($table, $field);
            // Add the field again.
            $dbman->add_field($table, $field);
        }

        // Usertours savepoint reached.
        upgrade_plugin_savepoint(true, 2022040601, 'tool', 'usertours');
    }

    if ($oldversion < 2022040602) {
        // Update shipped tours.
        // Normally, we just bump the version numbers because we need to call update_shipped_tours only once.
        manager::update_shipped_tours();

        upgrade_plugin_savepoint(true, 2022040602, 'tool', 'usertours');
    }

    // Automatically generated Moodle v4.0.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
