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
 * @package   assignfeedback_file
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
/**
 * File areas for file feedback assignment
 */
define('ASSIGNFEEDBACK_FILE_FILEAREA', 'feedback_files');
define('ASSIGNFEEDBACK_FILE_MAXSUMMARYFILES', 5);

/**
 * library class for file feedback plugin extending feedback plugin base class
 *
 * @package   asignfeedback_file
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_feedback_file extends assign_feedback_plugin {

    /**
     * Get the name of the file feedback plugin
     * @return string
     */
    public function get_name() {
        return get_string('file', 'assignfeedback_file');
    }

    /**
     * Get file feedback information from the database
     *
     * @param int $gradeid
     * @return mixed
     */
    public function get_file_feedback($gradeid) {
        global $DB;
        return $DB->get_record('assignfeedback_file', array('grade'=>$gradeid));
    }

    /**
     * File format options
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
     * Get form elements for grading form
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
                                                  $this->assignment->get_context(),
                                                  'assignfeedback_file',
                                                  ASSIGNFEEDBACK_FILE_FILEAREA,
                                                  $gradeid);
        $mform->addElement('filemanager', $elementname . '_filemanager', html_writer::tag('span', $this->get_name(),
            array('class' => 'accesshide')), null, $fileoptions);

        return true;
    }

    /**
     * Count the number of files
     *
     * @param int $gradeid
     * @param string $area
     * @return int
     */
    private function count_files($gradeid, $area) {
        global $USER;

        $fs = get_file_storage();
        $files = $fs->get_area_files($this->assignment->get_context()->id, 'assignfeedback_file', $area, $gradeid, "id", false);

        return count($files);
    }

    /**
     * Save the feedback files
     *
     * @param stdClass $grade
     * @param stdClass $data
     * @return bool
     */
    public function save(stdClass $grade, stdClass $data) {
        global $DB;

        $fileoptions = $this->get_file_options();

        $userid = $grade->userid;
        $elementname = 'files_' . $userid;

        $data = file_postupdate_standard_filemanager($data,
                                                     $elementname,
                                                     $fileoptions,
                                                     $this->assignment->get_context(),
                                                     'assignfeedback_file',
                                                     ASSIGNFEEDBACK_FILE_FILEAREA,
                                                     $grade->id);

        $filefeedback = $this->get_file_feedback($grade->id);
        if ($filefeedback) {
            $filefeedback->numfiles = $this->count_files($grade->id, ASSIGNFEEDBACK_FILE_FILEAREA);
            return $DB->update_record('assignfeedback_file', $filefeedback);
        } else {
            $filefeedback = new stdClass();
            $filefeedback->numfiles = $this->count_files($grade->id, ASSIGNFEEDBACK_FILE_FILEAREA);
            $filefeedback->grade = $grade->id;
            $filefeedback->assignment = $this->assignment->get_instance()->id;
            return $DB->insert_record('assignfeedback_file', $filefeedback) > 0;
        }
    }

    /**
     * Display the list of files  in the feedback status table
     *
     * @param stdClass $grade
     * @param bool $showviewlink - Set to true to show a link to see the full list of files
     * @return string
     */
    public function view_summary(stdClass $grade, & $showviewlink) {
        $count = $this->count_files($grade->id, ASSIGNFEEDBACK_FILE_FILEAREA);
        // show a view all link if the number of files is over this limit
        $showviewlink = $count > ASSIGNFEEDBACK_FILE_MAXSUMMARYFILES;

        if ($count <= ASSIGNFEEDBACK_FILE_MAXSUMMARYFILES) {
            return $this->assignment->render_area_files('assignfeedback_file', ASSIGNFEEDBACK_FILE_FILEAREA, $grade->id);
        } else {
            return get_string('countfiles', 'assignfeedback_file', $count);
        }
    }

    /**
     * Display the list of files  in the feedback status table
     * @param stdClass $grade
     * @return string
     */
    public function view(stdClass $grade) {
        return $this->assignment->render_area_files('assignfeedback_file', ASSIGNFEEDBACK_FILE_FILEAREA, $grade->id);
    }

    /**
     * The assignment has been deleted - cleanup
     *
     * @return bool
     */
    public function delete_instance() {
        global $DB;
        // will throw exception on failure
        $DB->delete_records('assignfeedback_file', array('assignment'=>$this->assignment->get_instance()->id));

        return true;
    }

    /**
     * Return true if there are no feedback files
     * @param stdClass $grade
     */
    public function is_empty(stdClass $grade) {
        return $this->count_files($grade->id, ASSIGNFEEDBACK_FILE_FILEAREA) == 0;
    }

    /**
     * Get file areas returns a list of areas this plugin stores files
     * @return array - An array of fileareas (keys) and descriptions (values)
     */
    public function get_file_areas() {
        return array(ASSIGNFEEDBACK_FILE_FILEAREA=>$this->get_name());
    }

    /**
     * Return true if this plugin can upgrade an old Moodle 2.2 assignment of this type
     * and version.
     *
     * @param string $type old assignment subtype
     * @param int $version old assignment version
     * @return bool True if upgrade is possible
     */
    public function can_upgrade($type, $version) {

        if (($type == 'upload' || $type == 'uploadsingle') && $version >= 2011112900) {
            return true;
        }
        return false;
    }

    /**
     * Upgrade the settings from the old assignment to the new plugin based one
     *
     * @param context $oldcontext - the context for the old assignment
     * @param stdClass $oldassignment - the data for the old assignment
     * @param string $log - can be appended to by the upgrade
     * @return bool was it a success? (false will trigger a rollback)
     */
    public function upgrade_settings(context $oldcontext, stdClass $oldassignment, & $log) {
        // first upgrade settings (nothing to do)
        return true;
    }

    /**
     * Upgrade the feedback from the old assignment to the new one
     *
     * @param context $oldcontext - the database for the old assignment context
     * @param stdClass $oldassignment The data record for the old assignment
     * @param stdClass $oldsubmission The data record for the old submission
     * @param stdClass $grade The data record for the new grade
     * @param string $log Record upgrade messages in the log
     * @return bool true or false - false will trigger a rollback
     */
    public function upgrade(context $oldcontext, stdClass $oldassignment, stdClass $oldsubmission, stdClass $grade, & $log) {
        global $DB;

        // now copy the area files
        $this->assignment->copy_area_files_for_upgrade($oldcontext->id,
                                                        'mod_assignment',
                                                        'response',
                                                        $oldsubmission->id,
                                                        // New file area
                                                        $this->assignment->get_context()->id,
                                                        'assignfeedback_file',
                                                        ASSIGNFEEDBACK_FILE_FILEAREA,
                                                        $grade->id);

        // now count them!
        $filefeedback = new stdClass();
        $filefeedback->numfiles = $this->count_files($grade->id, ASSIGNFEEDBACK_FILE_FILEAREA);
        $filefeedback->grade = $grade->id;
        $filefeedback->assignment = $this->assignment->get_instance()->id;
        if (!$DB->insert_record('assignfeedback_file', $filefeedback) > 0) {
            $log .= get_string('couldnotconvertgrade', 'mod_assign', $grade->userid);
            return false;
        }
        return true;
    }
}
