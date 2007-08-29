<?php //$Id$

require_once($CFG->dirroot.'/lib/formslib.php');

class key_form extends moodleform {

    // Define the form
    function definition () {
        global $USER, $CFG, $COURSE;

        $mform =& $this->_form;

        $mform->addElement('static', 'value', get_string('keyvalue', 'userkey'));
        $mform->addElement('text', 'iprestriction', get_string('keyiprestriction', 'userkey'), array('size'=>80));
        $mform->addElement('date_time_selector', 'validuntil', get_string('keyvaliduntil', 'userkey'), array('optional'=>true));

        $mform->setHelpButton('iprestriction', array(false, get_string('keyiprestriction', 'userkey'),
                false, true, false, get_string("keyiprestrictionhelp", 'userkey')));
        $mform->setHelpButton('validuntil', array(false, get_string('keyvaliduntil', 'userkey'), false, true, false, get_string("keyvaliduntilhelp", 'userkey')));

        $mform->addElement('hidden','id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden','courseid');
        $mform->setType('courseid', PARAM_INT);

        $this->add_action_buttons();
    }
}

?>
