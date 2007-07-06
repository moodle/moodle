<?php  //$Id$

require_once $CFG->libdir.'/formslib.php';

class edit_feedback_form extends moodleform {
    function definition() {
        global $CFG, $USER;
        $mform =& $this->_form;

        $feedbackformat = get_user_preferences('grade_report_feedbackformat', $CFG->grade_report_feedbackformat);
        
        // visible elements
        // User preference determines the format
        if ($CFG->htmleditor && $USER->htmleditor && $feedbackformat == GRADER_REPORT_FEEDBACK_FORMAT_HTML) {
            $mform->addElement('htmleditor', 'feedback', get_string('feedback', 'grades'),
                array('rows'=> '15', 'course' => optional_param('courseid', PARAM_INT), 'cols'=>'45'));
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
