<?php  //$Id$

require_once $CFG->libdir.'/formslib.php';

class edit_calculation_form extends moodleform {
    var $available;
    var $noidnumbers;
    var $showing;

    function definition() {
        global $COURSE;

        $mform =& $this->_form;

        $this->available = grade_item::fetch_all(array('courseid'=>$COURSE->id));
        $this->noidnumbers = array();
        foreach ($this->available as $item) {
            if (empty($item->idnumber)) {
                $this->noidnumbers[$item->id] = $item;
                unset($this->available[$item->id]);
            }
        }

/// visible elements
        $mform->addElement('header', 'general', get_string('gradeitem', 'grades'));

        $mform->addElement('static', 'itemname', get_string('itemname', 'grades'));
        $mform->addElement('textarea', 'calculation', get_string('calculation', 'grades'), 'cols="60" rows="5"');

/// idnumbers
        $mform->addElement('header', 'availableheader', get_string('availableidnumbers', 'grades'));
        foreach ($this->available as $item) {
            $mform->addElement('static', 'idnumber_'.$item->id, $item->get_name());
            $mform->setDefault('idnumber_'.$item->id, '[['.$item->idnumber.']]');
        }


/// set idnumbers
        if ($this->noidnumbers) {
            $mform->addElement('header', 'addidnumbersheader', get_string('addidnumbers', 'grades'));
            foreach ($this->noidnumbers as $item) {
                $mform->addElement('text', 'idnumber_'.$item->id, $item->get_name(), 'size="30"');
            }
        }


/// hidden params
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'courseid', 0);
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('hidden', 'showingadding', 0);


/// add return tracking info
        $gpr = $this->_customdata['gpr'];
        $gpr->add_mform_elements($mform);

//-------------------------------------------------------------------------------
        // buttons
        if ($this->noidnumbers) {
            $mform->addElement('submit', 'addidnumbers', get_string('addidnumbers', 'grades'));
            $mform->registerNoSubmitButton('addidnumbers');
        }
        $this->add_action_buttons();
    }

    function definition_after_data() {
        global $CFG, $COURSE;

        $mform =& $this->_form;

        if ($this->noidnumbers) {
            if (optional_param('addidnumbers', 0, PARAM_RAW)) {
                $el =& $mform->getElement('showingadding');
                $el->setValue(1);
            }

            $this->showing = $mform->getElementValue('showingadding');
            if (!$this->showing) {
                foreach ($this->noidnumbers as $item) {
                    $mform->removeElement('idnumber_'.$item->id);
                }
                $mform->removeElement('addidnumbersheader');
            } else {
                $mform->removeElement('addidnumbers');
            }
        }
    }

/// perform extra validation before submission
    function validation($data){
        $errors = array();

        $mform =& $this->_form;

        //first validate and store the new idnumbers
        if ($this->noidnumbers and $this->showing) {
            foreach ($this->noidnumbers as $grade_item) {
                $idnumber = 'idnumber_'.$grade_item->id;
                if (!empty($data[$idnumber])) {
                    if ($grade_item->itemtype == 'mod') {
                        $cm = get_coursemodule_from_instance($grade_item->itemmodule, $grade_item->iteminstance, $grade_item->courseid);
                    } else {
                        $cm = null;
                    }

                    if (!grade_verify_idnumber($data[$idnumber], $grade_item, $cm)) {
                        $errors[$idnumber] = get_string('idnumbertaken');
                        continue;
                    }

                    if (empty($grade_item->idnumber) and !$grade_item->add_idnumber(stripslashes($data['idnumber_'.$grade_item->id]))) {
                        $errors[$idnumber] = get_string('error');
                        continue;
                    }
                }

                // lock the adding field if idnumber already present or just changed
                if (!empty($grade_item->idnumber)) {
                    $el =& $mform->getElement($idnumber);
                    $el->setValue($grade_item->idnumber);
                    $mform->hardFreeze($idnumber);
                }
            }
        }

        // check the calculation formula
        if ($data['calculation'] != '') {
            $grade_item = grade_item::fetch(array('id'=>$data['id'], 'courseid'=>$data['courseid']));
            $result = $grade_item->validate_formula(stripslashes($data['calculation']));
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
