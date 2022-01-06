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
 * library class for editpdfplus feedback plugin extending feedback plugin base class
 *
 * @package   assignfeedback_editpdfplus
 * @copyright  2016 Université de Lausanne
 * The code is based on mod/assign/feedback/editpdf/locallib.php by Davo Smith.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

use \assignfeedback_editpdfplus\document_services;
use \assignfeedback_editpdfplus\page_editor;

class assign_feedback_editpdfplus extends assign_feedback_plugin {

    const AXISGENERIC = 0;

    /** @var boolean|null $enabledcache Cached lookup of the is_available function */
    private $enabledcache = null;

    /**
     * Get the name of the file feedback plugin
     * @return string
     */
    public function get_name() {
        return get_string('pluginname', 'assignfeedback_editpdfplus');
    }

    /**
     * Create a widget for rendering the editor.
     *
     * @param int $userid
     * @param stdClass $grade
     * @param bool $readonly
     * @return assignfeedback_editpdfplus_widget
     */
    public function get_widget($userid, $grade, $readonly) {
        $attempt = -1;
        if ($grade && isset($grade->attemptnumber)) {
            $attempt = $grade->attemptnumber;
        } else {
            $grade = $this->assignment->get_user_grade($userid, true);
        }

        $feedbackfile = document_services::get_feedback_document($this->assignment->get_instance()->id, $userid, $attempt);

        // get the costum toolbars
        $toolbars = array();
        $toolbarGeneric = array();
        $coursecontext = context::instance_by_id($this->assignment->get_context()->id);
        $coursecontexts = array_filter(explode('/', $coursecontext->path), 'strlen');
        $axis = array();
        foreach ($coursecontexts as $value) {
            $axistmp = page_editor::get_axis(array($value));
            if ($axistmp && sizeof($axistmp) > 0) {
                $axis = $axistmp;
            }
        }
        $tools = page_editor::get_tools($coursecontexts);
        foreach ($axis as $ax) {
            $toolbars[$ax->id]['axeid'] = $ax->id;
            $toolbars[$ax->id]['label'] = $ax->label;
        }
        foreach ($tools as $tool) {
            if (!$tool->enabled) {
                continue;
            }
            if ($tool->axis == self::AXISGENERIC) {
                $toolbarGeneric[$tool->id] = $tool;
            } else if (isset($toolbars[$tool->axis])) {
                $toolbars[$tool->axis]['tool'][$tool->id] = $tool;
            }
        }

        $url = false;
        $filename = '';
        if ($feedbackfile) {
            $url = moodle_url::make_pluginfile_url($this->assignment->get_context()->id, 'assignfeedback_editpdfplus', document_services::FINAL_PDF_FILEAREA, $grade->id, '/', $feedbackfile->get_filename(), false);
            $filename = $feedbackfile->get_filename();
        }

        return new assignfeedback_editpdfplus_widget(array(
            'assignment' => $this->assignment->get_instance()->id,
            'userid' => $userid,
            'attemptnumber' => $attempt,
            'downloadurl' => $url,
            'downloadfilename' => $filename,
            'readonly' => $readonly,
            'customToolbars' => $toolbars,
            'genericToolbar' => $toolbarGeneric,
            'axis' => $axis
        ));
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

        $renderer = $PAGE->get_renderer('assignfeedback_editpdfplus');

        $widget = $this->get_widget($userid, $grade, false);

        $html = $renderer->render($widget);

        $enablewidget = $this->is_enabled_precheck(true);
        if ($enablewidget === true) {
            // True means 'activate and show'
            // We potentially have to re-show the annotation widget.
            $html .= '<script>(function($){$(\'button.btn.collapse-none\').click();})(jQuery)</script>';
        } else {
            // False || 'jsonly' means we want to collapse the annotation widget panel.
            $html .= "<script>
                      (function($) {
                        $(document).ready(function(){
                            setTimeout(function(){
                                $('button.btn.collapse-review-panel').click();
                            }, 1000);
                        });
                      })(jQuery)
                    </script>";
        }
        if (!$enablewidget) {
            // False means we have to deactivate altogether (i.e. hide by CSS as it's easier) the annotation widget.
            $html .= '<style>.assignfeedback_editpdfplus_widget { display: none; }</style>';
        }

        $mform->addElement('static', 'editpdfplus', get_string('editpdfplus', 'assignfeedback_editpdfplus'), $html);
        $mform->addHelpButton('editpdfplus', 'editpdfplus', 'assignfeedback_editpdfplus');
        $mform->addElement('hidden', 'editpdfplus_source_userid', $userid);
        $mform->setType('editpdfplus_source_userid', PARAM_INT);
        $mform->setConstant('editpdfplus_source_userid', $userid);
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
        if (!empty($data->editpdfplus_source_userid)) {
            $sourceuserid = $data->editpdfplus_source_userid;
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
        if (!empty($data->editpdfplus_source_userid)) {
            $sourceuserid = $data->editpdfplus_source_userid;
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
            $html = $this->assignment->render_area_files('assignfeedback_editpdfplus', document_services::FINAL_PDF_FILEAREA, $grade->id);
            //debugging('tutu');
            // Also show the link to the read-only interface.
            $renderer = $PAGE->get_renderer('assignfeedback_editpdfplus');
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

        $annotations = $DB->count_records('assignfeedback_editpp_annot', array('gradeid' => $grade->id, 'draft' => 0));
        return $annotations == 0;
    }

    /**
     * The assignment has been deleted - remove the plugin specific data
     *
     * @return bool
     */
    public function delete_instance() {
        global $DB;
        $grades = $DB->get_records('assign_grades', array('assignment' => $this->assignment->get_instance()->id), '', 'id');
        if ($grades) {
            list($gradeids, $params) = $DB->get_in_or_equal(array_keys($grades), SQL_PARAMS_NAMED);
            $DB->delete_records_select('assignfeedback_editpp_annot', 'gradeid ' . $gradeids, $params);
        }
        return true;
    }

    /**
     * Pre-check to determine whether we (Unil) thinks we should use annotation
     *
     * this function is called from $this->get_form_elements_for_user() with $isajax parameter set to true
     *
     * This function considers all user submitted files for the initial page load, to check whether the annotation
     * feature should be enabled at all.
     * On subsequent (AJAX) calls, only the currently graded user's submitted files are considered, to show or hide
     * the annotation panel.
     *
     * Then, the following algorithm is used :
     *       1. If we're not in the grading process, don't bother changing anything.
     *       2. If we're using \assign_submission_mahara, deactivate annotation.
     *       3. If we're using \assign_submission_onlinetext and no other plugins, activate annotation.
     *       4. If we're using \assign_submission_file, deactivate if any files are not annotable
     *              for the current user if $isajax is true
     *       5. If no annotable files for current/all (depending on $isajax) participants have been found,
     *              deactivate annotation.
     *       6. If the grading area is using rubrics (i.e., an evaluation grid) return 'jsonly', which yields true
     *              but (in get_form_elements_for_user()) slides the annotation pane off screen.
     *       7. Finally, return true.
     *
     * @param bool $isajax are we loading the page, on a subsequent AJAX request getting the grading elements
     *                     for a particular user
     *
     * @return bool|string activate or not | special
     */
    private function is_enabled_precheck($isajax = false) {
        global $PAGE, $DB;
        if (in_array($PAGE->url->get_param('action'), array('grade', 'grader'))) {
            // Step 1.
            // Only do this if we're on the grading interface for minimal execution flow disruption.
            // Action 'grader' is used for direct HTTP hit, 'grade' apparently used in subsequent AJAX requests.
            $plugins = $this->assignment->get_submission_plugins();
            // These are all the currently installed submission plugins.
            foreach ($plugins as $plugin) {
                if (!($plugin->is_enabled() // Is the plugin enabled in this assignment?
                        && $plugin->is_visible() // Is the plugin enabled at site level? ("visible" with eye icon)
                        )) {
                    continue;
                }
                if ($plugin instanceof \assign_submission_mahara) {
                    return false;
                    // Step 2.
                    // Never try to annotate in this case
                }
                if ($plugin instanceof \assign_submission_onlinetext) {
                    if (count($plugins) == 1) {
                        break;
                        // Step 3.
                        // Always try to annotate if no other plugins are activated
                    }
                }
                if ($plugin instanceof \assign_submission_file) {
                    // Step 4.
                    $userid = ($PAGE->url->get_param('userid')) ? $PAGE->url->get_param('userid') : (optional_param('userid', 0, PARAM_INT));
                    if ($isajax && $userid) {
                        if ($this->assignment->get_instance()->teamsubmission) {
                            // groups enabled, use group submission
                            $submission = $this->assignment->get_group_submission($userid, 0, false);
                        } else {
                            $submission = $this->assignment->get_user_submission($userid, false);
                        }
                        $user = $DB->get_record('user', array('id' => $userid));
                        if (!$submission) {
                            return false;
                        } else {
                            $files = $plugin->get_files($submission, $user);
                        }
                        // That's the user we're currently grading, loading the UI via AJAX.
                    } else {
                        // We don't know yet which user we'll be grading (initial full page load of grading interface),
                        // so we have to take all assignment participants into account.
                        $fs = get_file_storage();
                        $files = $fs->get_area_files($this->assignment->get_context()->id, 'assignsubmission_file', ASSIGNSUBMISSION_FILE_FILEAREA, false, 'timemodified', false);
                        // We've now got all submission files, let's check them out.
                    }
                    $supported_extensions = array('bmp', 'doc', 'docx', 'eps', 'fodt', 'gif', 'jpeg', 'jpg', 'odt', 'ott', 'pdf', 'png', 'rtf', 'svg', 'tiff');
                    $nbfiles = 0;
                    foreach ($files as $file) {
                        $extension = core_text::strtolower(pathinfo($file->get_filename(), PATHINFO_EXTENSION));
                        if (in_array($extension, $supported_extensions)) {
                            $nbfiles++;
                        } else {
                            if ($isajax) {
                                return false;
                                // Step 4.
                                // Don't annotate if ANY non annoatable files found for the current user.
                            }
                        }
                    }
                    if (!$nbfiles) {
                        return false;
                        // Step 5.
                        // Didn't find any files to annotate.
                    }
                }
            }
            if ($isajax) {
                global $DB;
                $cm = $this->assignment->get_course_module();
                $context = \context_module::instance($cm->id);
                $gradingarea = $DB->get_record('grading_areas', array('contextid' => $context->id));
                //                $gradingdefinition = $DB->get_record('grading_definitions', array('areaid' => $gradingarea->id));
                if ($gradingarea && $gradingarea->activemethod == 'rubric') {
                    // Step 6.
                    return 'jsonly';
                    // If we're using a grading grid (rubrics) then click the 'slide pane' button for AJAX only,
                    // so that the rubrics table can be more prominent.
                }
            }
        }
        // Step 7.
        return true;
        // No reason not to activate the annotation interface.
    }

    public function is_enabled() {
        $editpdf = null;
        $editpdfenable = false;
        $editpdfconfenable = false;
        $listPlugins = $this->assignment->get_feedback_plugins();
        foreach ($listPlugins as $plug) {
            if ($plug->get_name() == get_string('pluginname', 'assignfeedback_editpdf')) {
                $editpdf = $plug;
                $editpdfenable = $plug->is_enabled();
                $tmpconf = $plug->get_config();
                if ($tmpconf && isset($tmpconf->enabled)) {
                    $editpdfconfenable = $plug->get_config()->enabled;
                }
                break;
            }
        }
        if ($editpdf && $editpdfenable && $editpdfconfenable) {
            return false;
        }
        $editpdfplusconf = $this->get_config();
        if ($editpdfplusconf && isset($editpdfplusconf->enabled)) {
            return $editpdfplusconf->enabled;
        }
        return $this->is_available();
    }

    /**
     * Determine if ghostscript is available and working.
     *
     * @return bool
     */
    public function is_available() {
        if ($this->enabledcache === null) {
            $testpath = assignfeedback_editpdfplus\pdf::test_gs_path(false);
            $this->enabledcache = $testpath->status == assignfeedback_editpdfplus\pdf::GSPATH_OK;
        }
        return $this->enabledcache;
    }

    /**
     * Automatically hide the setting for the editpdfplus feedback plugin.
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
