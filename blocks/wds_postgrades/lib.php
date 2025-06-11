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
 * Library functions for WDS Post Grades block.
 *
 * @package    block_wds_postgrades
 * @copyright  2025 onwards Louisiana State University
 * @copyright  2025 onwards Robert Russo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Function to extend the block settings navigation.
 *
 * @param settings_navigation $settingsnav The settings navigation object.
 * @param context $context The context.
 */
function block_wds_postgrades_extend_settings_navigation($settingsnav, $context) {
    global $PAGE;

    // Only add this settings item on non-site course pages.
    if (!$PAGE->course or $PAGE->course->id == SITEID) {
        return;
    }

    // Only let users with the appropriate capability see this settings item.
    if (!has_capability('moodle/backup:backupcourse', $context)) {
        return;
    }
}

/**
 * Add external page to the admin menu.
 *
 * @param admin_root $admin The admin root object.
 */
function block_wds_postgrades_admin_menu($admin) {
    $ADMIN->add('blocksettings', new admin_externalpage(
        'blockwdspostgradesperiodconfig',
        get_string('managegradeperiods', 'block_wds_postgrades'),
        new moodle_url('/blocks/wds_postgrades/period_config.php')
    ));
}
