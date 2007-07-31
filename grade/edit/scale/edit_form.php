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

        $mform->addElement('advcheckbox', 'standard', get_string('scalestandard'));

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

        $courseid = $mform->getElementValue('courseid');

        if ($id = $mform->getElementValue('id')) {
            $scale = grade_scale::fetch(array('id'=>$id));
            $count = $scale->get_item_uses_count();

            if ($count) {
                $mform->hardFreeze('scale');
            }

            if (empty($courseid)) {
                $mform->hardFreeze('standard');

            } else if (empty($scale->courseid) and !has_capability('moodle/course:managescales', get_context_instance(CONTEXT_SYSTEM))) {
                $mform->hardFreeze('standard');

            } else if ($count and !empty($scale->courseid)) {
                $mform->hardFreeze('standard');
            }

            $activities_el =& $mform->getElement('activities');
            $activities_el->setValue(get_string('usedinnplaces', '', $count));

        } else {
            $mform->removeElement('activities');
            if (empty($courseid) or !has_capability('moodle/course:managescales', get_context_instance(CONTEXT_SYSTEM))) {
                $mform->hardFreeze('standard');
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
