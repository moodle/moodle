<?php
require_once ('../../config.php');
require_once ($CFG->libdir . '/formslib.php');

class rolespermitted_form extends moodleform {

    function definition() {

        global $DB;
        $mform =& $this->_form;
        $mform->addElement('header', 'permitted', get_string('mnetpermittedroles'));
        $mform->addElement('static', 'instructions', '',get_string('mnetpermittedrolesinstructions'), ' ');
        $mform->addElement('hidden', 'hostid', 'yes');
        $roles = $DB->get_records('role', array(), 'id');
        foreach ($roles as $role) {
            $mform->addElement('checkbox', $role->shortname, $role->name,'('.$role->shortname.')');
        }


// buttons
        $this->add_action_buttons();
    }
}

