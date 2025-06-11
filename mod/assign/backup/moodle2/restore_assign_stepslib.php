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
 * Define all the restore steps that will be used by the restore_assign_activity_task
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/assign/locallib.php');

/**
 * Define the complete assignment structure for restore, with file and id annotations
 *
 * @package   mod_assign
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_assign_activity_structure_step extends restore_activity_structure_step {

    /**
     * Store whether submission details should be included. Details may not be included if the
     * this is a team submission, but groups/grouping information was not included in the backup.
     */
    protected $includesubmission = true;

    /**
     * Define the structure of the restore workflow.
     *
     * @return restore_path_element $structure
     */
    protected function define_structure() {

        $paths = array();
        // To know if we are including userinfo.
        $userinfo = $this->get_setting_value('userinfo');

        // Define each element separated.
        $paths[] = new restore_path_element('assign', '/activity/assign');
        if ($userinfo) {
            $submission = new restore_path_element('assign_submission',
                                                   '/activity/assign/submissions/submission');
            $paths[] = $submission;
            $this->add_subplugin_structure('assignsubmission', $submission);
            $grade = new restore_path_element('assign_grade', '/activity/assign/grades/grade');
            $paths[] = $grade;
            $this->add_subplugin_structure('assignfeedback', $grade);
            $userflag = new restore_path_element('assign_userflag',
                                                   '/activity/assign/userflags/userflag');
            $paths[] = $userflag;
        }

        $paths[] = new restore_path_element('assign_override', '/activity/assign/overrides/override');
        $paths[] = new restore_path_element('assign_plugin_config',
                                            '/activity/assign/plugin_configs/plugin_config');

        return $this->prepare_activity_structure($paths);
    }

    /**
     * Process an assign restore.
     *
     * @param object $data The data in object form
     * @return void
     */
    protected function process_assign($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        $data->course = $this->get_courseid();

        // Any changes to the list of dates that needs to be rolled should be same during course restore and course reset.
        // See MDL-9367.
        $data->allowsubmissionsfromdate = $this->apply_date_offset($data->allowsubmissionsfromdate);
        $data->duedate = $this->apply_date_offset($data->duedate);

        // If this is a team submission, but there is no group info we need to flag that the submission
        // information should not be included. It should not be restored.
        $groupinfo = $this->task->get_setting_value('groups');
        if ($data->teamsubmission && !$groupinfo) {
            $this->includesubmission = false;
        }

        // Reset revealidentities if blindmarking with no user data (MDL-43796).
        $userinfo = $this->get_setting_value('userinfo');
        if (!$userinfo && $data->blindmarking) {
            $data->revealidentities = 0;
        }

        if (!empty($data->teamsubmissiongroupingid)) {
            $data->teamsubmissiongroupingid = $this->get_mappingid('grouping',
                                                                   $data->teamsubmissiongroupingid);
        } else {
            $data->teamsubmissiongroupingid = 0;
        }
        if (!isset($data->cutoffdate)) {
            $data->cutoffdate = 0;
        }
        if (!isset($data->gradingduedate)) {
            $data->gradingduedate = 0;
        } else {
            $data->gradingduedate = $this->apply_date_offset($data->gradingduedate);
        }
        if (!isset($data->markingworkflow)) {
            $data->markingworkflow = 0;
        }
        if (!isset($data->markingallocation)) {
            $data->markingallocation = 0;
        }
        if (!isset($data->markinganonymous)) {
            $data->markinganonymous = 0;
        }
        if (!isset($data->preventsubmissionnotingroup)) {
            $data->preventsubmissionnotingroup = 0;
        }

        if (!empty($data->preventlatesubmissions)) {
            $data->cutoffdate = $data->duedate;
        } else {
            $data->cutoffdate = $this->apply_date_offset($data->cutoffdate);
        }

        if ($data->grade < 0) { // Scale found, get mapping.
            $data->grade = -($this->get_mappingid('scale', abs($data->grade)));
        }

        $newitemid = $DB->insert_record('assign', $data);

        $this->apply_activity_instance($newitemid);
    }

    /**
     * Process a submission restore
     * @param object $data The data in object form
     * @return void
     */
    protected function process_assign_submission($data) {
        global $DB;

        if (!$this->includesubmission) {
            return;
        }

        $data = (object)$data;
        $oldid = $data->id;

        $data->assignment = $this->get_new_parentid('assign');

        if ($data->userid > 0) {
            $data->userid = $this->get_mappingid('user', $data->userid);
        }
        if (!empty($data->groupid)) {
            $data->groupid = $this->get_mappingid('group', $data->groupid);
            if (!$data->groupid) {
                // If the group does not exist, then the submission cannot be viewed and restoring can
                // violate the unique index on the submission table.
                return;
            }
        } else {
            $data->groupid = 0;
        }

        // We will correct this in set_latest_submission_field() once all submissions are restored.
        $data->latest = 0;

        $newitemid = $DB->insert_record('assign_submission', $data);

        // Note - the old contextid is required in order to be able to restore files stored in
        // sub plugin file areas attached to the submissionid.
        $this->set_mapping('submission', $oldid, $newitemid, false, null, $this->task->get_old_contextid());
    }

    /**
     * Process a user_flags restore
     * @param object $data The data in object form
     * @return void
     */
    protected function process_assign_userflag($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->assignment = $this->get_new_parentid('assign');

        $data->userid = $this->get_mappingid('user', $data->userid);
        if (!empty($data->allocatedmarker)) {
            $data->allocatedmarker = $this->get_mappingid('user', $data->allocatedmarker);
        }
        if (!empty($data->extensionduedate)) {
            $data->extensionduedate = $this->apply_date_offset($data->extensionduedate);
        } else {
            $data->extensionduedate = 0;
        }
        // Flags mailed and locked need no translation on restore.

        $newitemid = $DB->insert_record('assign_user_flags', $data);
    }

    /**
     * Process a grade restore
     * @param object $data The data in object form
     * @return void
     */
    protected function process_assign_grade($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->assignment = $this->get_new_parentid('assign');

        $data->userid = $this->get_mappingid('user', $data->userid);
        $data->grader = $this->get_mappingid('user', $data->grader);

        // Handle flags restore to a different table (for upgrade from old backups).
        if (!empty($data->extensionduedate) ||
                !empty($data->mailed) ||
                !empty($data->locked)) {
            $flags = new stdClass();
            $flags->assignment = $this->get_new_parentid('assign');
            if (!empty($data->extensionduedate)) {
                $flags->extensionduedate = $this->apply_date_offset($data->extensionduedate);
            }
            if (!empty($data->mailed)) {
                $flags->mailed = $data->mailed;
            }
            if (!empty($data->locked)) {
                $flags->locked = $data->locked;
            }
            $flags->userid = $this->get_mappingid('user', $data->userid);
            $DB->insert_record('assign_user_flags', $flags);
        }
        // Fix null grades that were rescaled.
        if ($data->grade < 0 && $data->grade != ASSIGN_GRADE_NOT_SET) {
            $data->grade = ASSIGN_GRADE_NOT_SET;
        }
        $newitemid = $DB->insert_record('assign_grades', $data);

        // Note - the old contextid is required in order to be able to restore files stored in
        // sub plugin file areas attached to the gradeid.
        $this->set_mapping('grade', $oldid, $newitemid, false, null, $this->task->get_old_contextid());
        $this->set_mapping(restore_gradingform_plugin::itemid_mapping('submissions'), $oldid, $newitemid);
    }

    /**
     * Process a plugin-config restore
     * @param object $data The data in object form
     * @return void
     */
    protected function process_assign_plugin_config($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        $data->assignment = $this->get_new_parentid('assign');

        $newitemid = $DB->insert_record('assign_plugin_config', $data);
    }

    /**
     * For all submissions in this assignment, either set the
     * submission->latest field to 1 for the latest attempts
     * or create a new submission record for grades with no submission.
     *
     * @return void
     */
    protected function set_latest_submission_field() {
        global $DB, $CFG;

        // Required for constants.
        require_once($CFG->dirroot . '/mod/assign/locallib.php');

        $assignmentid = $this->get_new_parentid('assign');

        // First check for records with a grade, but no submission record.
        // This happens when a teacher marks a student before they have submitted anything.
        $records = $DB->get_recordset_sql('SELECT g.id, g.userid, g.attemptnumber
                                           FROM {assign_grades} g
                                      LEFT JOIN {assign_submission} s
                                             ON s.assignment = g.assignment
                                            AND s.userid = g.userid
                                          WHERE s.id IS NULL AND g.assignment = ?', array($assignmentid));

        $submissions = array();
        foreach ($records as $record) {
            $submission = new stdClass();
            $submission->assignment = $assignmentid;
            $submission->userid = $record->userid;
            $submission->attemptnumber = $record->attemptnumber;
            $submission->status = ASSIGN_SUBMISSION_STATUS_NEW;
            $submission->groupid = 0;
            $submission->latest = 0;
            $submission->timecreated = time();
            $submission->timemodified = time();
            array_push($submissions, $submission);
        }

        $records->close();

        $DB->insert_records('assign_submission', $submissions);

        // This code could be rewritten as a monster SQL - but the point of adding this "latest" field
        // to the submissions table in the first place was to get away from those hard to maintain SQL queries.

        // First user submissions.
        $sql = 'SELECT DISTINCT userid FROM {assign_submission} WHERE assignment = ? AND groupid = ?';
        $params = array($assignmentid, 0);
        $users = $DB->get_records_sql($sql, $params);

        foreach ($users as $userid => $unused) {
            $params = array('assignment'=>$assignmentid, 'groupid'=>0, 'userid'=>$userid);

            // Only return the row with the highest attemptnumber.
            $submission = null;
            $submissions = $DB->get_records('assign_submission', $params, 'attemptnumber DESC', '*', 0, 1);
            if ($submissions) {
                $submission = reset($submissions);
                $submission->latest = 1;
                $DB->update_record('assign_submission', $submission);
            }
        }
        // Then group submissions (if any).
        $sql = 'SELECT DISTINCT groupid FROM {assign_submission} WHERE assignment = ? AND userid = ?';
        $params = array($assignmentid, 0);
        $groups = $DB->get_records_sql($sql, $params);

        foreach ($groups as $groupid => $unused) {
            $params = array('assignment'=>$assignmentid, 'userid'=>0, 'groupid'=>$groupid);

            // Only return the row with the highest attemptnumber.
            $submission = null;
            $submissions = $DB->get_records('assign_submission', $params, 'attemptnumber DESC', '*', 0, 1);
            if ($submissions) {
                $submission = reset($submissions);
                $submission->latest = 1;
                $DB->update_record('assign_submission', $submission);
            }
        }
    }

    /**
     * Restore files from plugin configuration
     * @param string $subtype the plugin type to handle
     * @return void
     */
    protected function add_plugin_config_files($subtype) {
        $dummyassign = new assign(null, null, null);
        $plugins = $dummyassign->load_plugins($subtype);
        foreach ($plugins as $plugin) {
            $component = $plugin->get_subtype() . '_' . $plugin->get_type();
            $areas = $plugin->get_config_file_areas();
            foreach ($areas as $area) {
                $this->add_related_files($component, $area, null);
            }
        }
    }

    /**
     * Process a assign override restore
     * @param object $data The data in object form
     * @return void
     */
    protected function process_assign_override($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        // Based on userinfo, we'll restore user overides or no.
        $userinfo = $this->get_setting_value('userinfo');

        // Skip user overrides if we are not restoring userinfo.
        if (!$userinfo && !is_null($data->userid)) {
            return;
        }

        // Skip group overrides if we are not restoring groupinfo.
        $groupinfo = $this->get_setting_value('groups');
        if (!$groupinfo && !is_null($data->groupid)) {
            return;
        }

        $data->assignid = $this->get_new_parentid('assign');

        if (!is_null($data->userid)) {
            $data->userid = $this->get_mappingid('user', $data->userid);
        }
        if (!is_null($data->groupid)) {
            $data->groupid = $this->get_mappingid('group', $data->groupid);
        }

        $data->allowsubmissionsfromdate = $this->apply_date_offset($data->allowsubmissionsfromdate);
        $data->duedate = $this->apply_date_offset($data->duedate);
        $data->cutoffdate = $this->apply_date_offset($data->cutoffdate);

        $newitemid = $DB->insert_record('assign_overrides', $data);

        // Add mapping, restore of logs needs it.
        $this->set_mapping('assign_override', $oldid, $newitemid);
    }

    /**
     * Once the database tables have been fully restored, restore the files
     */
    protected function after_execute() {
        $this->add_related_files('mod_assign', 'intro', null);
        $this->add_related_files('mod_assign', 'introattachment', null);
        $this->add_related_files('mod_assign', 'activityattachment', null);

        $this->add_plugin_config_files('assignsubmission');
        $this->add_plugin_config_files('assignfeedback');

        $this->set_latest_submission_field();
    }
}
