<?php //$Id$

require_once $CFG->libdir.'/formslib.php';

class edit_item_form extends moodleform {
    function definition() {
        $mform =& $this->_form;

        // visible elements
        $mform->addElement('text', 'itemname', get_string('itemname', 'grades'));

        //TODO: add other elements

        // hidden params
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'courseid', 0);
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('hidden', 'itemtype', 0);
        $mform->setType('itemtype', PARAM_ALPHA);

//-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();
    }
}
?>