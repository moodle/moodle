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
 * The main block file.
 *
 * @package    block_ues_reprocess
 * @copyright  Louisiana State University
 * @copyright  David Lowe
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

// Create the settings block.
$settings = new admin_settingpage($section, get_string('settings', 'block_ues_reprocess'));

// Make sure only admins see this one.
if ($ADMIN->fulltree) {
    $priorafter = get_config('ues_reprocess', 'priorafter');
    $pa = $priorafter ? $priorafter : 60;

    // We should probably grab semesters this way.
    $sql = "SELECT * FROM {enrol_ues_semesters}
            WHERE grades_due > " . time() - ($pa * 86400) . "
            AND classes_start < " . time() + ($pa * 86400) . "
            ORDER BY campus DESC, year ASC, id ASC, session_key ASC";

    // Actually get them.
    $sems = $DB->get_records_sql($sql);

    // Build the kvp array.
    $semarray = [];

    // Loop through the array of objects and build the key and value pairs.
    foreach ($sems as $sem) {
        // Set the key to the id.
        $key = $sem->id;

        // Depending if we have a session or not, build the value.
        if ($sem->session_key == '') { 
            $value = $sem->year . " " . $sem->name . " " . $sem->campus;
        } else {
            $value = $sem->year . " " . $sem->name . " (" . $sem->session_key . ") " . $sem->campus;
        }

        // Populate the array.
        $semarray[$key] = $value;
    }

    if (count($semarray) == 0) {
        $semarray[0] = "No semesters found";
    }

    // Add the setting.
    $settings->add(
        new admin_setting_configmultiselect(
            'ues_reprocess/sems',
                get_string('semesters', 'block_ues_reprocess'),
                get_string('semesters_help', 'block_ues_reprocess'),
                array(),
                $semarray
        )
    );

    $coursecats = $DB->get_records_menu(
        'course_categories',
        null,
        'name ASC',
        'id, name'
    );

    if (count($coursecats) == 0) {
        $coursecats[0] = "No categories found";
    }

    $settings->add(
        new admin_setting_configmultiselect(
            'ues_reprocess/cats', get_string('categories', 'block_ues_reprocess'),
            get_string('categories_help', 'block_ues_reprocess'),
            array(),
            $coursecats
        )
    );

    $settings->add(
        new admin_setting_configtext(
            'ues_reprocess/priorafter',
            get_string('priorafter', 'block_ues_reprocess'),
            get_string('priorafter_help', 'block_ues_reprocess'),
            60 // Default.
        )
    );

}
