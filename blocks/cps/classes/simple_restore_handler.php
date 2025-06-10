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
 *
 * @package    block_cps
 * @copyright  2019 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

abstract class blocks_cps_simple_restore_handler {
    public static function simple_restore_complete($params) {
        global $DB, $CFG, $USER;
        require_once($CFG->dirroot . '/blocks/cps/classes/lib.php');

        extract($params); // TODO replace as it bad practice to use extract() 10/11/2019.
        $restoreto = $course_settings['restore_to'];
        $oldcourse = $course_settings['course'];

        $skip = array(
            'id', 'category', 'sortorder',
            'sectioncache', 'modinfo', 'newsitems'
        );

        $course = $DB->get_record('course', array('id' => $oldcourse->id));

        $resetgrades = cps_setting::get(array(
            'name' => 'user_grade_restore',
            'userid' => $USER->id
        ));

        // Defaults to reset grade items.
        if (empty($resetgrades)) {
            $resetgrades = new stdClass;
            $resetgrades->value = 1;
        }

        // Maintain the correct config.
        foreach (get_object_vars($oldcourse) as $key => $value) {
            if (in_array($key, $skip)) {
                continue;
            }

            $course->$key = $value;
        }

        $DB->update_record('course', $course);

        if ($resetgrades->value == 1) {
            require_once($CFG->libdir . '/gradelib.php');

            $items = grade_item::fetch_all(array('courseid' => $course->id));
            foreach ($items as $item) {
                $item->plusfactor = 0.00000;
                $item->multfactor = 1.00000;
                $item->update();
            }

            grade_regrade_final_grades($course->id);
        }

        // This is an import, ignore.
        if ($restoreto == 1) {
            return true;
        }

        $keepenrollments = (bool) get_config('simple_restore', 'keep_roles_and_enrolments');
        $keepgroups = (bool) get_config('simple_restore', 'keep_groups_and_groupings');

        // No need to re-enroll.
        if ($keepgroups and $keepenrollments) {
            $enrolinstances = $DB->get_records('enrol', array(
                'courseid' => $oldcourse->id,
                'enrol' => 'ues'
            ));

            // Cleanup old instances.
            $ues = enrol_get_plugin('ues');

            foreach (array_slice($enrolinstances, 1) as $instance) {
                $ues->delete_instance($instance);
            }

        } else {
            $sections = ues_section::from_course($course);

            // Nothing to do.
            if (empty($sections)) {
                return true;
            }

            // Rebuild enrollment.
            ues::enroll_users(ues_section::from_course($course));
        }

        return true;
    }
}