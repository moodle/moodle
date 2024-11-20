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
 * Restore a realtimequiz
 * @package mod_realtimequiz
 * @subpackage backup-moodle2
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;
require_once($CFG->dirroot.'/mod/realtimequiz/backup/moodle2/restore_realtimequiz_stepslib.php'); // Because it exists (must).

/**
 * realtimequiz restore task that provides all the settings and steps to perform one complete restore of the activity
 */
class restore_realtimequiz_activity_task extends restore_activity_task {

    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity.
    }

    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // Choice only has one structure step.
        $this->add_step(new restore_realtimequiz_activity_structure_step('realtimequiz_structure', 'realtimequiz.xml'));
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder
     */
    public static function define_decode_contents() {
        $contents = [];

        $contents[] = new restore_decode_content('realtimequiz', ['intro']);
        $contents[] = new restore_decode_content('realtimequiz_question', ['questiontext']);

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder
     */
    public static function define_decode_rules() {
        $rules = [];

        // List of realtimequizs in course.
        $rules[] = new restore_decode_rule('REALTIMEQUIZINDEX', '/mod/realtimequiz/index.php?id=$1', 'course');
        // Realtimequiz by cm->id and realtimequiz->id.
        $rules[] = new restore_decode_rule('REALTIMEQUIZVIEWBYID', '/mod/realtimequiz/view.php?id=$1', 'course_module');

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the restore_logs_processor when restoring
     * realtimequiz logs. It must return one array
     * of restore_log_rule objects
     * @return restore_log_rule[]
     */
    public static function define_restore_log_rules() {
        $rules = [];

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the restore_logs_processor when restoring
     * course logs. It must return one array
     * of restore_log_rule objects
     *
     * Note this rules are applied when restoring course logs
     * by the restore final task, but are defined here at
     * activity level. All them are rules not linked to any module instance (cmid = 0)
     * @return restore_log_rule[]
     */
    public static function define_restore_log_rules_for_course() {
        $rules = [];

        return $rules;
    }
}
