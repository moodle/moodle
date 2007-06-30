<?php //$Id$

require_once $CFG->libdir.'/formslib.php';

class edit_category_form extends moodleform {
    function definition() {
        $mform =& $this->_form;

        // visible elements
        $mform->addElement('text', 'fullname', get_string('categoryname', 'grades'));

        //TODO: add other elements

        // hidden params
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'courseid', 0);
        $mform->setType('courseid', PARAM_INT);

//-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();
    }
}
?>