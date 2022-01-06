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
 * The task that provides a complete restore of mod_googlemeet is defined here.
 *
 * @package     mod_googlemeet
 * @subpackage  backup-moodle2
 * @copyright   2020 Rone Santos <ronefel@hotmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'//mod/googlemeet/backup/moodle2/restore_googlemeet_stepslib.php');

/**
 * Restore task for mod_googlemeet.
 */
class restore_googlemeet_activity_task extends restore_activity_task {

    /**
     * Defines particular settings that this activity can have.
     */
    protected function define_my_settings() {
        return;
    }

    /**
     * Defines particular steps that this activity can have.
     *
     * @return base_step.
     */
    protected function define_my_steps() {
        $this->add_step(new restore_googlemeet_activity_structure_step('googlemeet_structure', 'googlemeet.xml'));
    }

    /**
     * Defines the contents in the activity that must be processed by the link decoder.
     *
     * @return array.
     */
    static public function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('googlemeet', array('intro'), 'googlemeet');

        return $contents;
    }

    /**
     * Defines the decoding rules for links belonging to the activity to be executed by the link decoder.
     *
     * @return array.
     */
    static public function define_decode_rules() {
        $rules = array();

        $rules[] = new restore_decode_rule('GOOGLEMEETINDEX', '/mod/googlemeet/index.php?id=$1', 'course');
        $rules[] = new restore_decode_rule('GOOGLEMEETVIEWBYID', '/mod/googlemeet/view.php?id=$1', 'course_module');

        return $rules;
    }

    /**
     * Defines the restore log rules that will be applied by the restore_logs_processor
     * when restoring mod_googlemeet logs.
     * It must return one array of restore_log_rule objects.
     *
     * @return array.
     */
    static public function define_restore_log_rules() {
        $rules = array();

        $rules[] = new restore_log_rule('googlemeet', 'add', 'view.php?id={course_module}', '{googlemeet}');
        $rules[] = new restore_log_rule('googlemeet', 'update', 'view.php?id={course_module}', '{googlemeet}');
        $rules[] = new restore_log_rule('googlemeet', 'view', 'view.php?id={course_module}', '{googlemeet}');

        return $rules;
    }
}
