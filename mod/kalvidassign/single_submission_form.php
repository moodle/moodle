<?php
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
 * Kaltura video assignment single submission form.
 *
 * @package    mod_kalvidassign
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}

require_once(dirname(dirname(dirname(__FILE__))).'/course/moodleform_mod.php');

class kalvidassign_singlesubmission_form extends moodleform {

    /**
     * This function defines the forums elments that are to be displayed
     */
    public function definition() {
        global $CFG, $PAGE;

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
        $mform->addElement('header', 'single_submission_1', get_string('submission', 'kalvidassign'));

        $mform->addelement('static', 'submittinguser', $this->_customdata->submissionuserpic, $this->_customdata->submissionuserinfo);

        /* Video preview */
        $mform->addElement('header', 'single_submission_2', get_string('previewvideo', 'kalvidassign'));

        $submission     = $this->_customdata->submission;
        $gradinginfo   = $this->_customdata->grading_info;
        $entryobject    = '';
        $timemodified   = '';

        if (!empty($submission->entry_id) && !empty($submission->source)) {
            $attr = array(
                'src' => $this->_generateLtiLaunchLink($submission->source, $submission),
                'height' => $submission->height,
                'width' => $submission->width,
                'allowfullscreen' => "true",
                'webkitallowfullscreen' => "true",
                'mozallowfullscreen' => "true"
            );
            $mform->addElement('html', html_writer::tag('iframe', '', $attr));
        }

        /* Grades section */
        $mform->addElement('header', 'single_submission_3', get_string('grades', 'kalvidassign'));

        $attributes = array();

        if ($this->_customdata->gradingdisabled || $this->_customdata->gradingdisabled) {
            $attributes['disabled'] = 'disabled';
        }

        $grademenu = make_grades_menu($this->_customdata->cminstance->grade);
        $grademenu['-1'] = get_string('nograde');

        $mform->addElement('select', 'xgrade', get_string('grade').':', $grademenu, $attributes);

        if (isset($submission->grade)) {
            $mform->setDefault('xgrade', $this->_customdata->submission->grade );
        } else {
            $mform->setDefault('xgrade', '-1' );
        }

        $mform->setType('xgrade', PARAM_INT);

        if (!empty($this->_customdata->enableoutcomes) && !empty($gradinginfo)) {

            foreach ($gradinginfo->outcomes as $n => $outcome) {

                $options = make_grades_menu(-$outcome->scaleid);

                if (array_key_exists($this->_customdata->userid, $outcome->grades) &&
                    $outcome->grades[$this->_customdata->userid]->locked) {

                    $options[0] = get_string('nooutcome', 'grades');
                    echo $options[$outcome->grades[$this->_customdata->userid]->grade];

                } else {

                    $options[''] = get_string('nooutcome', 'grades');
                    $attributes = array('id' => 'menuoutcome_'.$n );
                    $mform->addElement('select', 'outcome_'.$n.'['.$this->_customdata->userid.']', $outcome->name.':', $options, $attributes );
                    $mform->setType('outcome_'.$n.'['.$this->_customdata->userid.']', PARAM_INT);

                    if (array_key_exists($this->_customdata->userid, $outcome->grades)) {
                        $mform->setDefault('outcome_'.$n.'['.$this->_customdata->userid.']', $outcome->grades[$this->_customdata->userid]->grade );
                    }
                }
            }
        }

        if (has_capability('gradereport/grader:view', $this->_customdata->context) && has_capability('moodle/grade:viewall', $this->_customdata->context)) {

            if (empty($gradinginfo) || !array_key_exists($this->_customdata->userid, $gradinginfo->items[0]->grades)) {

                $grade = ' - ';

            } else if (0 != strcmp('-', $gradinginfo->items[0]->grades[$this->_customdata->userid]->str_grade)) {

                $grade = '<a href="'.$CFG->wwwroot.'/grade/report/grader/index.php?id='.$this->_customdata->cm->course.'" >';
                $grade .= $this->_customdata->grading_info->items[0]->grades[$this->_customdata->userid]->str_grade.'</a>';
            } else {

                $grade = $this->_customdata->grading_info->items[0]->grades[$this->_customdata->userid]->str_grade;
            }

        } else {

            $grade = $this->_customdata->grading_info->items[0]->grades[$this->_customdata->userid]->str_grade;

        }

        $mform->addElement('static', 'finalgrade', get_string('currentgrade', 'kalvidassign').':', $grade);
        $mform->setType('finalgrade', PARAM_INT);

        /* Feedback section */
        $mform->addElement('header', 'single_submission_4', get_string('feedback', 'kalvidassign'));

        if (!empty($this->_customdata->gradingdisabled)) {

            if (array_key_exists($this->_customdata->userid, $gradinginfo->items[0]->grades)) {
                $mform->addElement('static', 'disabledfeedback', '&nbsp;', $gradinginfo->items[0]->grades[$this->_customdata->userid]->str_feedback );
            } else {
                $mform->addElement('static', 'disabledfeedback', '&nbsp;', '' );
            }

        } else {

            $mform->addElement('editor', 'submissioncomment_editor', get_string('feedback', 'kalvidassign').':', null, $this->get_editor_options() );
            $mform->setType('submissioncomment_editor', PARAM_RAW);

        }

        /* Marked section */
        $mform->addElement('header', 'single_submission_5', get_string('lastgrade', 'kalvidassign'));

        $mform->addElement('static', 'markingteacher', $this->_customdata->markingteacherpic, $this->_customdata->markingteacherinfo);

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
        $editoroptions = array();
        $editoroptions['component'] = 'mod_kalvidassign';
        $editoroptions['noclean'] = false;
        $editoroptions['maxfiles'] = 0;
        $editoroptions['context'] = $this->_customdata->context;

        return $editoroptions;
    }
    
    private function _generateLtiLaunchLink($source, $data)
    {
        $cmid = $this->_customdata->cm->id;
        $courseId = $this->_customdata->cm->course;
        
        $width = 485;
        $height = 450;
        if(isset($data->height) && isset($data->width))
        {
            $width = $data->width;
            $height = $data->height;
        }
        
        $target = new moodle_url('/mod/kalvidassign/lti_launch_grade.php?cmid='.$cmid.'&source='.urlencode($source).'&height='.$height.'&width='.$width.'&courseid='.$courseId);
        return $target;
    }
}
