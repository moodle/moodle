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
 * @package moodlecore
 * @subpackage backup-moodle2
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the restore steps that will be used by the restore_quiz_activity_task
 */

/**
 * Structure step to restore one quiz activity
 */
class restore_quiz_activity_structure_step extends restore_questions_activity_structure_step {

    protected function define_structure() {

        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('quiz', '/activity/quiz');
        $paths[] = new restore_path_element('quiz_question_instance', '/activity/quiz/question_instances/question_instance');
        $paths[] = new restore_path_element('quiz_feedback', '/activity/quiz/feedbacks/feedback');
        $paths[] = new restore_path_element('quiz_override', '/activity/quiz/overrides/override');
        if ($userinfo) {
            $paths[] = new restore_path_element('quiz_grade', '/activity/quiz/grades/grade');
            $quizattempt = new restore_path_element('quiz_attempt', '/activity/quiz/attempts/attempt');
            $paths[] = $quizattempt;
            // Add states and sessions
            $this->add_question_attempts_states($quizattempt, $paths);
            $this->add_question_attempts_sessions($quizattempt, $paths);
        }

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_quiz($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        $data->timeopen = $this->apply_date_offset($data->timeopen);
        $data->timeclose = $this->apply_date_offset($data->timeclose);
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $data->questions = $this->questions_recode_layout($data->questions);

        // insert the quiz record
        $newitemid = $DB->insert_record('quiz', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }

    protected function process_quiz_question_instance($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->quiz = $this->get_new_parentid('quiz');

        $data->question = $this->get_mappingid('question', $data->question);

        $DB->insert_record('quiz_question_instances', $data);
    }

    protected function process_quiz_feedback($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->quizid = $this->get_new_parentid('quiz');

        $newitemid = $DB->insert_record('quiz_feedback', $data);
        $this->set_mapping('quiz_feedback', $oldid, $newitemid, true); // Has related files
    }

    protected function process_quiz_override($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // Based on userinfo, we'll restore user overides or no
        $userinfo = $this->get_setting_value('userinfo');

        // Skip user overrides if we are not restoring userinfo
        if (!$userinfo && !is_null($data->userid)) {
            return;
        }

        $data->quiz = $this->get_new_parentid('quiz');

        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->groupid = $this->get_mappingid('group', $data->groupid);

        $data->timeopen = $this->apply_date_offset($data->timeopen);
        $data->timeclose = $this->apply_date_offset($data->timeclose);

        $newitemid = $DB->insert_record('quiz_overrides', $data);

        // Add mapping, restore of logs needs it
        $this->set_mapping('quiz_override', $oldid, $newitemid);
    }

    protected function process_quiz_grade($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->quiz = $this->get_new_parentid('quiz');

        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->grade = $data->gradeval;

        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $DB->insert_record('quiz_grades', $data);
    }

    protected function process_quiz_attempt($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $olduniqueid = $data->uniqueid;

        $data->quiz = $this->get_new_parentid('quiz');
        $data->attempt = $data->attemptnum;

        $data->uniqueid = question_new_attempt_uniqueid('quiz');

        $data->userid = $this->get_mappingid('user', $data->userid);

        $data->timestart = $this->apply_date_offset($data->timestart);
        $data->timefinish = $this->apply_date_offset($data->timefinish);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $data->layout = $this->questions_recode_layout($data->layout);

        $newitemid = $DB->insert_record('quiz_attempts', $data);

        // Save quiz_attempt->uniqueid as quiz_attempt mapping, both question_states and
        // question_sessions have Fk to it and not to quiz_attempts->id at all.
        $this->set_mapping('quiz_attempt', $olduniqueid, $data->uniqueid, false);
        // Also save quiz_attempt->id mapping, because logs use it
        $this->set_mapping('quiz_attempt_id', $oldid, $newitemid, false);
    }

    protected function after_execute() {
        // Add quiz related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_quiz', 'intro', null);
        // Add feedback related files, matching by itemname = 'quiz_feedback'
        $this->add_related_files('mod_quiz', 'feedback', 'quiz_feedback');
    }
}
