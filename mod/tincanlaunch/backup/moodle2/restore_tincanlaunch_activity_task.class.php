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
 * Description of tincanlaunch restore task
 *
 * @package    mod_tincanlaunch
 * @copyright  2016 onward Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/tincanlaunch/backup/moodle2/restore_tincanlaunch_stepslib.php'); // Because it exists (must).

/**
 * Description of tincanlaunch restore task
 *
 * @package    mod_tincanlaunch
 * @copyright  2016 onward Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_tincanlaunch_activity_task extends restore_activity_task {

    /**
     * Define (add) particular settings this activity can have.
     *
     * @return void
     */
    protected function define_my_settings() {
        // No particular settings for this activity.
    }

    /**
     * Define (add) particular steps this activity can have.
     *
     * @return void
     */
    protected function define_my_steps() {
        // Choice only has one structure step.
        $this->add_step(new restore_tincanlaunch_activity_structure_step('tincanlaunch_structure', 'tincanlaunch.xml'));
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder.
     *
     * @return array
     */
    public static function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('tincanlaunch', array('intro'), 'tincanlaunch');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder.
     *
     * @return array
     */
    public static function define_decode_rules() {
        $rules = array();

        // List of tincanlaunchs in course.
        $rules[] = new restore_decode_rule('TINCANLAUNCHINDEX', '/mod/tincanlaunch/index.php?id=$1', 'course');

        // Tincanlaunch by cm->id.
        $rules[] = new restore_decode_rule('TINCANLAUNCHVIEWBYID', '/mod/tincanlaunch/view.php?id=$1', 'course_module');

        // Tincanlaunch by tincanlaunch->id.
        $rules[] = new restore_decode_rule('TINCANLAUNCHVIEWBYB', '/mod/tincanlaunch/view.php?b=$1', 'tincanlaunch');

        // Convert old tincanlaunch links MDL-33362 & MDL-35007.
        $rules[] = new restore_decode_rule('TINCANLAUNCHSTART', '/mod/tincanlaunch/view.php?id=$1', 'course_module');

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the restore_logs_processor when restoring
     * tincanlaunch logs. It must return one array
     * of restore_log_rule objects.
     *
     * @return array
     */
    public static function define_restore_log_rules() {
        $rules = array();

        $rules[] = new restore_log_rule('tincanlaunch', 'add', 'view.php?id={course_module}', '{tincanlaunch}');
        $rules[] = new restore_log_rule('tincanlaunch', 'update', 'view.php?id={course_module}', '{tincanlaunch}');
        $rules[] = new restore_log_rule('tincanlaunch', 'view', 'view.php?id={course_module}', '{tincanlaunch}');

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the restore_logs_processor when restoring
     * course logs. It must return one array
     * of restore_log_rule objects.
     *
     * Note this rules are applied when restoring course logs
     * by the restore final task, but are defined here at
     * activity level. All them are rules not linked to any module instance (cmid = 0).
     *
     * @return array
     */
    public static function define_restore_log_rules_for_course() {
        $rules = array();

        $rules[] = new restore_log_rule('tincanlaunch', 'view all', 'index.php?id={course}', null);

        return $rules;
    }
}
