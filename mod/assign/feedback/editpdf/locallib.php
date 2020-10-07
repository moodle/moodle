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
 * This file contains the definition for the library class for PDF feedback plugin
 *
 *
 * @package   assignfeedback_editpdf
 * @copyright 2012 Davo Smith
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \assignfeedback_editpdf\document_services;
use \assignfeedback_editpdf\page_editor;

/**
 * library class for editpdf feedback plugin extending feedback plugin base class
 *
 * @package   assignfeedback_editpdf
 * @copyright 2012 Davo Smith
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_feedback_editpdf extends assign_feedback_plugin {

    /** @var boolean|null $enabledcache Cached lookup of the is_enabled function */
    private $enabledcache = null;

    /**
     * Get the name of the file feedback plugin
     * @return string
     */
    public function get_name() {
        return get_string('pluginname', 'assignfeedback_editpdf');
    }

    /**
     * Create a widget for rendering the editor.
     *
     * @param int $userid
     * @param stdClass $grade
     * @param bool $readonly
     * @return assignfeedback_editpdf_widget
     */
    public function get_widget($userid, $grade, $readonly) {
        $attempt = -1;
        if ($grade && isset($grade->attemptnumber)) {
            $attempt = $grade->attemptnumber;
        } else {
            $grade = $this->assignment->get_user_grade($userid, true);
        }

        $feedbackfile = document_services::get_feedback_document($this->assignment->get_instance()->id,
                                                                 $userid,
                                                                 $attempt);

        $stampfiles = array();
        $systemfiles = array();
        $fs = get_file_storage();
        $syscontext = context_system::instance();
        $asscontext = $this->assignment->get_context();

        // Three file areas are used for stamps.
        // Current stamps are those configured as a site administration setting to be available for new uses.
        // When a stamp is removed from this filearea it is no longer available for new grade items.
        $currentstamps = $fs->get_area_files($syscontext->id, 'assignfeedback_editpdf', 'stamps', 0, 'filename', false);

        // Grade stamps are those which have been assigned for a specific grade item.
        // The stamps associated with a grade item are always used for that grade item, even if the stamp is removed
        // from the list of current stamps.
        $gradestamps = $fs->get_area_files($asscontext->id, 'assignfeedback_editpdf', 'stamps', $grade->id, 'filename', false);

        // The system stamps are perpetual and always exist.
        // They allow Moodle to serve a common URL for all users for any possible combination of stamps.
        // Files in the perpetual stamp filearea are within the system context, in itemid 0, and use the original stamps
        // contenthash as a folder name. This ensures that the combination of stamp filename, and stamp file content is
        // unique.
        $systemstamps = $fs->get_area_files($syscontext->id, 'assignfeedback_editpdf', 'systemstamps', 0, 'filename', false);

        // First check that all current stamps are listed in the grade stamps.
        foreach ($currentstamps as $stamp) {
            // Ensure that the current stamp is in the list of perpetual stamps.
            $systempathnamehash = $this->get_system_stamp_path($stamp);
            if (!array_key_exists($systempathnamehash, $systemstamps)) {
                $filerecord = (object) [
                    'filearea' => 'systemstamps',
                    'filepath' => '/' . $stamp->get_contenthash() . '/',
                ];
                $newstamp = $fs->create_file_from_storedfile($filerecord, $stamp);
                $systemstamps[$newstamp->get_pathnamehash()] = $newstamp;
            }

            // Ensure that the current stamp is in the list of stamps for the current grade item.
            $gradeitempathhash = $this->get_assignment_stamp_path($stamp, $grade->id);
            if (!array_key_exists($gradeitempathhash, $gradestamps)) {
                $filerecord = (object) [
                    'contextid' => $asscontext->id,
                    'filearea' => 'stamps',
                    'itemid' => $grade->id,
                ];
                $newstamp = $fs->create_file_from_storedfile($filerecord, $stamp);
                $gradestamps[$newstamp->get_pathnamehash()] = $newstamp;
            }
        }

        foreach ($gradestamps as $stamp) {
            // All gradestamps should be available in the systemstamps filearea, but some legacy stamps may not be.
            // These need to be copied over.
            // Note: This should ideally be performed as an upgrade step, but there may be other cases that these do not
            // exist, for example restored backups.
            // In any case this is a cheap operation as it is solely performing an array lookup.
            $systempathnamehash = $this->get_system_stamp_path($stamp);
            if (!array_key_exists($systempathnamehash, $systemstamps)) {
                $filerecord = (object) [
                    'contextid' => $syscontext->id,
                    'itemid' => 0,
                    'filearea' => 'systemstamps',
                    'filepath' => '/' . $stamp->get_contenthash() . '/',
                ];
                $systemstamp = $fs->create_file_from_storedfile($filerecord, $stamp);
                $systemstamps[$systemstamp->get_pathnamehash()] = $systemstamp;
            }

            // Always serve the perpetual system stamp.
            // This ensures that the stamp is highly cached and reduces the hit on the application server.
            $gradestamp = $systemstamps[$systempathnamehash];
            $url = moodle_url::make_pluginfile_url(
                $gradestamp->get_contextid(),
                $gradestamp->get_component(),
                $gradestamp->get_filearea(),
                null,
                $gradestamp->get_filepath(),
                $gradestamp->get_filename(),
                false
            );
            array_push($stampfiles, $url->out());
        }

        $url = false;
        $filename = '';
        if ($feedbackfile) {
            $url = moodle_url::make_pluginfile_url($this->assignment->get_context()->id,
                                                   'assignfeedback_editpdf',
                                                   document_services::FINAL_PDF_FILEAREA,
                                                   $grade->id,
                                                   '/',
                                                   $feedbackfile->get_filename(),
                                                   false);
           $filename = $feedbackfile->get_filename();
        }

        $widget = new assignfeedback_editpdf_widget($this->assignment->get_instance()->id,
                                                    $userid,
                                                    $attempt,
                                                    $url,
                                                    $filename,
                                                    $stampfiles,
                                                    $readonly
                                                );
        return $widget;
    }

    /**
     * Get the pathnamehash for the specified stamp if in the system stamps.
     *
     * @param   stored_file $file
     * @return  string
     */
    protected function get_system_stamp_path(stored_file $stamp): string {
        $systemcontext = context_system::instance();

        return file_storage::get_pathname_hash(
            $systemcontext->id,
            'assignfeedback_editpdf',
            'systemstamps',
            0,
            '/' . $stamp->get_contenthash() . '/',
            $stamp->get_filename()
        );
    }

    /**
     * Get the pathnamehash for the specified stamp if in the current assignment stamps.
     *
     * @param   stored_file $file
     * @param   int $gradeid
     * @return  string
     */
    protected function get_assignment_stamp_path(stored_file $stamp, int $gradeid): string {
        return file_storage::get_pathname_hash(
            $this->assignment->get_context()->id,
            'assignfeedback_editpdf',
            'stamps',
            $gradeid,
            $stamp->get_filepath(),
            $stamp->get_filename()
        );
    }

    /**
     * Get form elements for grading form
     *
     * @param stdClass $grade
     * @param MoodleQuickForm $mform
     * @param stdClass $data
     * @param int $userid
     * @return bool true if elements were added to the form
     */
    public function get_form_elements_for_user($grade, MoodleQuickForm $mform, stdClass $data, $userid) {
        global $PAGE;

        $attempt = -1;
        if ($grade) {
            $attempt = $grade->attemptnumber;
        }

        $renderer = $PAGE->get_renderer('assignfeedback_editpdf');

        // Links to download the generated pdf...
        if ($attempt > -1 && page_editor::has_annotations_or_comments($grade->id, false)) {
            $html = $this->assignment->render_area_files('assignfeedback_editpdf',
                                                         document_services::FINAL_PDF_FILEAREA,
                                                         $grade->id);
            $mform->addElement('static', 'editpdf_files', get_string('downloadfeedback', 'assignfeedback_editpdf'), $html);
        }

        $widget = $this->get_widget($userid, $grade, false);

        $html = $renderer->render($widget);
        $mform->addElement('static', 'editpdf', get_string('editpdf', 'assignfeedback_editpdf'), $html);
        $mform->addHelpButton('editpdf', 'editpdf', 'assignfeedback_editpdf');
        $mform->addElement('hidden', 'editpdf_source_userid', $userid);
        $mform->setType('editpdf_source_userid', PARAM_INT);
        $mform->setConstant('editpdf_source_userid', $userid);
    }

    /**
     * Check to see if the grade feedback for the pdf has been modified.
     *
     * @param stdClass $grade Grade object.
     * @param stdClass $data Data from the form submission (not used).
     * @return boolean True if the pdf has been modified, else false.
     */
    public function is_feedback_modified(stdClass $grade, stdClass $data) {
        // We only need to know if the source user's PDF has changed. If so then all
        // following users will have the same status. If it's only an individual annotation
        // then only one user will come through this method.
        // Source user id is only added to the form if there was a pdf.
        if (!empty($data->editpdf_source_userid)) {
            $sourceuserid = $data->editpdf_source_userid;
            // Retrieve the grade information for the source user.
            $sourcegrade = $this->assignment->get_user_grade($sourceuserid, true, $grade->attemptnumber);
            $pagenumbercount = document_services::page_number_for_attempt($this->assignment, $sourceuserid, $sourcegrade->attemptnumber);
            for ($i = 0; $i < $pagenumbercount; $i++) {
                // Select all annotations.
                $draftannotations = page_editor::get_annotations($sourcegrade->id, $i, true);
                $nondraftannotations = page_editor::get_annotations($grade->id, $i, false);
                // Check to see if the count is the same.
                if (count($draftannotations) != count($nondraftannotations)) {
                    // The count is different so we have a modification.
                    return true;
                } else {
                    $matches = 0;
                    // Have a closer look and see if the draft files match all the non draft files.
                    foreach ($nondraftannotations as $ndannotation) {
                        foreach ($draftannotations as $dannotation) {
                            foreach ($ndannotation as $key => $value) {
                                if ($key != 'id' && $value != $dannotation->{$key}) {
                                    continue 2;
                                }
                            }
                            $matches++;
                        }
                    }
                    if ($matches !== count($nondraftannotations)) {
                        return true;
                    }
                }
                // Select all comments.
                $draftcomments = page_editor::get_comments($sourcegrade->id, $i, true);
                $nondraftcomments = page_editor::get_comments($grade->id, $i, false);
                if (count($draftcomments) != count($nondraftcomments)) {
                    return true;
                } else {
                    // Go for a closer inspection.
                    $matches = 0;
                    foreach ($nondraftcomments as $ndcomment) {
                        foreach ($draftcomments as $dcomment) {
                            foreach ($ndcomment as $key => $value) {
                                if ($key != 'id' && $value != $dcomment->{$key}) {
                                    continue 2;
                                }
                            }
                            $matches++;
                        }
                    }
                    if ($matches !== count($nondraftcomments)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Generate the pdf.
     *
     * @param stdClass $grade
     * @param stdClass $data
     * @return bool
     */
    public function save(stdClass $grade, stdClass $data) {
        // Source user id is only added to the form if there was a pdf.
        if (!empty($data->editpdf_source_userid)) {
            $sourceuserid = $data->editpdf_source_userid;
            // Copy drafts annotations and comments if current user is different to sourceuserid.
            if ($sourceuserid != $grade->userid) {
                page_editor::copy_drafts_from_to($this->assignment, $grade, $sourceuserid);
            }
        }
        if (page_editor::has_annotations_or_comments($grade->id, true)) {
            document_services::generate_feedback_document($this->assignment, $grade->userid, $grade->attemptnumber);
        }

        return true;
    }

    /**
     * Display the list of files in the feedback status table.
     *
     * @param stdClass $grade
     * @param bool $showviewlink (Always set to false).
     * @return string
     */
    public function view_summary(stdClass $grade, & $showviewlink) {
        $showviewlink = false;
        return $this->view($grade);
    }

    /**
     * Display the list of files in the feedback status table.
     *
     * @param stdClass $grade
     * @return string
     */
    public function view(stdClass $grade) {
        global $PAGE;
        $html = '';
        // Show a link to download the pdf.
        if (page_editor::has_annotations_or_comments($grade->id, false)) {
            $html = $this->assignment->render_area_files('assignfeedback_editpdf',
                                                         document_services::FINAL_PDF_FILEAREA,
                                                         $grade->id);

            // Also show the link to the read-only interface.
            $renderer = $PAGE->get_renderer('assignfeedback_editpdf');
            $widget = $this->get_widget($grade->userid, $grade, true);

            $html .= $renderer->render($widget);
        }
        return $html;
    }

    /**
     * Return true if there are no released comments/annotations.
     *
     * @param stdClass $grade
     */
    public function is_empty(stdClass $grade) {
        global $DB;

        $comments = $DB->count_records('assignfeedback_editpdf_cmnt', array('gradeid'=>$grade->id, 'draft'=>0));
        $annotations = $DB->count_records('assignfeedback_editpdf_annot', array('gradeid'=>$grade->id, 'draft'=>0));
        return $comments == 0 && $annotations == 0;
    }

    /**
     * The assignment has been deleted - remove the plugin specific data
     *
     * @return bool
     */
    public function delete_instance() {
        global $DB;
        $grades = $DB->get_records('assign_grades', array('assignment'=>$this->assignment->get_instance()->id), '', 'id');
        if ($grades) {
            list($gradeids, $params) = $DB->get_in_or_equal(array_keys($grades), SQL_PARAMS_NAMED);
            $DB->delete_records_select('assignfeedback_editpdf_annot', 'gradeid ' . $gradeids, $params);
            $DB->delete_records_select('assignfeedback_editpdf_cmnt', 'gradeid ' . $gradeids, $params);
        }
        return true;
    }

    /**
     * Determine if ghostscript is available and working.
     *
     * @return bool
     */
    public function is_available() {
        if ($this->enabledcache === null) {
            $testpath = assignfeedback_editpdf\pdf::test_gs_path(false);
            $this->enabledcache = ($testpath->status == assignfeedback_editpdf\pdf::GSPATH_OK);
        }
        return $this->enabledcache;
    }
    /**
     * Prevent enabling this plugin if ghostscript is not available.
     *
     * @return bool false
     */
    public function is_configurable() {
        return $this->is_available();
    }

    /**
     * Get file areas returns a list of areas this plugin stores files.
     *
     * @return array - An array of fileareas (keys) and descriptions (values)
     */
    public function get_file_areas() {
        return array(document_services::FINAL_PDF_FILEAREA => $this->get_name());
    }

    /**
     * This plugin will inject content into the review panel with javascript.
     * @return bool true
     */
    public function supports_review_panel() {
        return true;
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
