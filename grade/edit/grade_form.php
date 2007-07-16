<?php  //$Id$

require_once $CFG->libdir.'/formslib.php';

class edit_grade_form extends moodleform {
    function definition() {

        global $CFG, $USER;
        $mform =& $this->_form;

        $gradeitem = $this->_customdata['gradeitem'];

         /// actual grade - numeric or scale
        if ($gradeitem->gradetype == GRADE_TYPE_VALUE) {
            // numeric grade
            $mform->addElement('text', 'finalgrade', get_string('finalgrade', 'grades'));

        } else if ($gradeitem->gradetype == GRADE_TYPE_SCALE) {
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

        // Feedback format is automatically converted to html if user has enabled editor
        $mform->addElement('htmleditor', 'feedback', get_string('feedback', 'grades'),
            array('rows'=> '15', 'course' => $gradeitem->courseid, 'cols'=>'45'));
        $mform->setType('text', PARAM_RAW); // to be cleaned before display
        $mform->addElement('format', 'feedbackformat', get_string('format'));
        $mform->setHelpButton('feedbackformat', array('textformat', get_string('helpformatting')));

        // hidden params
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('gradeid', PARAM_INT);

        $mform->addElement('hidden', 'courseid', 0);
        $mform->setType('courseid', PARAM_INT);

/// add return tracking info
        $gpr = $this->_customdata['gpr'];
        $gpr->add_mform_elements($mform);

//-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();
    }
}

?>