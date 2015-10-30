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
 * @package   mod_workshop
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the restore steps that will be used by the restore_workshop_activity_task
 */

/**
 * Structure step to restore one workshop activity
 */
class restore_workshop_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();

        $userinfo = $this->get_setting_value('userinfo'); // are we including userinfo?

        ////////////////////////////////////////////////////////////////////////
        // XML interesting paths - non-user data
        ////////////////////////////////////////////////////////////////////////

        // root element describing workshop instance
        $workshop = new restore_path_element('workshop', '/activity/workshop');
        $paths[] = $workshop;

        // Apply for 'workshopform' subplugins optional paths at workshop level
        $this->add_subplugin_structure('workshopform', $workshop);

        // Apply for 'workshopeval' subplugins optional paths at workshop level
        $this->add_subplugin_structure('workshopeval', $workshop);

        // example submissions
        $paths[] = new restore_path_element('workshop_examplesubmission',
                       '/activity/workshop/examplesubmissions/examplesubmission');

        // reference assessment of the example submission
        $referenceassessment = new restore_path_element('workshop_referenceassessment',
                                   '/activity/workshop/examplesubmissions/examplesubmission/referenceassessment');
        $paths[] = $referenceassessment;

        // Apply for 'workshopform' subplugins optional paths at referenceassessment level
        $this->add_subplugin_structure('workshopform', $referenceassessment);

        // End here if no-user data has been selected
        if (!$userinfo) {
            return $this->prepare_activity_structure($paths);
        }

        ////////////////////////////////////////////////////////////////////////
        // XML interesting paths - user data
        ////////////////////////////////////////////////////////////////////////

        // assessments of example submissions
        $exampleassessment = new restore_path_element('workshop_exampleassessment',
                                 '/activity/workshop/examplesubmissions/examplesubmission/exampleassessments/exampleassessment');
        $paths[] = $exampleassessment;

        // Apply for 'workshopform' subplugins optional paths at exampleassessment level
        $this->add_subplugin_structure('workshopform', $exampleassessment);

        // submissions
        $paths[] = new restore_path_element('workshop_submission', '/activity/workshop/submissions/submission');

        // allocated assessments
        $assessment = new restore_path_element('workshop_assessment',
                          '/activity/workshop/submissions/submission/assessments/assessment');
        $paths[] = $assessment;

        // Apply for 'workshopform' subplugins optional paths at assessment level
        $this->add_subplugin_structure('workshopform', $assessment);

        // aggregations of grading grades in this workshop
        $paths[] = new restore_path_element('workshop_aggregation', '/activity/workshop/aggregations/aggregation');

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_workshop($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        $data->timemodified = $this->apply_date_offset($data->timemodified);
        $data->submissionstart = $this->apply_date_offset($data->submissionstart);
        $data->submissionend = $this->apply_date_offset($data->submissionend);
        $data->assessmentstart = $this->apply_date_offset($data->assessmentstart);
        $data->assessmentend = $this->apply_date_offset($data->assessmentend);

        // insert the workshop record
        $newitemid = $DB->insert_record('workshop', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }

    protected function process_workshop_examplesubmission($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->workshopid = $this->get_new_parentid('workshop');
        $data->example = 1;
        $data->authorid = $this->task->get_userid();
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('workshop_submissions', $data);
        $this->set_mapping('workshop_examplesubmission', $oldid, $newitemid, true); // Mapping with files
    }

    protected function process_workshop_referenceassessment($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->submissionid = $this->get_new_parentid('workshop_examplesubmission');
        $data->reviewerid = $this->task->get_userid();
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('workshop_assessments', $data);
        $this->set_mapping('workshop_referenceassessment', $oldid, $newitemid, true); // Mapping with files
    }

    protected function process_workshop_exampleassessment($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->submissionid = $this->get_new_parentid('workshop_examplesubmission');
        $data->reviewerid = $this->get_mappingid('user', $data->reviewerid);
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('workshop_assessments', $data);
        $this->set_mapping('workshop_exampleassessment', $oldid, $newitemid, true); // Mapping with files
    }

    protected function process_workshop_submission($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->workshopid = $this->get_new_parentid('workshop');
        $data->example = 0;
        $data->authorid = $this->get_mappingid('user', $data->authorid);
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('workshop_submissions', $data);
        $this->set_mapping('workshop_submission', $oldid, $newitemid, true); // Mapping with files
    }

    protected function process_workshop_assessment($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->submissionid = $this->get_new_parentid('workshop_submission');
        $data->reviewerid = $this->get_mappingid('user', $data->reviewerid);
        $data->timecreated = $this->apply_date_offset($data->timecreated);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('workshop_assessments', $data);
        $this->set_mapping('workshop_assessment', $oldid, $newitemid, true); // Mapping with files
    }

    protected function process_workshop_aggregation($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->workshopid = $this->get_new_parentid('workshop');
        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->timegraded = $this->apply_date_offset($data->timegraded);

        $newitemid = $DB->insert_record('workshop_aggregations', $data);
    }

    protected function after_execute() {
        // Add workshop related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_workshop', 'intro', null);
        $this->add_related_files('mod_workshop', 'instructauthors', null);
        $this->add_related_files('mod_workshop', 'instructreviewers', null);
        $this->add_related_files('mod_workshop', 'conclusion', null);

        // Add example submission related files, matching by 'workshop_examplesubmission' itemname
        $this->add_related_files('mod_workshop', 'submission_content', 'workshop_examplesubmission');
        $this->add_related_files('mod_workshop', 'submission_attachment', 'workshop_examplesubmission');

        // Add reference assessment related files, matching by 'workshop_referenceassessment' itemname
        $this->add_related_files('mod_workshop', 'overallfeedback_content', 'workshop_referenceassessment');
        $this->add_related_files('mod_workshop', 'overallfeedback_attachment', 'workshop_referenceassessment');

        // Add example assessment related files, matching by 'workshop_exampleassessment' itemname
        $this->add_related_files('mod_workshop', 'overallfeedback_content', 'workshop_exampleassessment');
        $this->add_related_files('mod_workshop', 'overallfeedback_attachment', 'workshop_exampleassessment');

        // Add submission related files, matching by 'workshop_submission' itemname
        $this->add_related_files('mod_workshop', 'submission_content', 'workshop_submission');
        $this->add_related_files('mod_workshop', 'submission_attachment', 'workshop_submission');

        // Add assessment related files, matching by 'workshop_assessment' itemname
        $this->add_related_files('mod_workshop', 'overallfeedback_content', 'workshop_assessment');
        $this->add_related_files('mod_workshop', 'overallfeedback_attachment', 'workshop_assessment');
    }
}
