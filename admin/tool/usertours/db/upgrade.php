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

    // Automatically generated Moodle v3.5.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.6.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.7.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.8.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2020061501) {
        // Updating shipped tours will fix broken sortorder records in existing tours.
        manager::update_shipped_tours();

        upgrade_plugin_savepoint(true, 2020061501, 'tool', 'usertours');
    }

    // Automatically generated Moodle v3.9.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2020082700) {
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

        upgrade_plugin_savepoint(true, 2020082700, 'tool', 'usertours');
    }

    // Automatically generated Moodle v3.10.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}
