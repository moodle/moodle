<?php  //$Id$

require_once $CFG->libdir.'/formslib.php';

class edit_grade_form extends moodleform {
    function definition() {

        global $CFG, $USER;
        $mform =& $this->_form;

        $gradeitem = $this->_customdata['gradeitem'];

         /// actual grade - numeric or scale
        if ($gradeitem->gradetype == 1) {
            // numeric grade
            $mform->addElement('text', 'finalgrade', get_string('finalgrade', 'grades'));
        } else if ($gradeitem->gradetype == 2) {
            // scale grade
            $scaleopt[-1] = get_string('nograde');

            $i = 1;
            if ($scale = get_record('scale', 'id', $gradeitem->scaleid)) {
                foreach (split(",", $scale->scale) as $option) {
                    $scaleopt[$i] = $option;
                    $i++;
                }
            }

            $mform->addElement('select', 'finalgrade', get_string('finalgrade', 'grades'), $scaleopt);
        }

        /// hidden
        $mform->addElement('advcheckbox', 'hidden', get_string('hidden', 'grades'));

        /// locked
        $mform->addElement('advcheckbox', 'locked', get_string('locked', 'grades'));

        /// locktime
        $mform->addElement('date_time_selector', 'locktime', get_string('locktime', 'grades'), array('optional'=>true));
        $mform->disabledIf('locktime', 'gradetype', 'eq', GRADE_TYPE_NONE);
        /// hidden/visible

        /// feedback
        $feedbackformat = get_user_preferences('grade_report_feedbackformat', $CFG->grade_report_feedbackformat);

        // visible elements
        // User preference determines the format
        if ($CFG->htmleditor && $USER->htmleditor && $feedbackformat == GRADER_REPORT_FEEDBACK_FORMAT_HTML) {
            $mform->addElement('htmleditor', 'feedback', get_string('feedback', 'grades'),
                array('rows'=> '15', 'course' => $gradeitem->courseid, 'cols'=>'45'));
        } else {
            $mform->addElement('textarea', 'feedback', get_string('feedback', 'grades'));
        }

        //TODO: add other elements

        // hidden params
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('gradeid', PARAM_INT);

        $mform->addElement('hidden', 'courseid', 0);
        $mform->setType('courseid', PARAM_INT);

//-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();
    }
}

?>