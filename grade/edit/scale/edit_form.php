<?php  //$Id$

require_once $CFG->libdir.'/formslib.php';

class edit_scale_form extends moodleform {
    function definition() {
        global $CFG;
        $mform =& $this->_form;

        // visible elements
        $mform->addElement('header', 'general', get_string('scale'));

        $mform->addElement('text', 'name', get_string('name'));
        $mform->addRule('name', get_string('required'), 'required', null, 'client');
        $mform->setType('name', PARAM_TEXT);

        $mform->addElement('advcheckbox', 'custom', get_string('coursescale', 'grades'));

        $mform->addElement('static', 'activities', get_string('activities'));

        $mform->addElement('textarea', 'scale', get_string('scale'), array('cols'=>50, 'rows'=>2));
        $mform->addRule('scale', get_string('required'), 'required', null, 'client');
        $mform->setType('scale', PARAM_TEXT);

        $mform->addElement('htmleditor', 'description', get_string('description'), array('cols'=>80, 'rows'=>20));


        // hidden params
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'courseid', 0);
        $mform->setType('courseid', PARAM_INT);

/// add return tracking info
        $gpr = $this->_customdata['gpr'];
        $gpr->add_mform_elements($mform);

//-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();
    }


/// tweak the form - depending on existing data
    function definition_after_data() {
        global $CFG;

        $mform =& $this->_form;

        if ($id = $mform->getElementValue('id')) {
            $scale = grade_scale::fetch(array('id'=>$id));
            if ($count = $scale->get_uses_count()) {
                if (empty($scale->courseid)) {
                    $mform->hardFreeze('custom');
                }
                $mform->hardFreeze('scale');
            }
            $activities_el =& $mform->getElement('activities');
            $activities_el->setValue(get_string('usedinnplaces', '', $count));

        } else {
            $mform->removeElement('activities');
            if (!has_capability('moodle/course:managescales', get_context_instance(CONTEXT_SYSTEM))) {
                $mform->hardFreeze('custom');
            }
        }
    }

/// perform extra validation before submission
    function validation($data){
        $errors= array();

        $options = explode(',', $data['scale']);
        if (count($options) < 2) {
            $errors['scale'] = get_string('error');
        }

        if (0 == count($errors)){
            return true;
        } else {
            return $errors;
        }
    }


}

?>
