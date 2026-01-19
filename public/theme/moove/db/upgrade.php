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
 * Database upgrade.
 *
 * @package    theme_moove
 * @copyright  2022 Willian Mano {@link https://conecti.me}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

/**
 * Upgrade.
 *
 * @param int   $oldversion Is this an old version
 * @return bool Success.
 */
function xmldb_theme_moove_upgrade($oldversion = 0) {
    global $DB;

    if ($oldversion < 2022052800) {
        $usertours = $DB->get_records('tool_usertours_tours');

        if ($usertours) {
            foreach ($usertours as $usertour) {
                $configdata = json_decode($usertour->configdata);

                if (in_array('boost', $configdata->filtervalues->theme)) {
                    $configdata->filtervalues->theme[] = 'moove';
                }

                $updatedata = new stdClass();
                $updatedata->id = $usertour->id;
                $updatedata->configdata = json_encode($configdata);

                $DB->update_record('tool_usertours_tours', $updatedata);
            }
        }

        upgrade_plugin_savepoint(true, 2022052800, 'theme', 'moove');
    }

    return true;
}
