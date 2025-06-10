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
 * Structure step to restore one adaptivequiz activity.
 *
 * @copyright  2013 onwards Remote-Learner {@link http://www.remote-learner.ca/}
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class restore_adaptivequiz_activity_structure_step extends restore_questions_activity_structure_step {

    /**
     * Define the a structure for restoring the activity
     * @return backup_nested_element the $activitystructure wrapped by the common 'activity' element
     */
    protected function define_structure() {
        $paths = array();
        $userinfo = $this->get_setting_value('userinfo');

        $adaptivequiz = new restore_path_element('adaptivequiz', '/activity/adaptivequiz');
        $paths[] = $adaptivequiz;

        $paths[] = new restore_path_element('adaptivequiz_question',
            '/activity/adaptivequiz/adatpivequestioncats/adatpivequestioncat');

        if ($userinfo) {
            $attempt = new restore_path_element('adaptivequiz_attempt', '/activity/adaptivequiz/adaptiveattempts/adaptiveattempt');
            $paths[] = $attempt;

            // Add states and sessions.
            $this->add_question_usages($attempt, $paths);
        }

        // Return the paths wrapped into standard activity structure.
        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process the adaptivequiz element
     * @param stdClass an object whose properties are nodes in the adatpviequiz structure
     */
    protected function process_adaptivequiz($data) {
        global $CFG, $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);
        // Insert the quiz record.
        $newitemid = $DB->insert_record('adaptivequiz', $data);
        // Immediately after inserting "activity" record, call this.
        $this->apply_activity_instance($newitemid);
    }

    /**
     * Process the activity instance to question categories relation structure\
     * @param stdClass an object whose properties are nodes in the adatpviequiz_question structure
     */
    protected function process_adaptivequiz_question($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->instance = $this->get_new_parentid('adaptivequiz');
        // Check if catid is not empty and update the record with the new category id.
        $catid = $this->get_mappingid('question_category', $data->questioncategory);
        if (!empty($catid)) {
            $data->questioncategory = $catid;
        }
        $DB->insert_record('adaptivequiz_question', $data);
    }

    /**
     * Process the activity instance to question categories relation structure
     * @param stdClass an object whose properties are nodes in the adatpviequiz_attempt structure
     */
    protected function process_adaptivequiz_attempt($data) {
        $data = (object)$data;
        $oldid = $data->id;

        $data->instance = $this->get_new_parentid('adaptivequiz');
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // The data is actually inserted into the database later in inform_new_usage_id.
        $this->currentadatpivequizattempt = clone($data);
    }

    /**
     * This function assigns the new question usage by activity id to the attempt
     * @param int $newusageid a new question usage by activity id
     */
    protected function inform_new_usage_id($newusageid) {
        global $DB;

        $data = $this->currentadatpivequizattempt;

        $oldid = $data->id;
        $data->uniqueid = $newusageid;

        $newitemid = $DB->insert_record('adaptivequiz_attempt', $data);

        // Save quiz_attempt->id mapping, because logs use it. (logs will be implemented latter).
        $this->set_mapping('adaptivequiz_attempt', $oldid, $newitemid, false);
    }

    /**
     * This function adds any files assocaited with the intro field after the restore process has run
     */
    protected function after_execute() {
        parent::after_execute();
        // Add quiz related files, no need to match by itemname (just internally handled context).
        $this->add_related_files('mod_adaptivequiz', 'intro', null);
    }
}
