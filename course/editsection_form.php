<?php

require_once($CFG->libdir.'/formslib.php');

class editsection_form extends moodleform {

    function definition() {
        global $CFG, $DB;

        $mform  = $this->_form;
        $course = $this->_customdata;

        $mform->addElement('editor', 'summary', get_string('summary'), null, array('changeformat'=>false, 'maxfiles'=>-1));

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

//--------------------------------------------------------------------------------
        $this->add_action_buttons();

    }
}
