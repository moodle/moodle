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
 * @package mod_questionnaire
 * @copyright  2016 Mike Churchward (mike.churchward@poetgroup.org)
 * @author     Mike Churchward
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Because it exists (must).
require_once($CFG->dirroot . '/mod/questionnaire/backup/moodle2/restore_questionnaire_stepslib.php');

/**
 * questionnaire restore task that provides all the settings and steps to perform one
 * complete restore of the activity
 */
class restore_questionnaire_activity_task extends restore_activity_task {

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
        $this->add_step(new restore_questionnaire_activity_structure_step('questionnaire_structure', 'questionnaire.xml'));
    }

    /**
     * Define the contents in the activity that must be
     * processed by the link decoder
     */
    static public function define_decode_contents() {
        $contents = array();

        $contents[] = new restore_decode_content('questionnaire', array('intro'), 'questionnaire');
        $contents[] = new restore_decode_content('questionnaire_survey',
                        array('info', 'thank_head', 'thank_body', 'thanks_page'), 'questionnaire_survey');
        $contents[] = new restore_decode_content('questionnaire_question', array('content'), 'questionnaire_question');

        return $contents;
    }

    /**
     * Define the decoding rules for links belonging
     * to the activity to be executed by the link decoder
     */
    static public function define_decode_rules() {
        $rules = array();

        $rules[] = new restore_decode_rule('QUESTIONNAIREVIEWBYID', '/mod/questionnaire/view.php?id=$1', 'course_module');
        $rules[] = new restore_decode_rule('QUESTIONNAIREINDEX', '/mod/questionnaire/index.php?id=$1', 'course');

        return $rules;

    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * questionnaire logs. It must return one array
     * of {@link restore_log_rule} objects
     */
    static public function define_restore_log_rules() {
        $rules = array();

        $rules[] = new restore_log_rule('questionnaire', 'add', 'view.php?id={course_module}', '{questionnaire}');
        $rules[] = new restore_log_rule('questionnaire', 'update', 'view.php?id={course_module}', '{questionnaire}');
        $rules[] = new restore_log_rule('questionnaire', 'view', 'view.php?id={course_module}', '{questionnaire}');
        $rules[] = new restore_log_rule('questionnaire', 'choose', 'view.php?id={course_module}', '{questionnaire}');
        $rules[] = new restore_log_rule('questionnaire', 'choose again', 'view.php?id={course_module}', '{questionnaire}');
        $rules[] = new restore_log_rule('questionnaire', 'report', 'report.php?id={course_module}', '{questionnaire}');

        return $rules;
    }

    /**
     * Define the restore log rules that will be applied
     * by the {@link restore_logs_processor} when restoring
     * course logs. It must return one array
     * of {@link restore_log_rule} objects
     *
     * Note this rules are applied when restoring course logs
     * by the restore final task, but are defined here at
     * activity level. All them are rules not linked to any module instance (cmid = 0)
     */
    static public function define_restore_log_rules_for_course() {
        $rules = array();

        // Fix old wrong uses (missing extension).
        $rules[] = new restore_log_rule('questionnaire', 'view all', 'index?id={course}', null,
                                        null, null, 'index.php?id={course}');
        $rules[] = new restore_log_rule('questionnaire', 'view all', 'index.php?id={course}', null);

        return $rules;
    }
}
