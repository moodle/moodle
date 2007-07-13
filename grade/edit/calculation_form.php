<?php  //$Id$

require_once $CFG->libdir.'/formslib.php';

class edit_calculation_form extends moodleform {
    function definition() {
        global $COURSE;

        $mform =& $this->_form;

/// visible elements
        $mform->addElement('header', 'general', get_string('gradeitem', 'grades'));

        $mform->addElement('static', 'itemname', get_string('itemname', 'grades'));
        $mform->addElement('text', 'calculation', get_string('calculation', 'grades'));

/// hidden params
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'courseid', 0);
        $mform->setType('courseid', PARAM_INT);

//-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();
    }

/// perform extra validation before submission
    function validation($data){
        $errors= array();

        if ($data['calculation'] != '') {
            $grade_item = grade_item::fetch(array('id'=>$data['id'], 'courseid'=>$data['courseid']));
            $result = $grade_item->validate_formula($data['calculation']);
            if ($result !== true) {
                $errors['calculation'] = $result;
            }
        }
        if (0 == count($errors)){
            return true;
        } else {
            return $errors;
        }
    }

}
?>
