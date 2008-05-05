<?php  // $Id$
require_once "$CFG->libdir/formslib.php";
class mod_quiz_report_overview_settings extends moodleform {

    function definition() {
        global $COURSE;
        $mform    =& $this->_form;
//-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('preferencespage', 'quiz_overview'));

        $options = array(0 => get_string('attemptsonly','quiz_overview', $COURSE->students));
        if ($COURSE->id != SITEID) {
            $options[1] = get_string('noattemptsonly', 'quiz_overview', $COURSE->students);
            $options[2] = get_string('allstudents','quiz_overview', $COURSE->students);
            $options[3] = get_string('allattempts','quiz_overview');
        }
        $mform->addElement('select', 'attemptsmode', get_string('show', 'quiz_overview'), $options);

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('preferencesuser', 'quiz_overview'));

        $mform->addElement('text', 'pagesize', get_string('pagesize', 'quiz'));
        $mform->setType('pagesize', PARAM_INT);

        $mform->addElement('selectyesno', 'detailedmarks', get_string('showdetailedmarks', 'quiz'));

        $this->add_action_buttons(false, get_string('preferencessave', 'quiz_overview'));
    }
}
?>
