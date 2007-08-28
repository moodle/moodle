<?php //$Id$

require_once($CFG->dirroot.'/lib/formslib.php');

class key_form extends moodleform {

    // Define the form
    function definition () {
        global $USER, $CFG, $COURSE;

        $mform =& $this->_form;

        $mform->addElement('static', 'value', get_string('keyvalue'));
        $mform->addElement('text', 'iprestriction', get_string('keyiprestriction'), array('size'=>80));
        $mform->addElement('date_time_selector', 'validuntil', get_string('keyvaliduntil'), array('optional'=>true));


        $mform->addElement('hidden','id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden','courseid');
        $mform->setType('courseid', PARAM_INT);

        $this->add_action_buttons();
    }
}

?>
