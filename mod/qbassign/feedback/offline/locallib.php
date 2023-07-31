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
 * @package   qbassignfeedback_offline
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \mod_qbassign\output\qbassign_header;

require_once($CFG->dirroot.'/grade/grading/lib.php');

/**
 * library class for file feedback plugin extending feedback plugin base class
 *
 * @package   qbassignfeedback_offline
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class qbassign_feedback_offline extends qbassign_feedback_plugin {

    /** @var boolean|null $enabledcache Cached lookup of the is_enabled function */
    private $enabledcache = null;

    /**
     * Get the name of the file feedback plugin
     * @return string
     */
    public function get_name() {
        return get_string('pluginname', 'qbassignfeedback_offline');
    }

    /**
     * Get form elements for grading form
     *
     * @param stdClass $grade
     * @param MoodleQuickForm $mform
     * @param stdClass $data
     * @return bool true if elements were added to the form
     */
    public function get_form_elements($grade, MoodleQuickForm $mform, stdClass $data) {
        return false;
    }

    /**
     * Return true if there are no feedback files
     * @param stdClass $grade
     */
    public function is_empty(stdClass $grade) {
        return true;
    }

    /**
     * This plugin does not save through the normal interface so this returns false.
     *
     * @param stdClass $grade The grade.
     * @param stdClass $data Form data from the feedback form.
     * @return boolean - False
     */
    public function is_feedback_modified(stdClass $grade, stdClass $data) {
        return false;
    }

    /**
     * Loop through uploaded grades and update the grades for this qbassignment
     *
     * @param int $draftid - The unique draft item id for this import
     * @param int $importid - The unique import ID for this csv import operation
     * @param bool $ignoremodified - Ignore the last modified date when checking fields
     * @param string $encoding - Encoding of the file being processed.
     * @param string $separator - The character used to separate the information.
     * @return string - The html response
     */
    public function process_import_grades($draftid, $importid, $ignoremodified, $encoding = 'utf-8', $separator = 'comma') {
        global $USER, $DB;

        require_sesskey();
        require_capability('mod/qbassign:grade', $this->qbassignment->get_context());

        $gradeimporter = new qbassignfeedback_offline_grade_importer($importid, $this->qbassignment, $encoding, $separator);

        $context = context_user::instance($USER->id);
        $fs = get_file_storage();
        if (!$files = $fs->get_area_files($context->id, 'user', 'draft', $draftid, 'id DESC', false)) {
            redirect(new moodle_url('view.php',
                                array('id'=>$this->qbassignment->get_course_module()->id,
                                      'action'=>'grading')));
            return;
        }
        $file = reset($files);

        $csvdata = $file->get_content();

        if ($csvdata) {
            $gradeimporter->parsecsv($csvdata);
        }
        if (!$gradeimporter->init()) {
            $thisurl = new moodle_url('/mod/qbassign/view.php', array('action'=>'viewpluginpage',
                                                                     'pluginsubtype'=>'qbassignfeedback',
                                                                     'plugin'=>'offline',
                                                                     'pluginaction'=>'uploadgrades',
                                                                     'id' => $this->qbassignment->get_course_module()->id));
            throw new \moodle_exception('invalidgradeimport', 'qbassignfeedback_offline', $thisurl);
            return;
        }
        // Does this qbassignment use a scale?
        $scaleoptions = null;
        if ($this->qbassignment->get_instance()->grade < 0) {
            if ($scale = $DB->get_record('scale', array('id'=>-($this->qbassignment->get_instance()->grade)))) {
                $scaleoptions = make_menu_from_list($scale->scale);
            }
        }
        // We may need to upgrade the gradebook comments after this update.
        $adminconfig = $this->qbassignment->get_admin_config();
        $gradebookplugin = $adminconfig->feedback_plugin_for_gradebook;

        $updategradecount = 0;
        $updatefeedbackcount = 0;
        while ($record = $gradeimporter->next()) {
            $user = $record->user;
            $modified = $record->modified;
            $userdesc = fullname($user);
            $usergrade = $this->qbassignment->get_user_grade($user->id, false);

            if (!empty($scaleoptions)) {
                // This is a scale - we need to convert any grades to indexes in the scale.
                $scaleindex = array_search($record->grade, $scaleoptions);
                if ($scaleindex !== false) {
                    $record->grade = $scaleindex;
                } else {
                    $record->grade = '';
                }
            } else {
                $record->grade = unformat_float($record->grade);
            }

            // Note: Do not count the seconds when comparing modified dates.
            $skip = false;
            $stalemodificationdate = ($usergrade && $usergrade->timemodified > ($modified + 60));

            if ($usergrade && $usergrade->grade == $record->grade) {
                // Skip - grade not modified.
                $skip = true;
            } else if (!isset($record->grade) || $record->grade === '' || $record->grade < 0) {
                // Skip - grade has no value.
                $skip = true;
            } else if (!$ignoremodified && $stalemodificationdate) {
                // Skip - grade has been modified.
                $skip = true;
            } else if ($this->qbassignment->grading_disabled($record->user->id)) {
                // Skip grade is locked.
                $skip = true;
            } else if (($this->qbassignment->get_instance()->grade > -1) &&
                      (($record->grade < 0) || ($record->grade > $this->qbassignment->get_instance()->grade))) {
                // Out of range.
                $skip = true;
            }

            if (!$skip) {
                $grade = $this->qbassignment->get_user_grade($record->user->id, true);

                $grade->grade = $record->grade;
                $grade->grader = $USER->id;
                if ($this->qbassignment->update_grade($grade)) {
                    $this->qbassignment->notify_grade_modified($grade);
                    $updategradecount += 1;
                }
            }

            if ($ignoremodified || !$stalemodificationdate) {
                foreach ($record->feedback as $feedback) {
                    $plugin = $feedback['plugin'];
                    $field = $feedback['field'];
                    $newvalue = $feedback['value'];
                    $description = $feedback['description'];
                    $oldvalue = '';
                    if ($usergrade) {
                        $oldvalue = $plugin->get_editor_text($field, $usergrade->id);
                        if (empty($oldvalue)) {
                            $oldvalue = '';
                        }
                    }
                    if ($newvalue != $oldvalue) {
                        $updatefeedbackcount += 1;
                        $grade = $this->qbassignment->get_user_grade($record->user->id, true);
                        $this->qbassignment->notify_grade_modified($grade);
                        $plugin->set_editor_text($field, $newvalue, $grade->id);

                        // If this is the gradebook comments plugin - post an update to the gradebook.
                        if (($plugin->get_subtype() . '_' . $plugin->get_type()) == $gradebookplugin) {
                            $grade->feedbacktext = $plugin->text_for_gradebook($grade);
                            $grade->feedbackformat = $plugin->format_for_gradebook($grade);
                            $this->qbassignment->update_grade($grade);
                        }
                    }
                }
            }
        }
        $gradeimporter->close(true);

        $renderer = $this->qbassignment->get_renderer();
        $o = '';

        $o .= $renderer->render(new qbassign_header($this->qbassignment->get_instance(),
                                                  $this->qbassignment->get_context(),
                                                  false,
                                                  $this->qbassignment->get_course_module()->id,
                                                  get_string('importgrades', 'qbassignfeedback_offline')));
        $strparams = [
            'gradeupdatescount' => $updategradecount,
            'feedbackupdatescount' => $updatefeedbackcount,
        ];
        $o .= $renderer->box(get_string('updatedgrades', 'qbassignfeedback_offline', $strparams));
        $url = new moodle_url('view.php',
                              array('id'=>$this->qbassignment->get_course_module()->id,
                                    'action'=>'grading'));
        $o .= $renderer->continue_button($url);
        $o .= $renderer->render_footer();
        return $o;
    }

    /**
     * Display upload grades form
     *
     * @return string The response html
     */
    public function upload_grades() {
        global $CFG, $USER;

        require_capability('mod/qbassign:grade', $this->qbassignment->get_context());
        require_once($CFG->dirroot . '/mod/qbassign/feedback/offline/uploadgradesform.php');
        require_once($CFG->dirroot . '/mod/qbassign/feedback/offline/importgradesform.php');
        require_once($CFG->dirroot . '/mod/qbassign/feedback/offline/importgradeslib.php');
        require_once($CFG->libdir . '/csvlib.class.php');

        $mform = new qbassignfeedback_offline_upload_grades_form(null,
                                                              array('context'=>$this->qbassignment->get_context(),
                                                                    'cm'=>$this->qbassignment->get_course_module()->id));

        $o = '';

        $confirm = optional_param('confirm', 0, PARAM_BOOL);
        $renderer = $this->qbassignment->get_renderer();

        if ($mform->is_cancelled()) {
            redirect(new moodle_url('view.php',
                                    array('id'=>$this->qbassignment->get_course_module()->id,
                                          'action'=>'grading')));
            return;
        } else if (($data = $mform->get_data()) &&
                   ($csvdata = $mform->get_file_content('gradesfile'))) {

            $importid = csv_import_reader::get_new_iid('qbassignfeedback_offline');
            $gradeimporter = new qbassignfeedback_offline_grade_importer($importid, $this->qbassignment,
                    $data->encoding, $data->separator);
            // File exists and was valid.
            $ignoremodified = !empty($data->ignoremodified);

            $draftid = $data->gradesfile;

            // Preview import.

            $mform = new qbassignfeedback_offline_import_grades_form(null, array('qbassignment'=>$this->qbassignment,
                                                                       'csvdata'=>$csvdata,
                                                                       'ignoremodified'=>$ignoremodified,
                                                                       'gradeimporter'=>$gradeimporter,
                                                                       'draftid'=>$draftid));

            $o .= $renderer->render(new qbassign_header($this->qbassignment->get_instance(),
                                                            $this->qbassignment->get_context(),
                                                            false,
                                                            $this->qbassignment->get_course_module()->id,
                                                            get_string('confirmimport', 'qbassignfeedback_offline')));
            $o .= $renderer->render(new qbassign_form('confirmimport', $mform));
            $o .= $renderer->render_footer();
        } else if ($confirm) {
            $importid = optional_param('importid', 0, PARAM_INT);
            $draftid = optional_param('draftid', 0, PARAM_INT);
            $encoding = optional_param('encoding', 'utf-8', PARAM_ALPHANUMEXT);
            $separator = optional_param('separator', 'comma', PARAM_ALPHA);
            $ignoremodified = optional_param('ignoremodified', 0, PARAM_BOOL);
            $gradeimporter = new qbassignfeedback_offline_grade_importer($importid, $this->qbassignment, $encoding, $separator);
            $mform = new qbassignfeedback_offline_import_grades_form(null, array('qbassignment'=>$this->qbassignment,
                                                                       'csvdata'=>'',
                                                                       'ignoremodified'=>$ignoremodified,
                                                                       'gradeimporter'=>$gradeimporter,
                                                                       'draftid'=>$draftid));
            if ($mform->is_cancelled()) {
                redirect(new moodle_url('view.php',
                                        array('id'=>$this->qbassignment->get_course_module()->id,
                                              'action'=>'grading')));
                return;
            }

            $o .= $this->process_import_grades($draftid, $importid, $ignoremodified, $encoding, $separator);
        } else {

            $o .= $renderer->render(new qbassign_header($this->qbassignment->get_instance(),
                                                            $this->qbassignment->get_context(),
                                                            false,
                                                            $this->qbassignment->get_course_module()->id,
                                                            get_string('uploadgrades', 'qbassignfeedback_offline')));
            $o .= $renderer->render(new qbassign_form('batchuploadfiles', $mform));
            $o .= $renderer->render_footer();
        }

        return $o;
    }

    /**
     * Download a marking worksheet
     *
     * @return string The response html
     */
    public function download_grades() {
        global $CFG;

        require_capability('mod/qbassign:grade', $this->qbassignment->get_context());
        require_once($CFG->dirroot . '/mod/qbassign/gradingtable.php');

        $groupmode = groups_get_activity_groupmode($this->qbassignment->get_course_module());
        // All users.
        $groupid = 0;
        $groupname = '';
        if ($groupmode) {
            $groupid = groups_get_activity_group($this->qbassignment->get_course_module(), true);
            $groupname = groups_get_group_name($groupid) . '-';
        }
        $filename = clean_filename(get_string('offlinegradingworksheet', 'qbassignfeedback_offline') . '-' .
                                   $this->qbassignment->get_course()->shortname . '-' .
                                   $this->qbassignment->get_instance()->name . '-' .
                                   $groupname .
                                   $this->qbassignment->get_course_module()->id);

        $table = new qbassign_grading_table($this->qbassignment, 0, '', 0, false, $filename);

        $table->out(0, false);
        return;
    }

    /**
     * Print a sub page in this plugin
     *
     * @param string $action - The plugin action
     * @return string The response html
     */
    public function view_page($action) {
        if ($action == 'downloadgrades') {
            return $this->download_grades();
        } else if ($action == 'uploadgrades') {
            return $this->upload_grades();
        }

        return '';
    }

    /**
     * Return a list of the grading actions performed by this plugin
     * This plugin supports upload zip
     *
     * @return array The list of grading actions
     */
    public function get_grading_actions() {
        return array('uploadgrades'=>get_string('uploadgrades', 'qbassignfeedback_offline'),
                    'downloadgrades'=>get_string('downloadgrades', 'qbassignfeedback_offline'));
    }

    /**
     * Override the default is_enabled to disable this plugin if advanced grading is active
     *
     * @return bool
     */
    public function is_enabled() {
        if ($this->enabledcache === null) {
            $gradingmanager = get_grading_manager($this->qbassignment->get_context(), 'mod_qbassign', 'submissions');
            $controller = $gradingmanager->get_active_controller();
            $active = !empty($controller);

            if ($active) {
                $this->enabledcache = false;
            } else {
                $this->enabledcache = parent::is_enabled();
            }
        }
        return $this->enabledcache;
    }

    /**
     * Do not show this plugin in the grading table or on the front page
     *
     * @return bool
     */
    public function has_user_summary() {
        return false;
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
