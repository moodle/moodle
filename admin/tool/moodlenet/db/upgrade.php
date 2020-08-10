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
 * Upgrade script for tool_moodlenet.
 *
 * @package    tool_moodlenet
 * @copyright  2020 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade the plugin.
 *
 * @param int $oldversion
 * @return bool always true
 */
function xmldb_tool_moodlenet_upgrade(int $oldversion) {
    global $CFG, $DB;
    if ($oldversion < 2020060500) {

        // Grab some of the old settings.
        $categoryname = get_config('tool_moodlenet', 'profile_category');
        $profilefield = get_config('tool_moodlenet', 'profile_field_name');

        // Master version only!

        // Find out if we have a custom profile field for moodle.net.
        $sql = "SELECT f.*
                  FROM {user_info_field} f
                  JOIN {user_info_category} c ON c.id = f.categoryid and c.name = :categoryname
                 WHERE f.shortname = :name";

        $params = [
            'categoryname' => $categoryname,
            'name' => $profilefield
        ];

        $record = $DB->get_record_sql($sql, $params);

        if (!empty($record)) {
            $userentries = $DB->get_recordset('user_info_data', ['fieldid' => $record->id]);
            $recordstodelete = [];
            foreach ($userentries as $userentry) {
                $data = (object) [
                    'id' => $userentry->userid,
                    'moodlenetprofile' => $userentry->data
                ];
                $DB->update_record('user', $data, true);
                $recordstodelete[] = $userentry->id;
            }
            $userentries->close();

            // Remove the user profile data, fields, and category.
            $DB->delete_records_list('user_info_data', 'id', $recordstodelete);
            $DB->delete_records('user_info_field', ['id' => $record->id]);
            $DB->delete_records('user_info_category', ['name' => $categoryname]);
            unset_config('profile_field_name', 'tool_moodlenet');
            unset_config('profile_category', 'tool_moodlenet');
        }

        upgrade_plugin_savepoint(true, 2020060500, 'tool', 'moodlenet');
    }

    if ($oldversion < 2020061501) {
        // Change the domain.
        $defaultmoodlenet = get_config('tool_moodlenet', 'defaultmoodlenet');

        if ($defaultmoodlenet === 'https://home.moodle.net') {
            set_config('defaultmoodlenet', 'https://moodle.net', 'tool_moodlenet');
        }

        // Change the name.
        $defaultmoodlenetname = get_config('tool_moodlenet', 'defaultmoodlenetname');

        if ($defaultmoodlenetname === 'Moodle HQ MoodleNet') {
            set_config('defaultmoodlenetname', 'MoodleNet Central', 'tool_moodlenet');
        }

        upgrade_plugin_savepoint(true, 2020061501, 'tool', 'moodlenet');
    }

    if ($oldversion < 2020061502) {
        // Disable the MoodleNet integration by default till further notice.
        set_config('enablemoodlenet', 0, 'tool_moodlenet');

        upgrade_plugin_savepoint(true, 2020061502, 'tool', 'moodlenet');
    }

    // Automatically generated Moodle v3.9.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2021052501) {

        // Find out if there are users with MoodleNet profiles set.
        $sql = "SELECT u.*
                  FROM {user} u
                 WHERE u.moodlenetprofile IS NOT NULL";

        $records = $DB->get_records_sql($sql);

        foreach ($records as $record) {
            // Force clean user value just incase there is something malicious.
            $record->moodlenetprofile = clean_text($record->moodlenetprofile, PARAM_NOTAGS);
            $DB->update_record('user', $record);
        }

        upgrade_plugin_savepoint(true, 2021052501, 'tool', 'moodlenet');
    }

    return true;
}
