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
 * This file contains the definition for the library class for codeblock submission plugin
 *
 * This class provides all the functionality for the new qbassign module.
 *
 * @package qbassignsubmission_codeblock
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
// File area for code block submission qbassignment.
define('qbassignsubmission_codeblock_FILEAREA', 'submissions_codeblock');
 
/**
 * library class for codeblock submission plugin extending submission plugin base class
 *
 * @package qbassignsubmission_codeblock
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbassign_submission_codeblock extends qbassign_submission_plugin {

    /**
     * Get the name of the code block submission plugin
     * @return string
     */
    public function get_name() {
        return get_string('codeblock', 'qbassignsubmission_codeblock');
    }


    /**
     * Get codeblock submission information from the database
     *
     * @param  int $submissionid
     * @return mixed
     */
    private function get_codeblock_submission($submissionid) {
        global $DB;

        return $DB->get_record('qbassignsubmission_codeblock', array('submission'=>$submissionid));
    }

    /**
     * Remove a submission.
     *
     * @param stdClass $submission The submission
     * @return boolean
     */
    public function remove(stdClass $submission) {
        global $DB;

        $submissionid = $submission ? $submission->id : 0;
        if ($submissionid) {
            $DB->delete_records('qbassignsubmission_codeblock', array('submission' => $submissionid));
        }
        return true;
    }

    /**
     * Get the settings for codeblock submission plugin
     *
     * @param MoodleQuickForm $mform The form to add elements to
     * @return void
     */
    public function get_settings(MoodleQuickForm $mform) {
        global $PAGE;
        $PAGE->requires->jquery();

        $PAGE->requires->js('/mod/qbassign/submission/codeblock/js/custom.js', true);


      //  echo '<pre>'; print_r($COURSE); exit;
        $name = 'CodeBlock Type';//get_string('Choose', 'qbassignsubmission_codeblock');
        $mname = get_string('Manual', 'qbassignsubmission_codeblock');
        $aname = get_string('automatic', 'qbassignsubmission_codeblock');
       
        $savedtype = $this->get_config('type');
        $savedlang = $this->get_config('lang');

        $wordlimitgrp = array();
       
        $wordlimitgrp[] = $mform->createElement('radio', 'qbassignsubmission_codeblock_type','',$mname,1);

        $wordlimitgrp[] = $mform->createElement('radio', 'qbassignsubmission_codeblock_type','', $aname,2);

        $mform->addGroup($wordlimitgrp, 'qbassignsubmission_codeblock_type_group',$name, '', ' ', false);

        $mform->setType('qbassignsubmission_codeblock_type', PARAM_INT);
        $mform->hideIf('qbassignsubmission_codeblock_type_group',
                       'qbassignsubmission_codeblock_enabled',
                       'notchecked');

        if(empty($savedtype))
        $mform->setDefault('qbassignsubmission_codeblock_type_group[qbassignsubmission_codeblock_type]', 1);
        else
        $mform->setDefault('qbassignsubmission_codeblock_type_group[qbassignsubmission_codeblock_type]', $savedtype);

        $mform->addElement('select', 'qbassignsubmission_codeblock_language', 'Language', array('python'=>'Python', 'sql'=>'SQL', 'javascript'=>'Javascript'));

        $mform->hideIf('qbassignsubmission_codeblock_language',
                       'qbassignsubmission_codeblock_enabled',
                       'notchecked');
        if(empty($savedlang))
        $mform->setDefault('qbassignsubmission_codeblock_language', 'python');
        else
        $mform->setDefault('qbassignsubmission_codeblock_language', $savedlang);

    }

    /**
     * Save the settings for codeblock submission plugin
     *
     * @param stdClass $data
     * @return bool
     */
    public function save_settings(stdClass $data) {
       // echo '<pre>'; print_r($data); exit;
        if (empty($data->qbassignsubmission_codeblock_type_group['qbassignsubmission_codeblock_type']) || empty($data->qbassignsubmission_codeblock_enabled)) {
            $type = 1;          
        } else {
            $type = $data->qbassignsubmission_codeblock_type_group['qbassignsubmission_codeblock_type'];            
        }

        $this->set_config('type', $type);

        if (empty($data->qbassignsubmission_codeblock_language)) {
            $lang = 'python';          
        } else {
            $lang = $data->qbassignsubmission_codeblock_language;            
        }

        $this->set_config('lang', $lang);
       
        return true;
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

        if (!isset($data->codeblock)) {
            $data->codeblock = '';
        }
        if (!isset($data->codeblockformat)) {
            $data->codeblockformat = editors_get_preferred_format();
        }

        if ($submission) {
            $codeblocksubmission = $this->get_codeblock_submission($submission->id);
            if ($codeblocksubmission) {
                $data->codeblock = $codeblocksubmission->codeblock;
                $data->codeblockformat = $codeblocksubmission->onlineformat;
            }

        }

        $data = file_prepare_standard_editor($data,
                                             'codeblock',
                                             $editoroptions,
                                             $this->qbassignment->get_context(),
                                             'qbassignsubmission_codeblock',
                                             qbassignsubmission_codeblock_FILEAREA,
                                             $submissionid);
        $mform->addElement('editor', 'codeblock_editor', $this->get_name(), null, $editoroptions);

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
            'maxbytes' => $this->qbassignment->get_course()->maxbytes,
            'context' => $this->qbassignment->get_context(),
            'return_types' => (FILE_INTERNAL | FILE_EXTERNAL | FILE_CONTROLLED_LINK),
            'removeorphaneddrafts' => true // Whether or not to remove any draft files which aren't referenced in the text.
        );
        return $editoroptions;
    }

    /**
     * Save data to the database and trigger plagiarism plugin,
     * if enabled, to scan the uploaded content via events trigger
     *
     * @param stdClass $submission
     * @param stdClass $data
     * @return bool
     */
    public function save(stdClass $submission, stdClass $data) {
        global $USER, $DB;

        $editoroptions = $this->get_edit_options();

        $data = file_postupdate_standard_editor($data,
                                                'codeblock',
                                                $editoroptions,
                                                $this->qbassignment->get_context(),
                                                'qbassignsubmission_codeblock',
                                                qbassignsubmission_codeblock_FILEAREA,
                                                $submission->id);

        $codeblocksubmission = $this->get_codeblock_submission($submission->id);

        $fs = get_file_storage();

        $files = $fs->get_area_files($this->qbassignment->get_context()->id,
                                     'qbassignsubmission_codeblock',
                                     qbassignsubmission_codeblock_FILEAREA,
                                     $submission->id,
                                     'id',
                                     false);

        // Check word count before submitting anything.
        $exceeded = $this->check_word_count(trim($data->codeblock));
        if ($exceeded) {
            $this->set_error($exceeded);
            return false;
        }

        $params = array(
            'context' => context_module::instance($this->qbassignment->get_course_module()->id),
            'courseid' => $this->qbassignment->get_course()->id,
            'objectid' => $submission->id,
            'other' => array(
                'pathnamehashes' => array_keys($files),
                'content' => trim($data->codeblock),
                'format' => $data->codeblock_editor['format']
            )
        );
        if (!empty($submission->userid) && ($submission->userid != $USER->id)) {
            $params['relateduserid'] = $submission->userid;
        }
        if ($this->qbassignment->is_blind_marking()) {
            $params['anonymous'] = 1;
        }
        $event = \qbassignsubmission_codeblock\event\assessable_uploaded::create($params);
        $event->trigger();

        $groupname = null;
        $groupid = 0;
        // Get the group name as other fields are not transcribed in the logs and this information is important.
        if (empty($submission->userid) && !empty($submission->groupid)) {
            $groupname = $DB->get_field('groups', 'name', array('id' => $submission->groupid), MUST_EXIST);
            $groupid = $submission->groupid;
        } else {
            $params['relateduserid'] = $submission->userid;
        }

        $count = count_words($data->codeblock);

        // Unset the objectid and other field from params for use in submission events.
        unset($params['objectid']);
        unset($params['other']);
        $params['other'] = array(
            'submissionid' => $submission->id,
            'submissionattempt' => $submission->attemptnumber,
            'submissionstatus' => $submission->status,
            'codeblockwordcount' => $count,
            'groupid' => $groupid,
            'groupname' => $groupname
        );

        if ($codeblocksubmission) {

            $codeblocksubmission->codeblock = $data->codeblock;
            $codeblocksubmission->onlineformat = $data->codeblock_editor['format'];
            $params['objectid'] = $codeblocksubmission->id;
            $updatestatus = $DB->update_record('qbassignsubmission_codeblock', $codeblocksubmission);
            $event = \qbassignsubmission_codeblock\event\submission_updated::create($params);
            $event->set_qbassign($this->qbassignment);
            $event->trigger();
            return $updatestatus;
        } else {

            $codeblocksubmission = new stdClass();
            $codeblocksubmission->codeblock = $data->codeblock;
            $codeblocksubmission->onlineformat = $data->codeblock_editor['format'];

            $codeblocksubmission->submission = $submission->id;
            $codeblocksubmission->qbassignment = $this->qbassignment->get_instance()->id;
            $codeblocksubmission->id = $DB->insert_record('qbassignsubmission_codeblock', $codeblocksubmission);
            $params['objectid'] = $codeblocksubmission->id;
            $event = \qbassignsubmission_codeblock\event\submission_created::create($params);
            $event->set_qbassign($this->qbassignment);
            $event->trigger();
            return $codeblocksubmission->id > 0;
        }
    }

    /**
     * Return a list of the text fields that can be imported/exported by this plugin
     *
     * @return array An array of field names and descriptions. (name=>description, ...)
     */
    public function get_editor_fields() {
        return array('codeblock' => get_string('pluginname', 'qbassignsubmission_codeblock'));
    }

    /**
     * Get the saved text content from the editor
     *
     * @param string $name
     * @param int $submissionid
     * @return string
     */
    public function get_editor_text($name, $submissionid) {
        if ($name == 'codeblock') {
            $codeblocksubmission = $this->get_codeblock_submission($submissionid);
            if ($codeblocksubmission) {
                return $codeblocksubmission->codeblock;
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
        if ($name == 'codeblock') {
            $codeblocksubmission = $this->get_codeblock_submission($submissionid);
            if ($codeblocksubmission) {
                return $codeblocksubmission->onlineformat;
            }
        }

        return 0;
    }


     /**
      * Display codeblock word count in the submission status table
      *
      * @param stdClass $submission
      * @param bool $showviewlink - If the summary has been truncated set this to true
      * @return string
      */
    public function view_summary(stdClass $submission, & $showviewlink) {
        global $CFG;

        $codeblocksubmission = $this->get_codeblock_submission($submission->id);
        // Always show the view link.
        $showviewlink = true;

        if ($codeblocksubmission) {
            // This contains the shortened version of the text plus an optional 'Export to portfolio' button.
            $text = $this->qbassignment->render_editor_content(qbassignsubmission_codeblock_FILEAREA,
                                                             $codeblocksubmission->submission,
                                                             $this->get_type(),
                                                             'codeblock',
                                                             'qbassignsubmission_codeblock', true);

            // The actual submission text.
            $codeblock = trim($codeblocksubmission->codeblock);
            // The shortened version of the submission text.
            $shorttext = shorten_text($codeblock, 140);

            $plagiarismlinks = '';

            if (!empty($CFG->enableplagiarism)) {
                require_once($CFG->libdir . '/plagiarismlib.php');

                $plagiarismlinks .= plagiarism_get_links(array('userid' => $submission->userid,
                    'content' => $codeblock,
                    'cmid' => $this->qbassignment->get_course_module()->id,
                    'course' => $this->qbassignment->get_course()->id,
                    'qbassignment' => $submission->qbassignment));
            }
            // We compare the actual text submission and the shortened version. If they are not equal, we show the word count.
            if ($codeblock != $shorttext) {
                $wordcount = get_string('numwords', 'qbassignsubmission_codeblock', count_words($codeblock));

                return $plagiarismlinks . $wordcount . $text;
            } else {
                return $plagiarismlinks . $text;
            }
        }
        return '';
    }

    /**
     * Produce a list of files suitable for export that represent this submission.
     *
     * @param stdClass $submission - For this is the submission data
     * @param stdClass $user - This is the user record for this submission
     * @return array - return an array of files indexed by filename
     */
    public function get_files(stdClass $submission, stdClass $user) {
        global $DB;

        $files = array();
        $codeblocksubmission = $this->get_codeblock_submission($submission->id);

        // Note that this check is the same logic as the result from the is_empty function but we do
        // not call it directly because we already have the submission record.
        if ($codeblocksubmission) {
            // Do not pass the text through format_text. The result may not be displayed in Moodle and
            // may be passed to external services such as document conversion or portfolios.
            $formattedtext = $this->qbassignment->download_rewrite_pluginfile_urls($codeblocksubmission->codeblock, $user, $this);
            $head = '<head><meta charset="UTF-8"></head>';
            $submissioncontent = '<!DOCTYPE html><html>' . $head . '<body>'. $formattedtext . '</body></html>';

            $filename = get_string('codeblockfilename', 'qbassignsubmission_codeblock');
            $files[$filename] = array($submissioncontent);

            $fs = get_file_storage();

            $fsfiles = $fs->get_area_files($this->qbassignment->get_context()->id,
                                           'qbassignsubmission_codeblock',
                                           qbassignsubmission_codeblock_FILEAREA,
                                           $submission->id,
                                           'timemodified',
                                           false);

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
        global $CFG;
        $result = '';
        $plagiarismlinks = '';

        $codeblocksubmission = $this->get_codeblock_submission($submission->id);

        if ($codeblocksubmission) {

            // Render for portfolio API.
            $result .= $this->qbassignment->render_editor_content(qbassignsubmission_codeblock_FILEAREA,
                                                                $codeblocksubmission->submission,
                                                                $this->get_type(),
                                                                'codeblock',
                                                                'qbassignsubmission_codeblock');

            if (!empty($CFG->enableplagiarism)) {
                require_once($CFG->libdir . '/plagiarismlib.php');

                $plagiarismlinks .= plagiarism_get_links(array('userid' => $submission->userid,
                    'content' => trim($codeblocksubmission->codeblock),
                    'cmid' => $this->qbassignment->get_course_module()->id,
                    'course' => $this->qbassignment->get_course()->id,
                    'qbassignment' => $submission->qbassignment));
            }
        }

        return $plagiarismlinks . $result;
    }

    /**
     * Return true if this plugin can upgrade an old Moodle 2.2 qbassignment of this type and version.
     *
     * @param string $type old qbassignment subtype
     * @param int $version old qbassignment version
     * @return bool True if upgrade is possible
     */
    public function can_upgrade($type, $version) {
        if ($type == 'online' && $version >= 2011112900) {
            return true;
        }
        return false;
    }


    /**
     * Upgrade the settings from the old qbassignment to the new plugin based one
     *
     * @param context $oldcontext - the database for the old qbassignment context
     * @param stdClass $oldqbassignment - the database for the old qbassignment instance
     * @param string $log record log events here
     * @return bool Was it a success?
     */
    public function upgrade_settings(context $oldcontext, stdClass $oldqbassignment, & $log) {
        // No settings to upgrade.
        return true;
    }

    /**
     * Upgrade the submission from the old qbassignment to the new one
     *
     * @param context $oldcontext - the database for the old qbassignment context
     * @param stdClass $oldqbassignment The data record for the old qbassignment
     * @param stdClass $oldsubmission The data record for the old submission
     * @param stdClass $submission The data record for the new submission
     * @param string $log Record upgrade messages in the log
     * @return bool true or false - false will trigger a rollback
     */
    public function upgrade(context $oldcontext,
                            stdClass $oldqbassignment,
                            stdClass $oldsubmission,
                            stdClass $submission,
                            & $log) {
        global $DB;

        $codeblocksubmission = new stdClass();
        $codeblocksubmission->codeblock = $oldsubmission->data1;
        $codeblocksubmission->onlineformat = $oldsubmission->data2;

        $codeblocksubmission->submission = $submission->id;
        $codeblocksubmission->qbassignment = $this->qbassignment->get_instance()->id;

        if ($codeblocksubmission->codeblock === null) {
            $codeblocksubmission->codeblock = '';
        }

        if ($codeblocksubmission->onlineformat === null) {
            $codeblocksubmission->onlineformat = editors_get_preferred_format();
        }

        if (!$DB->insert_record('qbassignsubmission_codeblock', $codeblocksubmission) > 0) {
            $log .= get_string('couldnotconvertsubmission', 'mod_qbassign', $submission->userid);
            return false;
        }

        // Now copy the area files.
        $this->qbassignment->copy_area_files_for_upgrade($oldcontext->id,
                                                        'mod_qbassignment',
                                                        'submission',
                                                        $oldsubmission->id,
                                                        $this->qbassignment->get_context()->id,
                                                        'qbassignsubmission_codeblock',
                                                        qbassignsubmission_codeblock_FILEAREA,
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
        // Format the info for each submission plugin (will be logged).
        $codeblocksubmission = $this->get_codeblock_submission($submission->id);
        $codeblockloginfo = '';
        $codeblockloginfo .= get_string('numwordsforlog',
                                         'qbassignsubmission_codeblock',
                                         count_words($codeblocksubmission->codeblock));

        return $codeblockloginfo;
    }

    /**
     * The qbassignment has been deleted - cleanup
     *
     * @return bool
     */
    public function delete_instance() {
        global $DB;
        $DB->delete_records('qbassignsubmission_codeblock',
                            array('qbassignment'=>$this->qbassignment->get_instance()->id));

        return true;
    }

    /**
     * No text is set for this plugin
     *
     * @param stdClass $submission
     * @return bool
     */
    public function is_empty(stdClass $submission) {
        $codeblocksubmission = $this->get_codeblock_submission($submission->id);
        $wordcount = 0;
        $hasinsertedresources = false;

        if (isset($codeblocksubmission->codeblock)) {
            $wordcount = count_words(trim($codeblocksubmission->codeblock));
            // Check if the code block submission contains video, audio or image elements
            // that can be ignored and stripped by count_words().
            $hasinsertedresources = preg_match('/<\s*((video|audio)[^>]*>(.*?)<\s*\/\s*(video|audio)>)|(img[^>]*>(.*?))/',
                    trim($codeblocksubmission->codeblock));
        }

        return $wordcount == 0 && !$hasinsertedresources;
    }

    /**
     * Determine if a submission is empty
     *
     * This is distinct from is_empty in that it is intended to be used to
     * determine if a submission made before saving is empty.
     *
     * @param stdClass $data The submission data
     * @return bool
     */
    public function submission_is_empty(stdClass $data) {
        if (!isset($data->codeblock_editor)) {
            return true;
        }
        $wordcount = 0;
        $hasinsertedresources = false;

        if (isset($data->codeblock_editor['text'])) {
            $wordcount = count_words(trim((string)$data->codeblock_editor['text']));
            // Check if the code block submission contains video, audio or image elements
            // that can be ignored and stripped by count_words().
            $hasinsertedresources = preg_match('/<\s*((video|audio)[^>]*>(.*?)<\s*\/\s*(video|audio)>)|(img[^>]*>(.*?))/',
                    trim((string)$data->codeblock_editor['text']));
        }

        return $wordcount == 0 && !$hasinsertedresources;
    }

    /**
     * Get file areas returns a list of areas this plugin stores files
     * @return array - An array of fileareas (keys) and descriptions (values)
     */
    public function get_file_areas() {
        return array(qbassignsubmission_codeblock_FILEAREA=>$this->get_name());
    }

    /**
     * Copy the student's submission from a previous submission. Used when a student opts to base their resubmission
     * on the last submission.
     * @param stdClass $sourcesubmission
     * @param stdClass $destsubmission
     */
    public function copy_submission(stdClass $sourcesubmission, stdClass $destsubmission) {
        global $DB;

        // Copy the files across (attached via the text editor).
        $contextid = $this->qbassignment->get_context()->id;
        $fs = get_file_storage();
        $files = $fs->get_area_files($contextid, 'qbassignsubmission_codeblock',
                                     qbassignsubmission_codeblock_FILEAREA, $sourcesubmission->id, 'id', false);
        foreach ($files as $file) {
            $fieldupdates = array('itemid' => $destsubmission->id);
            $fs->create_file_from_storedfile($fieldupdates, $file);
        }

        // Copy the qbassignsubmission_codeblock record.
        $codeblocksubmission = $this->get_codeblock_submission($sourcesubmission->id);
        if ($codeblocksubmission) {
            unset($codeblocksubmission->id);
            $codeblocksubmission->submission = $destsubmission->id;
            $DB->insert_record('qbassignsubmission_codeblock', $codeblocksubmission);
        }
        return true;
    }

    /**
     * Return a description of external params suitable for uploading an codeblock submission from a webservice.
     *
     * @return external_description|null
     */
    public function get_external_parameters() {
        $editorparams = array('text' => new external_value(PARAM_RAW, 'The text for this submission.'),
                              'format' => new external_value(PARAM_INT, 'The format for this submission'),
                              'itemid' => new external_value(PARAM_INT, 'The draft area id for files attached to the submission'));
        $editorstructure = new external_single_structure($editorparams, 'Editor structure', VALUE_OPTIONAL);
        return array('codeblock_editor' => $editorstructure);
    }

    /**
     * Compare word count of codeblock submission to word limit, and return result.
     *
     * @param string $submissiontext codeblock submission text from editor
     * @return string Error message if limit is enabled and exceeded, otherwise null
     */
    public function check_word_count($submissiontext) {
        global $OUTPUT;

        $wordlimitenabled = $this->get_config('wordlimitenabled');
        $wordlimit = $this->get_config('wordlimit');

        if ($wordlimitenabled == 0) {
            return null;
        }

        // Count words and compare to limit.
        $wordcount = count_words($submissiontext);
        if ($wordcount <= $wordlimit) {
            return null;
        } else {
            $errormsg = get_string('wordlimitexceeded', 'qbassignsubmission_codeblock',
                    array('limit' => $wordlimit, 'count' => $wordcount));
            return $OUTPUT->error_text($errormsg);
        }
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


