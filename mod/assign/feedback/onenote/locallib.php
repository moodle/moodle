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
 * This file contains the definition for the library class for onenote feedback plugin
 *
 * @package   assignfeedback_onenote
 * @author Vinayak (Vin) Bhalerao (v-vibhal@microsoft.com)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  Microsoft, Inc. (based on files by NetSpot {@link http://www.netspot.com.au})
 */

use local_onenote\api\base;

/**
 * Library class for ONENOTE feedback plugin extending feedback plugin base class.
 *
 * @package   assignfeedback_onenote
 */
class assign_feedback_onenote extends assign_feedback_plugin {

    /**
     * Get the name of the onenote feedback plugin.
     *
     * @return string
     */
    public function get_name() {
        return get_string('onenote', 'assignfeedback_onenote');
    }

    /**
     * Get feedback OneNote file information from the database.
     *
     * @param int $gradeid
     * @return mixed
     */
    public function get_onenote_feedback($gradeid) {
        global $DB;
        return $DB->get_record('assignfeedback_onenote', ['grade' => $gradeid]);
    }

    /**
     * File format options.
     *
     * @return array
     */
    private function get_file_options() {
        global $COURSE;

        $fileoptions = ['subdirs' => 1, 'maxbytes' => $COURSE->maxbytes, 'accepted_types' => '*', 'return_types' => FILE_INTERNAL];
        return $fileoptions;
    }

    /**
     * Copy all the files from one file area to another.
     *
     * @param file_storage $fs - The source context id
     * @param int $fromcontextid - The source context id
     * @param string $fromcomponent - The source component
     * @param string $fromfilearea - The source filearea
     * @param int $fromitemid - The source item id
     * @param int $tocontextid - The destination context id
     * @param string $tocomponent - The destination component
     * @param string $tofilearea - The destination filearea
     * @param int $toitemid - The destination item id
     * @return boolean
     */
    private function copy_area_files(file_storage $fs, $fromcontextid, $fromcomponent, $fromfilearea, $fromitemid, $tocontextid,
        $tocomponent, $tofilearea, $toitemid) {

        $newfilerecord = new stdClass();
        $newfilerecord->contextid = $tocontextid;
        $newfilerecord->component = $tocomponent;
        $newfilerecord->filearea = $tofilearea;
        $newfilerecord->itemid = $toitemid;

        if ($files = $fs->get_area_files($fromcontextid, $fromcomponent, $fromfilearea, $fromitemid)) {
            foreach ($files as $file) {
                if ($file->is_directory() && $file->get_filepath() === '/') {
                    // We need a way to mark the age of each draft area.
                    // By not copying the root dir we force it to be created
                    // automatically with current timestamp.
                    continue;
                }
                $newfile = $fs->create_file_from_storedfile($newfilerecord, $file);
            }
        }
        return true;
    }

    /**
     * Get form elements for grading form.
     *
     * @param stdClass $grade
     * @param MoodleQuickForm $mform
     * @param stdClass $data
     * @param int $userid The userid we are currently grading
     * @return bool true if elements were added to the form
     */
    public function get_form_elements_for_user($grade, MoodleQuickForm $mform, stdClass $data, $userid) {
        global $USER, $PAGE;

        // Check to see if one note is disabled site wide.
        if (get_config('local_o365', 'onenote')) {
            return true;
        }

        $gradeid = $grade ? $grade->id : 0;

        try {
            $onenoteapi = base::getinstance();
        } catch (moodle_exception $e) {
            $html = '<div>' . $e->getMessage() . '</div>';
            $mform->addElement('html', $html);
            return false;
        }

        $isteacher = $onenoteapi->is_teacher($this->assignment->get_course_module()->id, $USER->id);

        if (!$isteacher) {
            return false;
        }
        $o = '<hr/><b>' . get_string('onenoteactions', 'assignfeedback_onenote') . '</b>&nbsp;&nbsp;&nbsp;&nbsp;';

        if ($onenoteapi->is_logged_in()) {
            // Show a link to open the OneNote page.
            $submission = $this->assignment->get_user_submission($userid, false);
            $o .= $onenoteapi->render_action_button(get_string('addfeedback', 'assignfeedback_onenote'),
                $this->assignment->get_course_module()->id, true, true, $userid, $submission ? $submission->id : 0,
                $grade ? $grade->id : null);
            $o .= '<br/><p>' . get_string('addfeedbackhelp', 'assignfeedback_onenote') . '</p>';

            // Show a view all link if the number of files is over this limit.
            $count = $this->count_files($grade->id, base::ASSIGNFEEDBACK_ONENOTE_FILEAREA);
            // Check if feedback is already given.
            if ($count <= base::ASSIGNFEEDBACK_ONENOTE_MAXSUMMARYFILES && $count > 0) {
                $o .= '<button type="submit" class="btn btn-primary" gradeid="' . $grade->id . '" userid="' . $userid;
                $o .= '" contextid="' . $this->assignment->get_context()->id;
                $o .= '" id="deleteuserfeedback"  name="deleteuserfeedback">';
                $o .= get_string('deletefeedbackforuser', 'assignfeedback_onenote') . '</button>';
            }

        } else {
            $o .= $onenoteapi->render_signin_widget();
            $o .= '<br/><br/><p>' . get_string('signinhelp1', 'assignfeedback_onenote') . '</p>';
        }

        $o .= '<hr/>';

        $mform->addElement('html', $o);
        $PAGE->requires->js_call_amd('assignfeedback_onenote/onenotedelete', 'init');

        return true;
    }

    /**
     * Count the number of feedback OneNote files.
     *
     * @param int $gradeid
     * @param string $area
     * @return int
     */
    private function count_files($gradeid, $area) {

        $fs = get_file_storage();
        $files = $fs->get_area_files($this->assignment->get_context()->id, 'assignfeedback_onenote', $area, $gradeid, 'id', false);

        return count($files);
    }

    /**
     * Update the number of feedback OneNote files in the OneNote file area.
     *
     * @param stdClass $grade The grade record
     * @return bool - true if the value was saved
     */
    public function update_file_count($grade) {
        global $DB;

        $filefeedback = $this->get_onenote_feedback($grade->id);
        if ($filefeedback) {
            $filefeedback->numfiles = $this->count_files($grade->id, base::ASSIGNFEEDBACK_ONENOTE_FILEAREA);
            return $DB->update_record('assignfeedback_onenote', $filefeedback);
        } else {
            $filefeedback = new stdClass();
            $filefeedback->numfiles = $this->count_files($grade->id, base::ASSIGNFEEDBACK_ONENOTE_FILEAREA);
            $filefeedback->grade = $grade->id;
            $filefeedback->assignment = $this->assignment->get_instance()->id;
            return $DB->insert_record('assignfeedback_onenote', $filefeedback) > 0;
        }
    }

    /**
     * Save the feedback OneNote files.
     *
     * @param stdClass $grade Grade data.
     * @param stdClass $data
     * @return bool True on successful save, false on error.
     */
    public function save(stdClass $grade, stdClass $data) {
        global $DB, $COURSE, $USER;

        // Get the OneNote page id corresponding to the teacher's feedback for this submission.
        $record = $DB->get_record('local_onenote_assign_pages', ['assign_id' => $grade->assignment, 'user_id' => $grade->userid]);
        if (empty($record) || empty($record->feedback_teacher_page_id)) {
            return true;
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
        $downloadinfo = $onenoteapi->download_page($record->feedback_teacher_page_id, $tempfile);

        if ($downloadinfo) {
            // Get feedback zip size.
            $feedbacksize = filesize($downloadinfo['path']);

            // Check if feedback size is greater than course upload limit.
            if (($COURSE->maxbytes > 0) && ($feedbacksize > $COURSE->maxbytes)) {

                // Display error if true.
                $this->set_error(get_string('feedbacklimitexceed', 'assignfeedback_onenote'));
                return false;
            }

            $fs = get_file_storage();

            // Delete any previous feedbacks.
            $fs->delete_area_files($this->assignment->get_context()->id, 'assignfeedback_onenote',
                base::ASSIGNFEEDBACK_ONENOTE_FILEAREA, $grade->id);

            // Prepare file record object.
            $fileinfo = ['contextid' => $this->assignment->get_context()->id, 'component' => 'assignfeedback_onenote',
                'filearea' => base::ASSIGNFEEDBACK_ONENOTE_FILEAREA, 'itemid' => $grade->id, 'filepath' => '/',
                'filename' => 'OneNote_' . time() . '.zip'];

            // Save it.
            $fs->create_file_from_pathname($fileinfo, $downloadinfo['path']);
            fulldelete($tempfolder);
        } else {
            if ($onenoteapi->is_logged_in()) {
                $this->set_error(get_string('feedbackdownloadfailed', 'assignfeedback_onenote'));
            } else {
                $this->set_error(get_string('notsignedin', 'assignfeedback_onenote'));
            }

            return false;
        }

        return $this->update_file_count($grade);
    }

    /**
     * Determine whether the plugin is being added to the front page.
     *
     * @return bool Whether the plugin is being added to the front page.
     */
    protected function isonfrontpage() {
        if (!empty($this->assignment) && $this->assignment instanceof assign) {
            $coursectx = $this->assignment->get_course_context();
            $coursectxvalid = (!empty($coursectx) && $coursectx instanceof context_course) ? true : false;
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
     * Display the list of feedback OneNote files in the feedback status table.
     *
     * @param stdClass $grade
     * @param bool $showviewlink - Set to true to show a link to see the full list of files
     * @return string
     */
    public function view_summary(stdClass $grade, &$showviewlink) {
        global $USER;

        // Show a view all link if the number of files is over this limit.
        $count = $this->count_files($grade->id, base::ASSIGNFEEDBACK_ONENOTE_FILEAREA);
        $showviewlink = $count > base::ASSIGNFEEDBACK_ONENOTE_MAXSUMMARYFILES;

        try {
            $onenoteapi = base::getinstance();
        } catch (moodle_exception $e) {
            return $e->getMessage();
        }

        $o = '';

        if ($count <= base::ASSIGNFEEDBACK_ONENOTE_MAXSUMMARYFILES) {

            if ($onenoteapi->is_logged_in()) {
                // Show a link to open the OneNote page.
                $submission = $this->assignment->get_user_submission($grade->userid, false);
                $isteacher = $onenoteapi->is_teacher($this->assignment->get_course_module()->id, $USER->id);
                $o .= $onenoteapi->render_action_button(get_string('viewfeedback', 'assignfeedback_onenote'),
                    $this->assignment->get_course_module()->id, true, $isteacher, $grade->userid, $submission ? $submission->id : 0,
                    $grade->id);
            } else {
                $o .= $onenoteapi->render_signin_widget();
                $o .= '<br/><br/><p>' . get_string('signinhelp2', 'assignfeedback_onenote') . '</p>';
            }

            // Show standard link to download zip package.
            $o .= '<p>Download:</p>';
            $filearea = base::ASSIGNFEEDBACK_ONENOTE_FILEAREA;
            $o .= $this->assignment->render_area_files('assignfeedback_onenote', $filearea, $grade->id);

            return $o;
        } else {
            return get_string('countfiles', 'assignfeedback_onenote', $count);
        }
    }

    /**
     * Display the list of feedback OneNote files in the feedback status table.
     *
     * @param stdClass $grade
     * @return string
     */
    public function view(stdClass $grade) {
        $filearea = base::ASSIGNFEEDBACK_ONENOTE_FILEAREA;
        return $this->assignment->render_area_files('assignfeedback_onenote', $filearea, $grade->id);
    }

    /**
     * The assignment has been deleted - cleanup.
     *
     * @return bool
     */
    public function delete_instance() {
        global $DB;
        // Will throw exception on failure.
        $DB->delete_records('assignfeedback_onenote', ['assignment' => $this->assignment->get_instance()->id]);

        return true;
    }

    /**
     * Return true if there are no feedback OneNote files.
     *
     * @param stdClass $grade
     */
    public function is_empty(stdClass $grade) {
        return $this->count_files($grade->id, base::ASSIGNFEEDBACK_ONENOTE_FILEAREA) == 0;
    }

    /**
     * Get file areas returns a list of areas this plugin stores files in.
     *
     * @return array - An array of fileareas (keys) and descriptions (values)
     */
    public function get_file_areas() {
        return [base::ASSIGNFEEDBACK_ONENOTE_FILEAREA => $this->get_name()];
    }
}
