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
 * This file contains the definition for the library class for onlinetext submission plugin
 *
 * This class provides all the functionality for the new assign module.
 *
 * @package assignsubmission_onlinetext
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
/**
 * File area for online text submission assignment
 */
define('ASSIGNSUBMISSION_ONLINETEXT_FILEAREA', 'submissions_onlinetext');

/**
 * library class for onlinetext submission plugin extending submission plugin base class
 *
 * @package assignsubmission_onlinetext
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_submission_onlinetext extends assign_submission_plugin {

    /**
     * Get the name of the online text submission plugin
     * @return string
     */
    public function get_name() {
        return get_string('onlinetext', 'assignsubmission_onlinetext');
    }


   /**
    * Get onlinetext submission information from the database
    *
    * @param  int $submissionid
    * @return mixed
    */
    private function get_onlinetext_submission($submissionid) {
        global $DB;

        return $DB->get_record('assignsubmission_onlinetext', array('submission'=>$submissionid));
    }

    /**
     * Add form elements for settings
     *
     * @param mixed $submission can be null
     * @param MoodleQuickForm $mform
     * @param stdClass $data
     * @return true if elements were added to the form
     */
    public function get_form_elements($submission, MoodleQuickForm $mform, stdClass $data) {
        $elements = array();

        $editoroptions = $this->get_edit_options();
        $submissionid = $submission ? $submission->id : 0;

        if (!isset($data->onlinetext)) {
            $data->onlinetext = '';
        }
        if (!isset($data->onlinetextformat)) {
            $data->onlinetextformat = editors_get_preferred_format();
        }

        if ($submission) {
            $onlinetextsubmission = $this->get_onlinetext_submission($submission->id);
            if ($onlinetextsubmission) {
                $data->onlinetext = $onlinetextsubmission->onlinetext;
                $data->onlinetextformat = $onlinetextsubmission->onlineformat;
            }

        }


        $data = file_prepare_standard_editor($data, 'onlinetext', $editoroptions, $this->assignment->get_context(), 'assignsubmission_onlinetext', ASSIGNSUBMISSION_ONLINETEXT_FILEAREA, $submissionid);
        $mform->addElement('editor', 'onlinetext_editor', '', null, $editoroptions);
        return true;
    }

    /**
     * Editor format options
     *
     * @return array
     */
    private function get_edit_options() {
         $editoroptions = array(
           'noclean' => false,
           'maxfiles' => EDITOR_UNLIMITED_FILES,
           'maxbytes' => $this->assignment->get_course()->maxbytes,
           'context' => $this->assignment->get_context(),
           'return_types' => FILE_INTERNAL | FILE_EXTERNAL
        );
        return $editoroptions;
    }

     /**
      * Save data to the database and trigger plagiarism plugin, if enabled, to scan the uploaded content via events trigger
      *
      * @param stdClass $submission
      * @param stdClass $data
      * @return bool
      */
     public function save(stdClass $submission, stdClass $data) {
        global $USER, $DB;

        $editoroptions = $this->get_edit_options();

        $data = file_postupdate_standard_editor($data, 'onlinetext', $editoroptions, $this->assignment->get_context(), 'assignsubmission_onlinetext', ASSIGNSUBMISSION_ONLINETEXT_FILEAREA, $submission->id);

        $onlinetextsubmission = $this->get_onlinetext_submission($submission->id);

        $fs = get_file_storage();
        $files = $fs->get_area_files($this->assignment->get_context()->id, 'assignsubmission_onlinetext', ASSIGNSUBMISSION_ONLINETEXT_FILEAREA, $submission->id, "id", false);
        // Let Moodle know that an assessable content was uploaded (eg for plagiarism detection)
        $eventdata = new stdClass();
        $eventdata->modulename = 'assign';
        $eventdata->cmid = $this->assignment->get_course_module()->id;
        $eventdata->itemid = $submission->id;
        $eventdata->courseid = $this->assignment->get_course()->id;
        $eventdata->userid = $USER->id;
        $eventdata->content = trim(format_text($data->onlinetext, $data->onlinetext_editor['format'], array('context'=>$this->assignment->get_context())));
        if ($files) {
            $eventdata->pathnamehashes = array_keys($files);
        }
        events_trigger('assessable_content_uploaded', $eventdata);

        if ($onlinetextsubmission) {

            $onlinetextsubmission->onlinetext = $data->onlinetext;
            $onlinetextsubmission->onlineformat = $data->onlinetext_editor['format'];


            return $DB->update_record('assignsubmission_onlinetext', $onlinetextsubmission);
        } else {

            $onlinetextsubmission = new stdClass();
            $onlinetextsubmission->onlinetext = $data->onlinetext;
            $onlinetextsubmission->onlineformat = $data->onlinetext_editor['format'];

            $onlinetextsubmission->submission = $submission->id;
            $onlinetextsubmission->assignment = $this->assignment->get_instance()->id;
            return $DB->insert_record('assignsubmission_onlinetext', $onlinetextsubmission) > 0;
        }


    }

    /**
     * Return a list of the text fields that can be imported/exported by this plugin
     *
     * @return array An array of field names and descriptions. (name=>description, ...)
     */
    public function get_editor_fields() {
        return array('onlinetext' => get_string('pluginname', 'assignsubmission_comments'));
    }

    /**
     * Get the saved text content from the editor
     *
     * @param string $name
     * @param int $submissionid
     * @return string
     */
    public function get_editor_text($name, $submissionid) {
        if ($name == 'onlinetext') {
            $onlinetextsubmission = $this->get_onlinetext_submission($submissionid);
            if ($onlinetextsubmission) {
                return $onlinetextsubmission->onlinetext;
            }
        }

        return '';
    }

    /**
     * Get the content format for the editor
     *
     * @param string $name
     * @param int $submissionid
     * @return int
     */
    public function get_editor_format($name, $submissionid) {
        if ($name == 'onlinetext') {
            $onlinetextsubmission = $this->get_onlinetext_submission($submissionid);
            if ($onlinetextsubmission) {
                return $onlinetextsubmission->onlineformat;
            }
        }


         return 0;
    }


     /**
      * Display onlinetext word count in the submission status table
      *
      * @param stdClass $submission
      * @param bool $showviewlink - If the summary has been truncated set this to true
      * @return string
      */
    public function view_summary(stdClass $submission, & $showviewlink) {
        global $CFG;

        $onlinetextsubmission = $this->get_onlinetext_submission($submission->id);
        // always show the view link
        $showviewlink = true;

        if ($onlinetextsubmission) {
            $text = $this->assignment->render_editor_content(ASSIGNSUBMISSION_ONLINETEXT_FILEAREA,
                                                             $onlinetextsubmission->submission,
                                                             $this->get_type(),
                                                             'onlinetext',
                                                             'assignsubmission_onlinetext');

            $shorttext = shorten_text($text, 140);
            $plagiarismlinks = '';
            if (!empty($CFG->enableplagiarism)) {
                require_once($CFG->libdir . '/plagiarismlib.php');
                $plagiarismlinks .= plagiarism_get_links(array('userid' => $submission->userid,
                    'content' => trim(format_text($onlinetextsubmission->onlinetext, $onlinetextsubmission->onlineformat, array('context'=>$this->assignment->get_context()))),
                    'cmid' => $this->assignment->get_course_module()->id,
                    'course' => $this->assignment->get_course()->id,
                    'assignment' => $submission->assignment));
            }
            if ($text != $shorttext) {
                return $shorttext . $plagiarismlinks . get_string('numwords', 'assignsubmission_onlinetext', count_words($text));
            } else {
                return $shorttext . $plagiarismlinks;
            }
        }
        return '';
    }

    /**
     * Produce a list of files suitable for export that represent this submission
     *
     * @param stdClass $submission - For this is the submission data
     * @return array - return an array of files indexed by filename
     */
    public function get_files(stdClass $submission) {
        global $DB;
        $files = array();
        $onlinetextsubmission = $this->get_onlinetext_submission($submission->id);
        if ($onlinetextsubmission) {
            $user = $DB->get_record("user", array("id"=>$submission->userid),'id,username,firstname,lastname', MUST_EXIST);

            if (!$this->assignment->is_blind_marking()) {
                $filename = str_replace('_', '', fullname($user)) . '_' .
                            $this->assignment->get_uniqueid_for_user($submission->userid) . '_' .
                            $this->get_name() . '_';
                $prefix = clean_filename($filename);
            } else {
                $filename = get_string('participant', 'assign') . '_' .
                            $this->assignment->get_uniqueid_for_user($submission->userid) . '_' .
                            $this->get_name() . '_';
                $prefix = clean_filename($filename);
            }

            $finaltext = str_replace('@@PLUGINFILE@@/', $prefix, $onlinetextsubmission->onlinetext);
            $submissioncontent = "<html><body>". format_text($finaltext, $onlinetextsubmission->onlineformat, array('context'=>$this->assignment->get_context())). "</body></html>";      //fetched from database

            $files[get_string('onlinetextfilename', 'assignsubmission_onlinetext')] = array($submissioncontent);

            $fs = get_file_storage();

            $fsfiles = $fs->get_area_files($this->assignment->get_context()->id, 'assignsubmission_onlinetext', ASSIGNSUBMISSION_ONLINETEXT_FILEAREA, $submission->id, "timemodified", false);

            foreach ($fsfiles as $file) {
                $files[$file->get_filename()] = $file;
            }
        }

        return $files;
    }

    /**
     * Display the saved text content from the editor in the view table
     *
     * @param stdClass $submission
     * @return string
     */
    public function view(stdClass $submission) {
        $result = '';

        $onlinetextsubmission = $this->get_onlinetext_submission($submission->id);


        if ($onlinetextsubmission) {

            // render for portfolio API
            $result .= $this->assignment->render_editor_content(ASSIGNSUBMISSION_ONLINETEXT_FILEAREA, $onlinetextsubmission->submission, $this->get_type(), 'onlinetext', 'assignsubmission_onlinetext');

        }

        return $result;
    }

     /**
     * Return true if this plugin can upgrade an old Moodle 2.2 assignment of this type and version.
     *
     * @param string $type old assignment subtype
     * @param int $version old assignment version
     * @return bool True if upgrade is possible
     */
    public function can_upgrade($type, $version) {
        if ($type == 'online' && $version >= 2011112900) {
            return true;
        }
        return false;
    }


    /**
     * Upgrade the settings from the old assignment to the new plugin based one
     *
     * @param context $oldcontext - the database for the old assignment context
     * @param stdClass $oldassignment - the database for the old assignment instance
     * @param string $log record log events here
     * @return bool Was it a success?
     */
    public function upgrade_settings(context $oldcontext, stdClass $oldassignment, & $log) {
        // first upgrade settings (nothing to do)
        return true;
    }

    /**
     * Upgrade the submission from the old assignment to the new one
     *
     * @param context $oldcontext - the database for the old assignment context
     * @param stdClass $oldassignment The data record for the old assignment
     * @param stdClass $oldsubmission The data record for the old submission
     * @param stdClass $submission The data record for the new submission
     * @param string $log Record upgrade messages in the log
     * @return bool true or false - false will trigger a rollback
     */
    public function upgrade(context $oldcontext, stdClass $oldassignment, stdClass $oldsubmission, stdClass $submission, & $log) {
        global $DB;

        $onlinetextsubmission = new stdClass();
        $onlinetextsubmission->onlinetext = $oldsubmission->data1;
        $onlinetextsubmission->onlineformat = $oldsubmission->data2;

        $onlinetextsubmission->submission = $submission->id;
        $onlinetextsubmission->assignment = $this->assignment->get_instance()->id;

        if ($onlinetextsubmission->onlinetext === null) {
            $onlinetextsubmission->onlinetext = '';
        }

        if ($onlinetextsubmission->onlineformat === null) {
            $onlinetextsubmission->onlineformat = editors_get_preferred_format();
        }

        if (!$DB->insert_record('assignsubmission_onlinetext', $onlinetextsubmission) > 0) {
            $log .= get_string('couldnotconvertsubmission', 'mod_assign', $submission->userid);
            return false;
        }

        // now copy the area files
        $this->assignment->copy_area_files_for_upgrade($oldcontext->id,
                                                        'mod_assignment',
                                                        'submission',
                                                        $oldsubmission->id,
                                                        // New file area
                                                        $this->assignment->get_context()->id,
                                                        'assignsubmission_onlinetext',
                                                        ASSIGNSUBMISSION_ONLINETEXT_FILEAREA,
                                                        $submission->id);
        return true;
    }

    /**
     * Formatting for log info
     *
     * @param stdClass $submission The new submission
     * @return string
     */
    public function format_for_log(stdClass $submission) {
        // format the info for each submission plugin add_to_log
        $onlinetextsubmission = $this->get_onlinetext_submission($submission->id);
        $onlinetextloginfo = '';
        $text = format_text($onlinetextsubmission->onlinetext,
                            $onlinetextsubmission->onlineformat,
                            array('context'=>$this->assignment->get_context()));
        $onlinetextloginfo .= get_string('numwordsforlog', 'assignsubmission_onlinetext', count_words($text));

        return $onlinetextloginfo;
    }

    /**
     * The assignment has been deleted - cleanup
     *
     * @return bool
     */
    public function delete_instance() {
        global $DB;
        // will throw exception on failure
        $DB->delete_records('assignsubmission_onlinetext', array('assignment'=>$this->assignment->get_instance()->id));

        return true;
    }

    /**
     * No text is set for this plugin
     *
     * @param stdClass $submission
     * @return bool
     */
    public function is_empty(stdClass $submission) {
        $onlinetextsubmission = $this->get_onlinetext_submission($submission->id);

        return empty($onlinetextsubmission->onlinetext);
    }

    /**
     * Get file areas returns a list of areas this plugin stores files
     * @return array - An array of fileareas (keys) and descriptions (values)
     */
    public function get_file_areas() {
        return array(ASSIGNSUBMISSION_ONLINETEXT_FILEAREA=>$this->get_name());
    }

}


