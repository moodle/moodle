<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once $CFG->libdir.'/formslib.php';

class database_export_form extends moodleform {

    function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'database', get_string('dbexport', 'dbtransfer'));
        $mform->addElement('textarea', 'description', get_string('description'), array('rows'=>5, 'cols'=>60));

        $this->add_action_buttons(false, get_string('exportdata', 'dbtransfer'));
    }
}
