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
 * This file contains the definition for the library class for file feedback plugin
 *
 *
 * @package   qbassignfeedback_file
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \mod_qbassign\output\qbassign_header;

// File areas for file feedback qbassignment.
define('qbassignFEEDBACK_FILE_FILEAREA', 'feedback_files');
define('qbassignFEEDBACK_FILE_BATCH_FILEAREA', 'feedback_files_batch');
define('qbassignFEEDBACK_FILE_IMPORT_FILEAREA', 'feedback_files_import');
define('qbassignFEEDBACK_FILE_MAXSUMMARYFILES', 5);
define('qbassignFEEDBACK_FILE_MAXSUMMARYUSERS', 5);
define('qbassignFEEDBACK_FILE_MAXFILEUNZIPTIME', 120);

/**
 * Library class for file feedback plugin extending feedback plugin base class.
 *
 * @package   qbassignfeedback_file
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbassign_feedback_file extends qbassign_feedback_plugin {

    /**
     * Get the name of the file feedback plugin.
     *
     * @return string
     */
    public function get_name() {
        return get_string('file', 'qbassignfeedback_file');
    }

    /**
     * Get file feedback information from the database.
     *
     * @param int $gradeid
     * @return mixed
     */
    public function get_file_feedback($gradeid) {
        global $DB;
        return $DB->get_record('qbassignfeedback_file', array('grade'=>$gradeid));
    }

    /**
     * File format options.
     *
     * @return array
     */
    private function get_file_options() {
        global $COURSE;

        $fileoptions = array('subdirs'=>1,
                             'maxbytes'=>$COURSE->maxbytes,
                             'accepted_types'=>'*',
                             'return_types'=>FILE_INTERNAL);
        return $fileoptions;
    }

    /**
     * Has the feedback file been modified?
     *
     * @param stdClass $grade Grade object.
     * @param stdClass $data Form data.
     * @return boolean True if the file area has been modified, else false.
     */
    public function is_feedback_modified(stdClass $grade, stdClass $data) {
        global $USER;

        $filekey = null;
        $draftareainfo = null;
        foreach ($data as $key => $value) {
            if (strpos($key, 'files_') === 0 && strpos($key, '_filemanager')) {
                $filekey = $key;
            }
        }
        if (isset($filekey)) {
            $draftareainfo = file_get_draft_area_info($data->$filekey);
            $filecount = $this->count_files($grade->id, qbassignFEEDBACK_FILE_FILEAREA);
            if ($filecount != $draftareainfo['filecount']) {
                return true;
            } else {
                // We need to check that the files in the draft area are the same as in the file area.
                $usercontext = context_user::instance($USER->id);
                $fs = get_file_storage();
                $draftfiles = $fs->get_area_files($usercontext->id, 'user', 'draft', $data->$filekey, 'id', true);
                $files = $fs->get_area_files($this->qbassignment->get_context()->id,
                                     'qbassignfeedback_file',
                                     qbassignFEEDBACK_FILE_FILEAREA,
                                     $grade->id,
                                     'id',
                                     false);
                foreach ($files as $key => $file) {
                    // Flag for recording if we have a matching file.
                    $matchflag = false;
                    foreach ($draftfiles as $draftkey => $draftfile) {
                        if (!$file->is_directory()) {
                            // File name is the same, but it could be a different file with the same name.
                            if ($draftfile->get_filename() == $file->get_filename()) {
                                // If the file name is the same but the content hash is different, or
                                // The file path for the file has changed, then we have a modification.
                                if ($draftfile->get_contenthash() != $file->get_contenthash() ||
                                        $draftfile->get_filepath() != $file->get_filepath()) {
                                    return true;
                                }
                                // These files match. Check the next file.
                                $matchflag = true;
                                // We have a match on the file name so we can move to the next file and not
                                // proceed through the other draftfiles.
                                break;
                            }
                        }
                    }
                    // If the file does not match then there has been a modification.
                    if (!$matchflag) {
                        return true;
                    }
                }
            }
        }
        return false;
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
    private function copy_area_files(file_storage $fs,
                                     $fromcontextid,
                                     $fromcomponent,
                                     $fromfilearea,
                                     $fromitemid,
                                     $tocontextid,
                                     $tocomponent,
                                     $tofilearea,
                                     $toitemid) {

        $newfilerecord = new stdClass();
        $newfilerecord->contextid = $tocontextid;
        $newfilerecord->component = $tocomponent;
        $newfilerecord->filearea = $tofilearea;
        $newfilerecord->itemid = $toitemid;

        if ($files = $fs->get_area_files($fromcontextid, $fromcomponent, $fromfilearea, $fromitemid)) {
            foreach ($files as $file) {
                if ($file->is_directory() and $file->get_filepath() === '/') {
                    // We need a way to mark the age of each draft area.
                    // By not copying the root dir we force it to be created
                    // automatically with current timestamp.
                    continue;
                }

                $existingfile = $fs->get_file(
                    $newfilerecord->contextid,
                    $newfilerecord->component,
                    $newfilerecord->filearea,
                    $newfilerecord->itemid,
                    $file->get_filepath(),
                    $file->get_filename()
                );
                if ($existingfile) {
                    // If the file already exists, remove it so it can be updated.
                    $existingfile->delete();
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

        $fileoptions = $this->get_file_options();
        $gradeid = $grade ? $grade->id : 0;
        $elementname = 'files_' . $userid;

        $data = file_prepare_standard_filemanager($data,
                                                  $elementname,
                                                  $fileoptions,
                                                  $this->qbassignment->get_context(),
                                                  'qbassignfeedback_file',
                                                  qbassignFEEDBACK_FILE_FILEAREA,
                                                  $gradeid);
        $mform->addElement('filemanager', $elementname . '_filemanager', $this->get_name(), null, $fileoptions);

        return true;
    }

    /**
     * Count the number of files.
     *
     * @param int $gradeid
     * @param string $area
     * @return int
     */
    private function count_files($gradeid, $area) {

        $fs = get_file_storage();
        $files = $fs->get_area_files($this->qbassignment->get_context()->id,
                                     'qbassignfeedback_file',
                                     $area,
                                     $gradeid,
                                     'id',
                                     false);

        return count($files);
    }

    /**
     * Update the number of files in the file area.
     *
     * @param stdClass $grade The grade record
     * @return bool - true if the value was saved
     */
    public function update_file_count($grade) {
        global $DB;

        $filefeedback = $this->get_file_feedback($grade->id);
        if ($filefeedback) {
            $filefeedback->numfiles = $this->count_files($grade->id, qbassignFEEDBACK_FILE_FILEAREA);
            return $DB->update_record('qbassignfeedback_file', $filefeedback);
        } else {
            $filefeedback = new stdClass();
            $filefeedback->numfiles = $this->count_files($grade->id, qbassignFEEDBACK_FILE_FILEAREA);
            $filefeedback->grade = $grade->id;
            $filefeedback->qbassignment = $this->qbassignment->get_instance()->id;
            return $DB->insert_record('qbassignfeedback_file', $filefeedback) > 0;
        }
    }

    /**
     * Save the feedback files.
     *
     * @param stdClass $grade
     * @param stdClass $data
     * @return bool
     */
    public function save(stdClass $grade, stdClass $data) {
        $fileoptions = $this->get_file_options();

        // The element name may have been for a different user.
        foreach ($data as $key => $value) {
            if (strpos($key, 'files_') === 0 && strpos($key, '_filemanager')) {
                $elementname = substr($key, 0, strpos($key, '_filemanager'));
            }
        }

        $data = file_postupdate_standard_filemanager($data,
                                                     $elementname,
                                                     $fileoptions,
                                                     $this->qbassignment->get_context(),
                                                     'qbassignfeedback_file',
                                                     qbassignFEEDBACK_FILE_FILEAREA,
                                                     $grade->id);

        return $this->update_file_count($grade);
    }

    /**
     * Display the list of files in the feedback status table.
     *
     * @param stdClass $grade
     * @param bool $showviewlink - Set to true to show a link to see the full list of files
     * @return string
     */
    public function view_summary(stdClass $grade, & $showviewlink) {

        $count = $this->count_files($grade->id, qbassignFEEDBACK_FILE_FILEAREA);

        // Show a view all link if the number of files is over this limit.
        $showviewlink = $count > qbassignFEEDBACK_FILE_MAXSUMMARYFILES;

        if ($count <= qbassignFEEDBACK_FILE_MAXSUMMARYFILES) {
            return $this->qbassignment->render_area_files('qbassignfeedback_file',
                                                        qbassignFEEDBACK_FILE_FILEAREA,
                                                        $grade->id);
        } else {
            return get_string('countfiles', 'qbassignfeedback_file', $count);
        }
    }

    /**
     * Display the list of files in the feedback status table.
     *
     * @param stdClass $grade
     * @return string
     */
    public function view(stdClass $grade) {
        return $this->qbassignment->render_area_files('qbassignfeedback_file',
                                                    qbassignFEEDBACK_FILE_FILEAREA,
                                                    $grade->id);
    }

    /**
     * The qbassignment has been deleted - cleanup.
     *
     * @return bool
     */
    public function delete_instance() {
        global $DB;
        // Will throw exception on failure.
        $DB->delete_records('qbassignfeedback_file',
                            array('qbassignment'=>$this->qbassignment->get_instance()->id));

        return true;
    }

    /**
     * Return true if there are no feedback files.
     *
     * @param stdClass $grade
     */
    public function is_empty(stdClass $grade) {
        return $this->count_files($grade->id, qbassignFEEDBACK_FILE_FILEAREA) == 0;
    }

    /**
     * Get file areas returns a list of areas this plugin stores files.
     *
     * @return array - An array of fileareas (keys) and descriptions (values)
     */
    public function get_file_areas() {
        return array(qbassignFEEDBACK_FILE_FILEAREA=>$this->get_name());
    }

    /**
     * Return true if this plugin can upgrade an old Moodle 2.2 qbassignment of this type
     * and version.
     *
     * @param string $type old qbassignment subtype
     * @param int $version old qbassignment version
     * @return bool True if upgrade is possible
     */
    public function can_upgrade($type, $version) {
        if (($type == 'upload' || $type == 'uploadsingle') && $version >= 2011112900) {
            return true;
        }
        return false;
    }

    /**
     * Upgrade the settings from the old qbassignment to the new plugin based one.
     *
     * @param context $oldcontext - the context for the old qbassignment
     * @param stdClass $oldqbassignment - the data for the old qbassignment
     * @param string $log - can be appended to by the upgrade
     * @return bool was it a success? (false will trigger a rollback)
     */
    public function upgrade_settings(context $oldcontext, stdClass $oldqbassignment, & $log) {
        // First upgrade settings (nothing to do).
        return true;
    }

    /**
     * Upgrade the feedback from the old qbassignment to the new one.
     *
     * @param context $oldcontext - the database for the old qbassignment context
     * @param stdClass $oldqbassignment The data record for the old qbassignment
     * @param stdClass $oldsubmission The data record for the old submission
     * @param stdClass $grade The data record for the new grade
     * @param string $log Record upgrade messages in the log
     * @return bool true or false - false will trigger a rollback
     */
    public function upgrade(context $oldcontext,
                            stdClass $oldqbassignment,
                            stdClass $oldsubmission,
                            stdClass $grade,
                            & $log) {
        global $DB;

        // Now copy the area files.
        $this->qbassignment->copy_area_files_for_upgrade($oldcontext->id,
                                                        'mod_qbassignment',
                                                        'response',
                                                        $oldsubmission->id,
                                                        $this->qbassignment->get_context()->id,
                                                        'qbassignfeedback_file',
                                                        qbassignFEEDBACK_FILE_FILEAREA,
                                                        $grade->id);

        // Now count them!
        $filefeedback = new stdClass();
        $filefeedback->numfiles = $this->count_files($grade->id, qbassignFEEDBACK_FILE_FILEAREA);
        $filefeedback->grade = $grade->id;
        $filefeedback->qbassignment = $this->qbassignment->get_instance()->id;
        if (!$DB->insert_record('qbassignfeedback_file', $filefeedback) > 0) {
            $log .= get_string('couldnotconvertgrade', 'mod_qbassign', $grade->userid);
            return false;
        }
        return true;
    }

    /**
     * Return a list of the batch grading operations performed by this plugin.
     * This plugin supports batch upload files and upload zip.
     *
     * @return array The list of batch grading operations
     */
    public function get_grading_batch_operations() {
        return array('uploadfiles'=>get_string('uploadfiles', 'qbassignfeedback_file'));
    }

    /**
     * Upload files and send them to multiple users.
     *
     * @param array $users - An array of user ids
     * @return string - The response html
     */
    public function view_batch_upload_files($users) {
        global $CFG, $DB, $USER;

        require_capability('mod/qbassign:grade', $this->qbassignment->get_context());
        require_once($CFG->dirroot . '/mod/qbassign/feedback/file/batchuploadfilesform.php');
        require_once($CFG->dirroot . '/mod/qbassign/renderable.php');

        $formparams = array('cm'=>$this->qbassignment->get_course_module()->id,
                            'users'=>$users,
                            'context'=>$this->qbassignment->get_context());

        $usershtml = '';

        $usercount = 0;
        foreach ($users as $userid) {
            if ($usercount >= qbassignFEEDBACK_FILE_MAXSUMMARYUSERS) {
                $moreuserscount = count($users) - qbassignFEEDBACK_FILE_MAXSUMMARYUSERS;
                $usershtml .= get_string('moreusers', 'qbassignfeedback_file', $moreuserscount);
                break;
            }
            $user = $DB->get_record('user', array('id'=>$userid), '*', MUST_EXIST);

            $usersummary = new qbassign_user_summary($user,
                                                   $this->qbassignment->get_course()->id,
                                                   has_capability('moodle/site:viewfullnames',
                                                   $this->qbassignment->get_course_context()),
                                                   $this->qbassignment->is_blind_marking(),
                                                   $this->qbassignment->get_uniqueid_for_user($user->id),
                                                   // TODO Does not support custom user profile fields (MDL-70456).
                                                   \core_user\fields::get_identity_fields($this->qbassignment->get_context(), false));
            $usershtml .= $this->qbassignment->get_renderer()->render($usersummary);
            $usercount += 1;
        }

        $formparams['usershtml'] = $usershtml;

        $mform = new qbassignfeedback_file_batch_upload_files_form(null, $formparams);

        if ($mform->is_cancelled()) {
            redirect(new moodle_url('view.php',
                                    array('id'=>$this->qbassignment->get_course_module()->id,
                                          'action'=>'grading')));
            return;
        } else if ($data = $mform->get_data()) {
            // Copy the files from the draft area to a temporary import area.
            $data = file_postupdate_standard_filemanager($data,
                                                         'files',
                                                         $this->get_file_options(),
                                                         $this->qbassignment->get_context(),
                                                         'qbassignfeedback_file',
                                                         qbassignFEEDBACK_FILE_BATCH_FILEAREA,
                                                         $USER->id);
            $fs = get_file_storage();

            // Now copy each of these files to the users feedback file area.
            foreach ($users as $userid) {
                $grade = $this->qbassignment->get_user_grade($userid, true);
                $this->qbassignment->notify_grade_modified($grade);

                $this->copy_area_files($fs,
                                       $this->qbassignment->get_context()->id,
                                       'qbassignfeedback_file',
                                       qbassignFEEDBACK_FILE_BATCH_FILEAREA,
                                       $USER->id,
                                       $this->qbassignment->get_context()->id,
                                       'qbassignfeedback_file',
                                       qbassignFEEDBACK_FILE_FILEAREA,
                                       $grade->id);

                $filefeedback = $this->get_file_feedback($grade->id);
                if ($filefeedback) {
                    $filefeedback->numfiles = $this->count_files($grade->id,
                                                                 qbassignFEEDBACK_FILE_FILEAREA);
                    $DB->update_record('qbassignfeedback_file', $filefeedback);
                } else {
                    $filefeedback = new stdClass();
                    $filefeedback->numfiles = $this->count_files($grade->id,
                                                                 qbassignFEEDBACK_FILE_FILEAREA);
                    $filefeedback->grade = $grade->id;
                    $filefeedback->qbassignment = $this->qbassignment->get_instance()->id;
                    $DB->insert_record('qbassignfeedback_file', $filefeedback);
                }
            }

            // Now delete the temporary import area.
            $fs->delete_area_files($this->qbassignment->get_context()->id,
                                   'qbassignfeedback_file',
                                   qbassignFEEDBACK_FILE_BATCH_FILEAREA,
                                   $USER->id);

            redirect(new moodle_url('view.php',
                                    array('id'=>$this->qbassignment->get_course_module()->id,
                                          'action'=>'grading')));
            return;
        } else {

            $header = new qbassign_header($this->qbassignment->get_instance(),
                                        $this->qbassignment->get_context(),
                                        false,
                                        $this->qbassignment->get_course_module()->id,
                                        get_string('batchuploadfiles', 'qbassignfeedback_file'));
            $o = '';
            $o .= $this->qbassignment->get_renderer()->render($header);
            $o .= $this->qbassignment->get_renderer()->render(new qbassign_form('batchuploadfiles', $mform));
            $o .= $this->qbassignment->get_renderer()->render_footer();
        }

        return $o;
    }

    /**
     * User has chosen a custom grading batch operation and selected some users.
     *
     * @param string $action - The chosen action
     * @param array $users - An array of user ids
     * @return string - The response html
     */
    public function grading_batch_operation($action, $users) {

        if ($action == 'uploadfiles') {
            return $this->view_batch_upload_files($users);
        }
        return '';
    }

    /**
     * View the upload zip form.
     *
     * @return string - The html response
     */
    public function view_upload_zip() {
        global $CFG, $USER;

        require_capability('mod/qbassign:grade', $this->qbassignment->get_context());
        require_once($CFG->dirroot . '/mod/qbassign/feedback/file/uploadzipform.php');
        require_once($CFG->dirroot . '/mod/qbassign/feedback/file/importziplib.php');
        require_once($CFG->dirroot . '/mod/qbassign/feedback/file/importzipform.php');

        $formparams = array('context'=>$this->qbassignment->get_context(),
                            'cm'=>$this->qbassignment->get_course_module()->id);
        $mform = new qbassignfeedback_file_upload_zip_form(null, $formparams);

        $o = '';

        $confirm = optional_param('confirm', 0, PARAM_BOOL);
        $renderer = $this->qbassignment->get_renderer();

        // Delete any existing files.
        $importer = new qbassignfeedback_file_zip_importer();
        $contextid = $this->qbassignment->get_context()->id;

        if ($mform->is_cancelled()) {
            $importer->delete_import_files($contextid);
            $urlparams = array('id'=>$this->qbassignment->get_course_module()->id,
                               'action'=>'grading');
            $url = new moodle_url('view.php', $urlparams);
            redirect($url);
            return;
        } else if ($confirm) {
            $params = array('qbassignment'=>$this->qbassignment, 'importer'=>$importer);

            $mform = new qbassignfeedback_file_import_zip_form(null, $params);
            if ($mform->is_cancelled()) {
                $importer->delete_import_files($contextid);
                $urlparams = array('id'=>$this->qbassignment->get_course_module()->id,
                                   'action'=>'grading');
                $url = new moodle_url('view.php', $urlparams);
                redirect($url);
                return;
            }

            $o .= $importer->import_zip_files($this->qbassignment, $this);
            $importer->delete_import_files($contextid);
        } else if (($data = $mform->get_data()) &&
                   ($zipfile = $mform->save_stored_file('feedbackzip',
                                                        $contextid,
                                                        'qbassignfeedback_file',
                                                        qbassignFEEDBACK_FILE_IMPORT_FILEAREA,
                                                        $USER->id,
                                                        '/',
                                                        'import.zip',
                                                        true))) {

            $importer->extract_files_from_zip($zipfile, $contextid);

            $params = array('qbassignment'=>$this->qbassignment, 'importer'=>$importer);

            $mform = new qbassignfeedback_file_import_zip_form(null, $params);

            $header = new qbassign_header($this->qbassignment->get_instance(),
                                        $this->qbassignment->get_context(),
                                        false,
                                        $this->qbassignment->get_course_module()->id,
                                        get_string('confirmuploadzip', 'qbassignfeedback_file'));
            $o .= $renderer->render($header);
            $o .= $renderer->render(new qbassign_form('confirmimportzip', $mform));
            $o .= $renderer->render_footer();

        } else {

            $header = new qbassign_header($this->qbassignment->get_instance(),
                                        $this->qbassignment->get_context(),
                                        false,
                                        $this->qbassignment->get_course_module()->id,
                                        get_string('uploadzip', 'qbassignfeedback_file'));
            $o .= $renderer->render($header);
            $o .= $renderer->render(new qbassign_form('uploadfeedbackzip', $mform));
            $o .= $renderer->render_footer();
        }

        return $o;
    }

    /**
     * Called by the qbassignment module when someone chooses something from the
     * grading navigation or batch operations list.
     *
     * @param string $action - The page to view
     * @return string - The html response
     */
    public function view_page($action) {
        if ($action == 'uploadfiles') {
            $users = required_param('selectedusers', PARAM_SEQUENCE);
            return $this->view_batch_upload_files(explode(',', $users));
        }
        if ($action == 'uploadzip') {
            return $this->view_upload_zip();
        }

        return '';
    }

    /**
     * Return a list of the grading actions performed by this plugin.
     * This plugin supports upload zip.
     *
     * @return array The list of grading actions
     */
    public function get_grading_actions() {
        return array('uploadzip'=>get_string('uploadzip', 'qbassignfeedback_file'));
    }

    /**
     * Return a description of external params suitable for uploading a feedback file from a webservice.
     *
     * @return external_description|null
     */
    public function get_external_parameters() {
        return array(
            'files_filemanager' => new external_value(
                PARAM_INT,
                'The id of a draft area containing files for this feedback.',
                VALUE_OPTIONAL
            )
        );
    }

    /**
     * Return the plugin configs for external functions.
     *
     * @return array the list of settings
     * @since Moodle 3.2
     */
    public function get_config_for_external() {
        return (array) $this->get_config();
    }
}
