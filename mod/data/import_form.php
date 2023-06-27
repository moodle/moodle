<?php
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->libdir.'/csvlib.class.php');

class mod_data_import_form extends moodleform {

    function definition() {
        $mform =& $this->_form;

        $dataid = $this->_customdata['dataid'];

        $mform->addElement('filepicker', 'recordsfile', get_string('csvfile', 'data'));

        $delimiters = csv_import_reader::get_delimiter_list();
        $mform->addElement('select', 'fielddelimiter', get_string('fielddelimiter', 'data'), $delimiters);
        $mform->setDefault('fielddelimiter', 'comma');

        $mform->addElement('text', 'fieldenclosure', get_string('fieldenclosure', 'data'));
        $mform->setType('fieldenclosure', PARAM_CLEANHTML);

        $choices = core_text::get_encodings();
        $mform->addElement('select', 'encoding', get_string('fileencoding', 'mod_data'), $choices);
        $mform->setDefault('encoding', 'UTF-8');

        // Database activity ID.
        $mform->addElement('hidden', 'd');
        $mform->setType('d', PARAM_INT);
        $mform->setDefault('d', $dataid);

        $this->add_action_buttons(false, get_string('submit'));
    }
}
