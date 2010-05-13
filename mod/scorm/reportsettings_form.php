<?php
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once "$CFG->libdir/formslib.php";
class mod_scorm_report_settings extends moodleform {
    
    function definition() {
        global $COURSE;
        $mform    =& $this->_form;
        //-------------------------------------------------------------------------------
        $mform->addElement('header', 'preferencespage', get_string('preferencespage', 'scorm'));

        $options = array();
        if ($this->_customdata['currentgroup'] || $COURSE->id != SITEID) {
            $options[SCORM_REPORT_ATTEMPTS_ALL_STUDENTS] = get_string('optallstudents','scorm');
            $options[SCORM_REPORT_ATTEMPTS_STUDENTS_WITH] = get_string('optattemptsonly','scorm');
            $options[SCORM_REPORT_ATTEMPTS_STUDENTS_WITH_NO] = get_string('optnoattemptsonly', 'scorm');
        }
        $mform->addElement('select', 'attemptsmode', get_string('show', 'scorm'), $options);

        //-------------------------------------------------------------------------------
        $mform->addElement('header', 'preferencesuser', get_string('preferencesuser', 'scorm'));

        $mform->addElement('text', 'pagesize', get_string('pagesize', 'scorm'));
        $mform->setType('pagesize', PARAM_INT);

        $mform->addElement('selectyesno', 'detailedrep', get_string('details', 'scorm'));

        $this->add_action_buttons(false, get_string('savepreferences'));
    }
    
}
