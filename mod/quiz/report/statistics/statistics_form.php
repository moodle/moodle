<?php
require_once "$CFG->libdir/formslib.php";
class mod_quiz_report_statistics extends moodleform {

    function definition() {
        global $COURSE;
        $mform    =& $this->_form;
//-------------------------------------------------------------------------------
        $mform->addElement('header', 'preferencespage', get_string('preferencespage', 'quiz_overview'));

        $options = array();
        $options[0] = get_string('attemptsfirst','quiz_statistics');
        $options[1] = get_string('attemptsall','quiz_statistics');
        $mform->addElement('select', 'useallattempts', get_string('calculatefrom', 'quiz_statistics'), $options);
        $mform->setDefault('useallattempts', 0);
//-------------------------------------------------------------------------------
        $mform->addElement('submit', 'submitbutton', get_string('preferencessave', 'quiz_overview'));
    }
}

