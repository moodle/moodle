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
 * Restore a realtimequiz.
 * @package mod_realtimequiz
 * @subpackage backup-moodle2
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the restore steps that will be used by the restore_realtimequiz_activity_task
 */

/**
 * Structure step to restore one realtimequiz activity
 */
class restore_realtimequiz_activity_structure_step extends restore_activity_structure_step {

    /**
     * Define the restore structure
     * @return mixed
     * @throws base_step_exception
     */
    protected function define_structure() {

        $paths = [];
        $userinfo = $this->get_setting_value('userinfo');

        $paths[] = new restore_path_element('realtimequiz', '/activity/realtimequiz');
        $paths[] = new restore_path_element('realtimequiz_question', '/activity/realtimequiz/questions/question');
        $paths[] = new restore_path_element('realtimequiz_answer',
                                            '/activity/realtimequiz/questions/question/answers/answer');
        if ($userinfo) {
            $paths[] = new restore_path_element('realtimequiz_session', '/activity/realtimequiz/sessions/session');
            $paths[] = new restore_path_element('realtimequiz_submission',
                                                '/activity/realtimequiz/questions/question/submissions/submission');
        }

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Create a realtimequiz record.
     * @param object|array $data
     * @throws base_step_exception
     * @throws dml_exception
     */
    protected function process_realtimequiz($data) {
        global $DB;

        $data = (object)$data;
        $data->course = $this->get_courseid();

        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('realtimequiz', $data);
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Create a question record.
     * @param object|array $data
     * @throws base_step_exception
     * @throws dml_exception
     * @throws restore_step_exception
     */
    protected function process_realtimequiz_question($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();
        $data->quizid = $this->get_new_parentid('realtimequiz');

        $newitemid = $DB->insert_record('realtimequiz_question', $data);
        $this->set_mapping('realtimequiz_question', $oldid, $newitemid, true);
    }

    /**
     * Create an answer record.
     * @param object|array $data
     * @throws dml_exception
     * @throws restore_step_exception
     */
    protected function process_realtimequiz_answer($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->questionid = $this->get_new_parentid('realtimequiz_question');

        $newitemid = $DB->insert_record('realtimequiz_answer', $data);
        $this->set_mapping('realtimequiz_answer', $oldid, $newitemid);
    }

    /**
     * Create a session record
     * @param object|array $data
     * @throws dml_exception
     * @throws restore_step_exception
     */
    protected function process_realtimequiz_session($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->quizid = $this->get_new_parentid('realtimequiz');
        $data->timestamp = $this->apply_date_offset($data->timestamp);

        $newitemid = $DB->insert_record('realtimequiz_session', $data);
        $this->set_mapping('realtimequiz_session', $oldid, $newitemid, true);
    }

    /**
     * Create a submission record.
     * @param object|array $data
     * @throws dml_exception
     */
    protected function process_realtimequiz_submission($data) {
        global $DB;

        $data = (object)$data;

        $data->questionid = $this->get_new_parentid('realtimequiz_question');
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->answerid = $this->get_mappingid('realtimequiz_answer', $data->answerid);

        if ($data->answerid) {
            // Skip any user responses that don't match an existing answer.
            $DB->insert_record('realtimequiz_submitted', $data);
        }
    }

    /**
     * Extra steps after we've finished the restore.
     */
    protected function after_execute() {
        // Add question files.
        $this->add_related_files('mod_realtimequiz', 'question', 'realtimequiz_question');
    }
}
