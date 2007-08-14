<?php  //$Id$

require_once $CFG->libdir.'/formslib.php';

class edit_calculation_form extends moodleform {
    var $available;
    var $noidnumbers;

    function definition() {
        global $COURSE;

        $mform =& $this->_form;

        $itemid = $this->_customdata['itemid'];

        $this->available = grade_item::fetch_all(array('courseid'=>$COURSE->id));
        $this->noidnumbers = array();

        // All items that have no idnumbers are added to a separate section of the form (hidden by default),
        // enabling the user to assign idnumbers to these grade_items.
        foreach ($this->available as $item) {
            if (empty($item->idnumber)) {
                $this->noidnumbers[$item->id] = $item;
                unset($this->available[$item->id]);
            }
            if ($item->id == $itemid) { // Do not include the current grade_item in the available section
                unset($this->available[$item->id]);
            }
        }

/// visible elements
        $mform->addElement('header', 'general', get_string('gradeitem', 'grades'));
        $mform->addElement('static', 'itemname', get_string('itemname', 'grades'));
        $mform->addElement('textarea', 'calculation', get_string('calculation', 'grades'), 'cols="60" rows="5"');
        $mform->setHelpButton('calculation', array('calculation', get_string('calculation', 'grades'), 'grade'));

/// hidden params
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'courseid', 0);
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('hidden', 'section', 0);
        $mform->setType('section', PARAM_ALPHA);
        $mform->setDefault('section', 'calculation');

/// add return tracking info
        $gpr = $this->_customdata['gpr'];
        $gpr->add_mform_elements($mform);

        $this->add_action_buttons();
    }

    function definition_after_data() {
        global $CFG, $COURSE;

        $mform =& $this->_form;
    }

/// perform extra validation before submission
    function validation($data){
        $errors = array();

        $mform =& $this->_form;

        // check the calculation formula
        if ($data['calculation'] != '') {
            $grade_item = grade_item::fetch(array('id'=>$data['id'], 'courseid'=>$data['courseid']));
            $calculation = calc_formula::unlocalize(stripslashes($data['calculation']));
            $result = $grade_item->validate_formula($calculation);
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
