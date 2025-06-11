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
 * This file contains the definition for the library class for OneNote submission plugin
 * This class provides all the functionality for the new assign module.
 *
 * @package assignsubmission_onenote
 * @author Vinayak (Vin) Bhalerao (v-vibhal@microsoft.com) Sushant Gawali (sushant@introp.net)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  Microsoft, Inc. (based on files by NetSpot {@link http://www.netspot.com.au})
 */

use assignsubmission_onenote\event\assessable_uploaded;
use assignsubmission_onenote\event\submission_created;
use assignsubmission_onenote\event\submission_updated;
use local_onenote\api\base;

/**
 * Library class for OneNote submission plugin extending submission plugin base class
 *
 * @package   assignsubmission_onenote
 */
class assign_submission_onenote extends assign_submission_plugin {

    /**
     * Get the name of the onenote submission plugin
     *
     * @return string
     */
    public function get_name() {
        return get_string('onenote', 'assignsubmission_onenote');
    }

    /**
     * Get file submission information from the database
     *
     * @param int $submissionid
     * @return mixed
     */
    private function get_file_submission($submissionid) {
        global $DB;
        return $DB->get_record('assignsubmission_onenote', ['submission' => $submissionid]);
    }

    /**
     * Get the default setting for OneNote submission plugin.
     *
     * @param MoodleQuickForm $mform The form to add elements to
     * @return void
     */
    public function get_settings(MoodleQuickForm $mform) {
        global $CFG, $COURSE;

        $defaultmaxfilesubmissions = $this->get_config('maxfilesubmissions');
        $defaultmaxsubmissionsizebytes = $this->get_config('maxsubmissionsizebytes');

        $options = [];
        for ($i = 1; $i <= base::ASSIGNSUBMISSION_ONENOTE_MAXFILES; $i++) {
            $options[$i] = $i;
        }

        $name = get_string('maxfilessubmission', 'assignsubmission_onenote');
        $mform->addElement('select', 'assignsubmission_onenote_maxfiles', $name, $options);
        $mform->addHelpButton('assignsubmission_onenote_maxfiles', 'maxfilessubmission', 'assignsubmission_onenote');
        $mform->setDefault('assignsubmission_onenote_maxfiles', $defaultmaxfilesubmissions);
        $mform->disabledIf('assignsubmission_onenote_maxfiles', 'assignsubmission_onenote_enabled', 'notchecked');

        $choices = get_max_upload_sizes($CFG->maxbytes, $COURSE->maxbytes, get_config('assignsubmission_onenote', 'maxbytes'));

        $name = get_string('maximumsubmissionsize', 'assignsubmission_onenote');
        $mform->addElement('select', 'assignsubmission_onenote_maxsizebytes', $name, $choices);
        $mform->addHelpButton('assignsubmission_onenote_maxsizebytes', 'maximumsubmissionsize', 'assignsubmission_onenote');
        $mform->setDefault('assignsubmission_onenote_maxsizebytes', $defaultmaxsubmissionsizebytes);
        $mform->disabledIf('assignsubmission_onenote_maxsizebytes', 'assignsubmission_onenote_enabled', 'notchecked');
    }

    /**
     * Save the settings for OneNote submission plugin
     *
     * @param stdClass $data
     * @return bool
     */
    public function save_settings(stdClass $data) {
        $this->set_config('maxfilesubmissions', $data->assignsubmission_onenote_maxfiles);
        $this->set_config('maxsubmissionsizebytes', $data->assignsubmission_onenote_maxsizebytes);
        return true;
    }

    /**
     * File format options
     *
     * @return array
     */
    private function get_file_options() {
        $fileoptions = ['subdirs' => 1, 'maxbytes' => $this->get_config('maxsubmissionsizebytes'),
            'maxfiles' => $this->get_config('maxfilesubmissions'), 'accepted_types' => '*', 'return_types' => FILE_INTERNAL];
        return $fileoptions;
    }

    /**
     * Add elements to submission form
     *
     * @param mixed $submission stdClass|null
     * @param MoodleQuickForm $mform
     * @param stdClass $data
     * @return bool
     */
    public function get_form_elements($submission, MoodleQuickForm $mform, stdClass $data) {
        global $USER;

        // Check to see if one note is disabled site wide.
        if (get_config('local_o365', 'onenote')) {
            return true;
        }

        if ($this->get_config('maxfilesubmissions') <= 0) {
            return false;
        }

        try {
            $onenoteapi = base::getinstance();
        } catch (moodle_exception $e) {
            $html = '<div>' . $e->getMessage() . '</div>';
            $mform->addElement('html', $html);
            return false;
        }

        $cmid = $this->assignment->get_course_module()->id;
        $isstudent = $onenoteapi->is_student($cmid, $USER->id);

        if (!$isstudent) {
            return false;
        }
        $o = '<hr/><b>' . get_string('onenoteactions', 'assignsubmission_onenote') . '</b>';

        if ($onenoteapi->is_logged_in()) {
            // Show a button to open the OneNote page.
            $o .= $onenoteapi->render_action_button(get_string('workonthis', 'assignsubmission_onenote'),
                $this->assignment->get_course_module()->id, false, false, $submission ? $submission->userid : null,
                $submission ? $submission->id : null, null);
            $o .= '<br/><p>' . get_string('workonthishelp', 'assignsubmission_onenote') . '</p>';
        } else {
            $o .= $onenoteapi->render_signin_widget();
            $o .= '<br/><br/><p>' . get_string('signinhelp1', 'assignsubmission_onenote') . '</p>';
        }

        $o .= '<hr/>';

        $mform->addElement('html', $o);
        return true;
    }

    /**
     * Count the number of submission OneNote files
     *
     * @param int $submissionid
     * @param string $area
     * @return int
     */
    private function count_files($submissionid, $area) {

        $fs = get_file_storage();
        $files = $fs->get_area_files($this->assignment->get_context()->id, 'assignsubmission_onenote', $area, $submissionid, 'id',
            false);

        return count($files);
    }

    /**
     * Save the submission OneNote files
     *
     * @param stdClass $submission
     * @param stdClass $data
     * @return bool
     */
    public function save(stdClass $submission, stdClass $data) {
        global $USER, $DB, $COURSE;

        // Get OneNote page id.
        $record = $DB->get_record('local_onenote_assign_pages',
            ["assign_id" => $submission->assignment, "user_id" => $submission->userid]);

        if (!$record || !$record->submission_student_page_id) {
            $this->set_error(get_string('submissionnotstarted', 'assignsubmission_onenote'));
            return false;
        }

        try {
            $onenoteapi = base::getinstance();
        } catch (moodle_exception $e) {
            // Display error.
            $this->set_error($e->getMessage());
            return false;
        }

        $tempfolder = $onenoteapi->create_temp_folder();
        $tempfile = join(DIRECTORY_SEPARATOR, [rtrim($tempfolder, DIRECTORY_SEPARATOR), uniqid('asg_')]) . '.zip';

        // Create zip file containing onenote page and related files.
        $downloadinfo = $onenoteapi->download_page($record->submission_student_page_id, $tempfile);

        if (!$downloadinfo) {
            if ($onenoteapi->is_logged_in()) {
                $this->set_error(get_string('submissiondownloadfailed', 'assignsubmission_onenote'));
            } else {
                $this->set_error(get_string('notsignedin', 'assignsubmission_onenote'));
            }
            return false;
        }

        // Retrieve OneNote page metadata and save the last time modified.
        $pagemetadata = $onenoteapi->get_page_metadata($record->submission_student_page_id);
        $modtimeparam = 'lastModifiedDateTime';
        $pagemetadata = $onenoteapi->process_apicall_response($pagemetadata, [$modtimeparam => null]);
        if (!empty($pagemetadata)) {
            $record->student_lastmodified = strtotime($pagemetadata[$modtimeparam]);
            $DB->update_record('local_onenote_assign_pages', $record);
        }

        // Get assignment submission size limit.
        $submissionlimit = $this->get_config('maxsubmissionsizebytes');

        // Get submission zip size.
        $submissionsize = filesize($downloadinfo['path']);

        // Check if assignment submission limit is zero, i.e. when user selected course upload limit.
        if ($submissionlimit == 0) {
            // Check if submission size is greater than course upload limit.
            if (($COURSE->maxbytes > 0) && ($submissionsize > $COURSE->maxbytes)) {

                // Display error if true.
                $this->set_error(get_string('submissionlimitexceed', 'assignsubmission_onenote'));
                return false;
            }

            // Check if submission size is greater assignment submission limit.
        } else if ($submissionsize > $submissionlimit) {
            // Display error if true.
            $this->set_error(get_string('submissionlimitexceed', 'assignsubmission_onenote'));
            return false;
        }

        $fs = get_file_storage();

        // Delete any previous attempts.
        $fs->delete_area_files($this->assignment->get_context()->id, 'assignsubmission_onenote',
            base::ASSIGNSUBMISSION_ONENOTE_FILEAREA, $submission->id);

        // Prepare file record object.
        $fileinfo = ['contextid' => $this->assignment->get_context()->id, 'component' => 'assignsubmission_onenote',
            'filearea' => base::ASSIGNSUBMISSION_ONENOTE_FILEAREA, 'itemid' => $submission->id,
            'filepath' => '/', 'filename' => 'OneNote_' . time() . '.zip'];

        // Save it.
        $fs->create_file_from_pathname($fileinfo, $downloadinfo['path']);
        fulldelete($tempfolder);

        $filesubmission = $this->get_file_submission($submission->id);

        // Plagiarism code event trigger when files are uploaded.
        $files = $fs->get_area_files($this->assignment->get_context()->id, 'assignsubmission_onenote',
            base::ASSIGNSUBMISSION_ONENOTE_FILEAREA, $submission->id, 'id', false);

        $count = $this->count_files($submission->id, base::ASSIGNSUBMISSION_ONENOTE_FILEAREA);

        $params = ['context' => context_module::instance($this->assignment->get_course_module()->id),
            'courseid' => $this->assignment->get_course()->id, 'objectid' => $submission->id,
            'other' => ['content' => '', 'pathnamehashes' => array_keys($files)]];

        if (!empty($submission->userid) && ($submission->userid != $USER->id)) {
            $params['relateduserid'] = $submission->userid;
        }

        $event = assessable_uploaded::create($params);
        $event->set_legacy_files($files);
        $event->trigger();

        $groupname = null;
        $groupid = 0;
        // Get the group name as other fields are not transcribed in the logs and this information is important.
        if (empty($submission->userid) && !empty($submission->groupid)) {
            $groupname = $DB->get_field('groups', 'name', ['id' => $submission->groupid], '*', MUST_EXIST);
            $groupid = $submission->groupid;
        } else {
            $params['relateduserid'] = $submission->userid;
        }

        // Unset the objectid and other field from params for use in submission events.
        unset($params['objectid']);
        unset($params['other']);
        $params['other'] = ['submissionid' => $submission->id, 'submissionattempt' => $submission->attemptnumber,
            'submissionstatus' => $submission->status, 'filesubmissioncount' => $count, 'groupid' => $groupid,
            'groupname' => $groupname];

        if ($filesubmission) {
            $filesubmission->numfiles =
                $this->count_files($submission->id, base::ASSIGNSUBMISSION_ONENOTE_FILEAREA);
            $updatestatus = $DB->update_record('assignsubmission_onenote', $filesubmission);
            $params['objectid'] = $filesubmission->id;

            $event = submission_updated::create($params);
            $event->set_assign($this->assignment);
            $event->trigger();
            return $updatestatus;
        } else {
            $filesubmission = new stdClass();
            $filesubmission->numfiles =
                $this->count_files($submission->id, base::ASSIGNSUBMISSION_ONENOTE_FILEAREA);
            $filesubmission->submission = $submission->id;
            $filesubmission->assignment = $this->assignment->get_instance()->id;
            $filesubmission->id = $DB->insert_record('assignsubmission_onenote', $filesubmission);
            $params['objectid'] = $filesubmission->id;

            $event = submission_created::create($params);
            $event->set_assign($this->assignment);
            $event->trigger();
            return $filesubmission->id > 0;
        }
    }

    /**
     * Produce a list of files suitable for export that represent this feedback or submission
     *
     * @param stdClass $submission The submission
     * @param stdClass $user The user record - unused
     * @return array - return an array of files indexed by filename
     */
    public function get_files(stdClass $submission, stdClass $user) {
        $result = [];
        $fs = get_file_storage();

        $files = $fs->get_area_files($this->assignment->get_context()->id, 'assignsubmission_onenote',
            base::ASSIGNSUBMISSION_ONENOTE_FILEAREA, $submission->id, 'timemodified', false);

        foreach ($files as $file) {
            $result[$file->get_filename()] = $file;
        }
        return $result;
    }

    /**
     * Display the list of submission OneNote files in the submission status table
     *
     * @param stdClass $submission
     * @param bool $showviewlink Set this to true if the list of files is long
     * @return string
     */
    public function view_summary(stdClass $submission, &$showviewlink) {
        global $USER;

        // Should we show a link to view all files for this plugin?
        $count = $this->count_files($submission->id, base::ASSIGNSUBMISSION_ONENOTE_FILEAREA);
        $showviewlink = $count > base::ASSIGNSUBMISSION_ONENOTE_MAXSUMMARYFILES;

        try {
            $onenoteapi = base::getinstance();
        } catch (moodle_exception $e) {
            return $e->getMessage();
        }

        $isteacher = $onenoteapi->is_teacher($this->assignment->get_course_module()->id, $USER->id);
        $o = '';

        if ($count <= base::ASSIGNSUBMISSION_ONENOTE_MAXSUMMARYFILES) {
            if (($count > 0) &&
                ($isteacher || (isset($submission->status) && ($submission->status == ASSIGN_SUBMISSION_STATUS_SUBMITTED)))) {
                if ($onenoteapi->is_logged_in()) {
                    // Show a link to open the OneNote page.
                    $o .= $onenoteapi->render_action_button(get_string('viewsubmission', 'assignsubmission_onenote'),
                        $this->assignment->get_course_module()->id, false, $isteacher, $submission->userid, $submission->id, null);
                    if ($isteacher) {
                        $o .= '<p class="warning onenote_warning">' .
                            get_string('viewsubmissionwarning', 'assignsubmission_onenote') . '</p>';
                    }
                } else {
                    $o .= $onenoteapi->render_signin_widget();
                    $o .= '<br/><br/><p>' . get_string('signinhelp2', 'assignsubmission_onenote') . '</p>';
                }

                // Show standard link to download zip package.
                $o .= '<p>' . get_string('download', 'assignsubmission_onenote') . '</p>';
                $o .= $this->assignment->render_area_files('assignsubmission_onenote',
                    base::ASSIGNSUBMISSION_ONENOTE_FILEAREA, $submission->id);
            }

            return $o;
        } else {
            return get_string('countfiles', 'assignsubmission_onenote', $count);
        }
    }

    /**
     * No full submission view - the summary contains the list of files and that is the whole submission
     *
     * @param stdClass $submission
     * @return string
     */
    public function view(stdClass $submission) {
        return $this->assignment->render_area_files('assignsubmission_onenote',
            base::ASSIGNSUBMISSION_ONENOTE_FILEAREA, $submission->id);
    }

    /**
     * The assignment has been deleted - cleanup
     *
     * @return bool
     */
    public function delete_instance() {
        global $DB;
        // Will throw exception on failure.
        $DB->delete_records('assignsubmission_onenote', ['assignment' => $this->assignment->get_instance()->id]);

        return true;
    }

    /**
     * Determine whether the plugin is being added to the front page.
     *
     * @return bool Whether the plugin is being added to the front page.
     */
    protected function isonfrontpage() {
        if (!empty($this->assignment) && $this->assignment instanceof \assign) {
            $coursectx = $this->assignment->get_course_context();
            $coursectxvalid = (!empty($coursectx) && $coursectx instanceof \context_course) ? true : false;
            if ($coursectxvalid === true && $coursectx->instanceid == SITEID) {
                return true;
            }
        }
        return false;
    }

    /**
     * Automatically disable plugin if we're on the front page.
     *
     * @return bool
     */
    public function is_enabled() {
        if ($this->isonfrontpage() === true) {
            return false;
        }
        return parent::is_enabled();
    }

    /**
     * Automatically hide the setting for the submission plugin.
     *
     * @return bool
     */
    public function is_configurable() {
        if ($this->isonfrontpage() === true) {
            return false;
        }
        return parent::is_configurable();
    }

    /**
     * Return true if there are no submission OneNote files
     *
     * @param stdClass $submission
     * @return bool
     */
    public function is_empty(stdClass $submission) {
        return $this->count_files($submission->id, base::ASSIGNSUBMISSION_ONENOTE_FILEAREA) == 0;
    }

    /**
     * Get file areas returns a list of areas this plugin stores files in.
     *
     * @return array - An array of fileareas (keys) and descriptions (values)
     */
    public function get_file_areas() {
        return [base::ASSIGNSUBMISSION_ONENOTE_FILEAREA => $this->get_name()];
    }

    /**
     * Copy the student's submission from a previous submission. Used when a student opts to base their resubmission
     * on the last submission.
     *
     * @param stdClass $sourcesubmission
     * @param stdClass $destsubmission
     */
    public function copy_submission(stdClass $sourcesubmission, stdClass $destsubmission) {
        global $DB;

        // Copy the files across.
        $contextid = $this->assignment->get_context()->id;
        $fs = get_file_storage();
        $files =
            $fs->get_area_files($contextid, 'assignsubmission_onenote', base::ASSIGNSUBMISSION_ONENOTE_FILEAREA,
                $sourcesubmission->id, 'id', false);
        foreach ($files as $file) {
            $fieldupdates = ['itemid' => $destsubmission->id];
            $fs->create_file_from_storedfile($fieldupdates, $file);
        }

        // Copy the assignsubmission_file record.
        if ($filesubmission = $this->get_file_submission($sourcesubmission->id)) {
            unset($filesubmission->id);
            $filesubmission->submission = $destsubmission->id;
            $DB->insert_record('assignsubmission_onenote', $filesubmission);
        }
        return true;
    }

    /**
     * Remove files from this submission.
     *
     * @param stdClass $submission The submission
     * @return boolean
     */
    public function remove(stdClass $submission) {
        global $DB;
        $fs = get_file_storage();

        $fs->delete_area_files($this->assignment->get_context()->id, 'assignsubmission_onenote',
            base::ASSIGNSUBMISSION_ONENOTE_FILEAREA, $submission->id);

        $currentsubmission = $this->get_file_submission($submission->id);
        if ($currentsubmission) {
            $currentsubmission->numfiles = 0;
            $DB->update_record('assignsubmission_onenote', $currentsubmission);
        }

        return true;
    }
}
