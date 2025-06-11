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
 * Panopto Submission restore activity task.
 *
 * @package mod_panoptosubmission
 * @copyright  Panopto 2021
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/mod/panoptosubmission/backup/moodle2/restore_panoptosubmission_stepslib.php');

/**
 * Panopto Submission restore activity task.
 *
 * @package mod_panoptosubmission
 * @copyright  Panopto 2021
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_panoptosubmission_activity_task extends restore_activity_task {

    /**
     * defines settings for restore
     */
    protected function define_my_settings() {
    }

    /**
     * Defines steps for restore
     */
    protected function define_my_steps() {
        $this->add_step(
            new restore_panoptosubmission_activity_structure_step('panoptosubmission_structure', 'panoptosubmission.xml')
        );
    }

    /**
     * Decode contents for restore
     */
    public static function define_decode_contents() {
        $contents = [];

        $contents[] = new restore_decode_content('panoptosubmission', ['intro'], 'panoptosubmission');

        return $contents;
    }

    /**
     * Decode rules for restore
     */
    public static function define_decode_rules() {
        $rules = [];

        $rules[] = new restore_decode_rule('PANOPTOSUBMISSIONVIEWBYID', '/mod/panoptosubmission/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('PANOPTOSUBMISSIONINDEX', '/mod/panoptosubmission/index.php?id=$1', 'course');

        return $rules;

    }

    /**
     * log rules for restore
     */
    public static function define_restore_log_rules() {
        $rules = [];

        $rules[] = new restore_log_rule('panoptosubmission', 'add', 'view.php?id={course_module}', '{panoptosubmission}');
        $rules[] = new restore_log_rule('panoptosubmission', 'update', 'view.php?id={course_module}', '{panoptosubmission}');
        $rules[] = new restore_log_rule('panoptosubmission', 'view', 'view.php?id={course_module}', '{panoptosubmission}');

        return $rules;
    }

    /**
     * Log rules for course during restore
     */
    public static function define_restore_log_rules_for_course() {
        $rules = [];
        $rules[] = new restore_log_rule('panoptosubmission', 'view all', 'index.php?id={course}', null);

        return $rules;
    }
}
