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
 * single submission form for the Panopto Student Submission module.
 *
 * @package mod_panoptosubmission
 * @copyright  Panopto 2021
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(dirname(dirname(__FILE__))) . '/course/moodleform_mod.php');
require_once(dirname(__FILE__).'/locallib.php');

/**
 * This form is used to view and grade a single submission
 *
 * @package mod_panoptosubmission
 * @copyright  Panopto 2021
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class panoptosubmission_singlesubmission_form extends moodleform {

    /**
     * This function defines the forums elments that are to be displayed
     */
    public function definition() {
        global $CFG, $PAGE;

        $renderer = $PAGE->get_renderer('mod_panoptosubmission');

        $mform =& $this->_form;

        $cm = $this->_customdata->cm;
        $userid = $this->_customdata->userid;

        $mform->addElement('hidden', 'cmid', $cm->id);
        $mform->setType('cmid', PARAM_INT);
        $mform->addelement('hidden', 'userid', $userid);
        $mform->setType('userid', PARAM_INT);
        $mform->addElement('hidden', 'tifirst', $this->_customdata->tifirst);
        $mform->setType('tifirst', PARAM_TEXT);
        $mform->addElement('hidden', 'tilast', $this->_customdata->tilast);
        $mform->setType('tilast', PARAM_TEXT);
        $mform->addElement('hidden', 'page', $this->_customdata->page);
        $mform->setType('page', PARAM_INT);

        /* Submission section */
        $mform->addElement('header', 'single_submission_1', get_string('submission', 'panoptosubmission'));

        $mform->addelement('static', 'submittinguser',
            $this->_customdata->submissionuserpic, $this->_customdata->submissionuserinfo);

        $submission = $this->_customdata->submission;
        $gradinginfo = $this->_customdata->grading_info;

        if (!empty($submission->source)) {
            $mform->addElement('html', $renderer->get_video_container($submission, $cm->course, $cm->id));
        }

        $gradingdisabled = $this->_customdata->gradingdisabled;
        if ($gradingdisabled) {
            $attributes['disabled'] = 'disabled';
        }

        $grademenu = make_grades_menu($this->_customdata->cminstance->grade);
        $grademenu['-1'] = get_string('nograde');

        $currentgrade = isset($this->_customdata->submission->grade) &&
            $this->_customdata->submission->grade > 0 ? $this->_customdata->submission->grade : null;

        $gradinginstance = panoptosubmission_get_grading_instance(
            $this->_customdata->cminstance,
            $this->_customdata->context,
            $submission,
            $gradingdisabled
        );

        $mform->addElement('header', 'gradeheader', get_string('gradeverb', 'panoptosubmission'));
        if ($gradinginstance) {
            $gradingelement = $mform->addElement('grading',
                                                 'advancedgrading',
                                                 get_string('gradeverb', 'panoptosubmission').':',
                                                 ['gradinginstance' => $gradinginstance]);
            if ($gradingdisabled) {
                $gradingelement->freeze();
            } else {
                $mform->addElement('hidden', 'advancedgradinginstanceid', $gradinginstance->get_id());
                $mform->setType('advancedgradinginstanceid', PARAM_INT);
            }
        } else {
            if ($this->_customdata->cminstance->grade > 0) {
                $gradeinputattributes = [
                    'id' => 'panoptogradeinputbox',
                    'class' => 'mod-panoptosubmission-grade-input-box',
                    'type' => 'number',
                    'step' => 'any',
                    'min' => 0,
                    'max' => $this->_customdata->cminstance->grade,
                ];

                $mform->addElement('text', 'xgrade', get_string('grade_out_of', 'panoptosubmission',
                    $this->_customdata->cminstance->grade), $grademenu, $gradeinputattributes);
                $mform->setDefault('xgrade', $currentgrade);
            } else {
                $attributes = [];
                $mform->addElement('select', 'xgrade', get_string('gradenoun', 'panoptosubmission') . ':', $grademenu, $attributes);

                if (isset($submission->grade)) {
                    $mform->setDefault('xgrade', $this->_customdata->submission->grade);
                } else {
                    $mform->setDefault('xgrade', '-1');
                }
            }
        }

        $mform->setType('xgrade', PARAM_NUMBER);

        if (!empty($this->_customdata->enableoutcomes) && !empty($gradinginfo)) {

            foreach ($gradinginfo->outcomes as $n => $outcome) {

                $options = make_grades_menu(-$outcome->scaleid);

                if (array_key_exists($this->_customdata->userid, $outcome->grades) &&
                    $outcome->grades[$this->_customdata->userid]->locked) {

                    $options[0] = get_string('nooutcome', 'grades');
                    echo $options[$outcome->grades[$this->_customdata->userid]->grade];

                } else {

                    $options[''] = get_string('nooutcome', 'grades');
                    $attributes = ['id' => 'menuoutcome_' . $n ];
                    $mform->addElement('select', 'outcome_' . $n . '[' . $this->_customdata->userid . ']',
                        $outcome->name . ':', $options, $attributes );
                    $mform->setType('outcome_' . $n . '[' . $this->_customdata->userid . ']', PARAM_INT);

                    if (array_key_exists($this->_customdata->userid, $outcome->grades)) {
                        $mform->setDefault('outcome_' . $n . '[' . $this->_customdata->userid . ']',
                            $outcome->grades[$this->_customdata->userid]->grade );
                    }
                }
            }
        }

        if (has_capability('gradereport/grader:view', $this->_customdata->context) &&
            has_capability('moodle/grade:viewall', $this->_customdata->context)) {

            if (empty($gradinginfo) || !array_key_exists($this->_customdata->userid, $gradinginfo->items[0]->grades)) {

                $grade = ' - ';

            } else if (0 != strcmp('-', $gradinginfo->items[0]->grades[$this->_customdata->userid]->str_grade)) {

                $grade = '<a href="' . $CFG->wwwroot . '/grade/report/grader/index.php?id=' .
                    $this->_customdata->cm->course . '" >';
                $grade .= $this->_customdata->grading_info->items[0]->grades[$this->_customdata->userid]->str_grade . '</a>';
            } else {

                $grade = $this->_customdata->grading_info->items[0]->grades[$this->_customdata->userid]->str_grade;
            }

        } else {

            $grade = $this->_customdata->grading_info->items[0]->grades[$this->_customdata->userid]->str_grade;

        }

        $mform->addElement('static', 'finalgrade', get_string('currentgrade', 'panoptosubmission') . ':', $grade);
        $mform->setType('finalgrade', PARAM_NUMBER);
        $mform->addElement('static', 'markingteacher',
            $this->_customdata->markingteacherpic, $this->_customdata->markingteacherinfo);

        if (!empty($this->_customdata->gradingdisabled)) {

            if (array_key_exists($this->_customdata->userid, $gradinginfo->items[0]->grades)) {
                $mform->addElement('static', 'disabledfeedback', '&nbsp;',
                    $gradinginfo->items[0]->grades[$this->_customdata->userid]->str_feedback );
            } else {
                $mform->addElement('static', 'disabledfeedback', '&nbsp;', '' );
            }

        } else {

            $mform->addElement('editor', 'submissioncomment_editor',
                get_string('feedback', 'panoptosubmission') . ':', null, $this->get_editor_options());
            $mform->setType('submissioncomment_editor', PARAM_RAW);

            // Prepare draft area for existing feedback.
            $draftitemid = file_get_submitted_draft_itemid('submissioncomment_editor');
            $currenttext = file_prepare_draft_area($draftitemid,
                $this->_customdata->context->id,
                STUDENTSUBMISSION_FILE_COMPONENT,
                STUDENTSUBMISSION_FILE_FILEAREA,
                $submission->id,
                ['subdirs' => true],
                $submission->submissioncomment ?? ""
            );

            // Replace @@PLUGINFILE@@ with actual URLs for display in editor if necessary.
            if (!empty($submission->submissioncomment)) {
                $feedbackcontent = file_rewrite_pluginfile_urls(
                    $currenttext,
                    'pluginfile.php',
                    $this->_customdata->context->id,
                    STUDENTSUBMISSION_FILE_COMPONENT,
                    STUDENTSUBMISSION_FILE_FILEAREA,
                    $submission->id
                );
            } else {
                $feedbackcontent = $currenttext;
            }

            $feedbackeditor = [
                'text' => $feedbackcontent,
                'format' => $submission->format ?? FORMAT_HTML,
                'itemid' => $draftitemid,
            ];
            $mform->setDefault('submissioncomment_editor', $feedbackeditor);
        }

        // Notify student checkbox.
        $mform->addElement('selectyesno', 'sendstudentnotifications', get_string('sendstudentnotifications', 'panoptosubmission'));
        $mform->setDefault('sendstudentnotifications', $this->_customdata->cminstance->sendstudentnotifications ?? 0);
        $mform->setType('sendstudentnotifications', PARAM_BOOL);

        $this->add_action_buttons();
    }

    /**
     * This function sets the text editor format.
     * @param object|array $data object or array of default values
     * @return void
     */
    public function set_data($data) {

        if (!isset($data->submission->format)) {
            $data->textformat = FORMAT_HTML;
        } else {
            $data->textformat = $data->submission->format;
        }

        $editoroptions = $this->get_editor_options();

        return parent::set_data($data);

    }

    /**
     * This function gets the editor options.
     * @return array An array of editor options.
     */
    protected function get_editor_options() {
        return [
            'component' => 'mod_panoptosubmission',
            'noclean' => false,
            'accepted_types' => '*',
            'maxfiles' => '-1',
            'context' => $this->_customdata->context
        ];
    }
}
