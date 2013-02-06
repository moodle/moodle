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
 * @package   assignfeedback_offline
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/grade/grading/lib.php');

/**
 * library class for file feedback plugin extending feedback plugin base class
 *
 * @package   assignfeedback_offline
 * @copyright 2012 NetSpot {@link http://www.netspot.com.au}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class assign_feedback_offline extends assign_feedback_plugin {

    /**
     * Get the name of the file feedback plugin
     * @return string
     */
    public function get_name() {
        return get_string('pluginname', 'assignfeedback_offline');
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
     * Loop through uploaded grades and update the grades for this assignment
     *
     * @param int $draftid - The unique draft item id for this import
     * @param int $importid - The unique import ID for this csv import operation
     * @param bool $ignoremodified - Ignore the last modified date when checking fields
     * @return string - The html response
     */
    public function process_import_grades($draftid, $importid, $ignoremodified) {
        global $USER, $DB;

        require_sesskey();
        require_capability('mod/assign:grade', $this->assignment->get_context());

        $gradeimporter = new assignfeedback_offline_grade_importer($importid, $this->assignment);

        $context = get_context_instance(CONTEXT_USER, $USER->id);
        $fs = get_file_storage();
        if (!$files = $fs->get_area_files($context->id, 'user', 'draft', $draftid, 'id DESC', false)) {
            redirect(new moodle_url('view.php',
                                array('id'=>$this->assignment->get_course_module()->id,
                                      'action'=>'grading')));
            return;
        }
        $file = reset($files);

        $csvdata = $file->get_content();

        if ($csvdata) {
            $gradeimporter->parsecsv($csvdata);
        }
        if (!$gradeimporter->init()) {
            $thisurl = new moodle_url('/mod/assign/view.php', array('action'=>'viewpluginpage',
                                                                     'pluginsubtype'=>'assignfeedback',
                                                                     'plugin'=>'offline',
                                                                     'pluginaction'=>'uploadgrades',
                                                                     'id'=>$assignment->get_course_module()->id));
            print_error('invalidgradeimport', 'assignfeedback_offline', $thisurl);
            return;
        }
        // Does this assignment use a scale?
        $scaleoptions = null;
        if ($this->assignment->get_instance()->grade < 0) {
            $scale = $DB->get_record('scale', array('id'=>-($this->assignment->get_instance()->grade)));
            if ($scale) {
                $scaleoptions = explode(',', $scale->scale);
            }
        }
        // We may need to upgrade the gradebook comments after this update.
        $adminconfig = $this->assignment->get_admin_config();
        $gradebookplugin = $adminconfig->feedback_plugin_for_gradebook;

        $updatecount = 0;
        while ($record = $gradeimporter->next()) {
            $user = $record->user;
            $modified = $record->modified;
            $userdesc = fullname($user);
            $usergrade = $this->assignment->get_user_grade($user->id, false);

            if (!empty($scaleoptions)) {
                // This is a scale - we need to convert any grades to indexes in the scale.
                $scaleindex = array_search($record->grade, $scaleoptions);
                if ($scaleindex !== false) {
                    $record->grade = $scaleindex + 1;
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
            } else if ($this->assignment->grading_disabled($record->user->id)) {
                // Skip grade is locked.
                $skip = true;
            } else if (($this->assignment->get_instance()->grade > -1) &&
                      (($record->grade < 0) || ($record->grade > $this->assignment->get_instance()->grade))) {
                // Out of range.
                $skip = true;
            }

            if (!$skip) {
                $grade = $this->assignment->get_user_grade($record->user->id, true);

                $grade->grade = $record->grade;
                $grade->grader = $USER->id;
                if ($this->assignment->update_grade($grade)) {
                    $this->assignment->notify_grade_modified($grade);
                    $this->assignment->add_to_log('grade submission', $this->assignment->format_grade_for_log($grade));
                    $updatecount += 1;
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
                        $updatecount += 1;
                        $grade = $this->assignment->get_user_grade($record->user->id, true);
                        $this->assignment->notify_grade_modified($grade);
                        if ($plugin->set_editor_text($field, $newvalue, $grade->id)) {
                            $logdesc = get_string('feedbackupdate', 'assignfeedback_offline',
                                                  array('field'=>$description,
                                                        'student'=>$userdesc,
                                                        'text'=>$newvalue));

                            $this->assignment->add_to_log('save grading feedback', $logdesc);
                        }

                        // If this is the gradebook comments plugin - post an update to the gradebook.
                        if (($plugin->get_subtype() . '_' . $plugin->get_type()) == $gradebookplugin) {
                            $grade->feedbacktext = $plugin->text_for_gradebook($grade);
                            $grade->feedbackformat = $plugin->format_for_gradebook($grade);
                            $this->assignment->update_grade($grade);
                        }
                    }
                }
            }
        }
        $gradeimporter->close(true);

        $renderer = $this->assignment->get_renderer();
        $o = '';

        $o .= $renderer->render(new assign_header($this->assignment->get_instance(),
                                                  $this->assignment->get_context(),
                                                  false,
                                                  $this->assignment->get_course_module()->id,
                                                  get_string('importgrades', 'assignfeedback_offline')));
        $o .= $renderer->box(get_string('updatedgrades', 'assignfeedback_offline', $updatecount));
        $url = new moodle_url('view.php',
                              array('id'=>$this->assignment->get_course_module()->id,
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

        require_capability('mod/assign:grade', $this->assignment->get_context());
        require_once($CFG->dirroot . '/mod/assign/feedback/offline/uploadgradesform.php');
        require_once($CFG->dirroot . '/mod/assign/feedback/offline/importgradesform.php');
        require_once($CFG->dirroot . '/mod/assign/feedback/offline/importgradeslib.php');
        require_once($CFG->libdir . '/csvlib.class.php');

        $mform = new assignfeedback_offline_upload_grades_form(null,
                                                              array('context'=>$this->assignment->get_context(),
                                                                    'cm'=>$this->assignment->get_course_module()->id));

        $o = '';

        $confirm = optional_param('confirm', 0, PARAM_BOOL);
        $renderer = $this->assignment->get_renderer();

        if ($mform->is_cancelled()) {
            redirect(new moodle_url('view.php',
                                    array('id'=>$this->assignment->get_course_module()->id,
                                          'action'=>'grading')));
            return;
        } else if (($data = $mform->get_data()) &&
                   ($csvdata = $mform->get_file_content('gradesfile'))) {

            $importid = csv_import_reader::get_new_iid('assignfeedback_offline');
            $gradeimporter = new assignfeedback_offline_grade_importer($importid, $this->assignment);
            // File exists and was valid.
            $ignoremodified = !empty($data->ignoremodified);

            $draftid = $data->gradesfile;

            // Preview import.

            $mform = new assignfeedback_offline_import_grades_form(null, array('assignment'=>$this->assignment,
                                                                       'csvdata'=>$csvdata,
                                                                       'ignoremodified'=>$ignoremodified,
                                                                       'gradeimporter'=>$gradeimporter,
                                                                       'draftid'=>$draftid));

            $o .= $renderer->render(new assign_header($this->assignment->get_instance(),
                                                            $this->assignment->get_context(),
                                                            false,
                                                            $this->assignment->get_course_module()->id,
                                                            get_string('confirmimport', 'assignfeedback_offline')));
            $o .= $renderer->render(new assign_form('confirmimport', $mform));
            $o .= $renderer->render_footer();
        } else if ($confirm) {

            $importid = optional_param('importid', 0, PARAM_INT);
            $draftid = optional_param('draftid', 0, PARAM_INT);
            $ignoremodified = optional_param('ignoremodified', 0, PARAM_BOOL);
            $gradeimporter = new assignfeedback_offline_grade_importer($importid, $this->assignment);
            $mform = new assignfeedback_offline_import_grades_form(null, array('assignment'=>$this->assignment,
                                                                       'csvdata'=>'',
                                                                       'ignoremodified'=>$ignoremodified,
                                                                       'gradeimporter'=>$gradeimporter,
                                                                       'draftid'=>$draftid));
            if ($mform->is_cancelled()) {
                redirect(new moodle_url('view.php',
                                        array('id'=>$this->assignment->get_course_module()->id,
                                              'action'=>'grading')));
                return;
            }

            $o .= $this->process_import_grades($draftid, $importid, $ignoremodified);
        } else {

            $o .= $renderer->render(new assign_header($this->assignment->get_instance(),
                                                            $this->assignment->get_context(),
                                                            false,
                                                            $this->assignment->get_course_module()->id,
                                                            get_string('uploadgrades', 'assignfeedback_offline')));
            $o .= $renderer->render(new assign_form('batchuploadfiles', $mform));
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

        require_capability('mod/assign:grade', $this->assignment->get_context());
        require_once($CFG->dirroot . '/mod/assign/gradingtable.php');

        $groupmode = groups_get_activity_groupmode($this->assignment->get_course_module());
        // All users.
        $groupid = 0;
        $groupname = '';
        if ($groupmode) {
            $groupid = groups_get_activity_group($this->assignment->get_course_module(), true);
            $groupname = groups_get_group_name($groupid) . '-';
        }
        $filename = clean_filename(get_string('offlinegradingworksheet', 'assignfeedback_offline') . '-' .
                                   $this->assignment->get_course()->shortname . '-' .
                                   $this->assignment->get_instance()->name . '-' .
                                   $groupname .
                                   $this->assignment->get_course_module()->id);

        $table = new assign_grading_table($this->assignment, 0, '', 0, false, $filename);

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
        return array('uploadgrades'=>get_string('uploadgrades', 'assignfeedback_offline'),
                    'downloadgrades'=>get_string('downloadgrades', 'assignfeedback_offline'));
    }

    /**
     * Override the default is_enabled to disable this plugin if advanced grading is active
     *
     * @return bool
     */
    public function is_enabled() {
        $gradingmanager = get_grading_manager($this->assignment->get_context(), 'mod_assign', 'submissions');
        $controller = $gradingmanager->get_active_controller();
        $active = !empty($controller);

        if ($active) {
            return false;
        }
        return parent::is_enabled();
    }

    /**
     * Do not show this plugin in the grading table or on the front page
     *
     * @return bool
     */
    public function has_user_summary() {
        return false;
    }

}
