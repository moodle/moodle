<?php
require_once ('moodleform_mod.php');

class mod_journal_mod_form extends moodleform_mod {

    function definition() {

        global $COURSE;
        $mform    =& $this->_form;

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('journalname', 'journal'), array('size'=>'64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');

        $mform->addElement('htmleditor', 'intro', get_string('journalquestion', 'journal'));
        $mform->setType('intro', PARAM_RAW);
        $mform->addRule('intro', get_string('required'), 'required', null, 'client');
        $mform->setHelpButton('intro', array('writing', 'questions', 'richtext'), false, 'editorhelpbutton');

        $mform->addElement('format', 'format', get_string('format'));

        $mform->addElement('modgrade', 'assessed', get_string('grade'), false);
        $mform->setDefault('assessed', 0);

        $options = array();
        $options[0] = get_string('alwaysopen', 'journal');
        for ($i=1;$i<=13;$i++) {
            $options[$i] = get_string('numdays', '', $i);
        }
        for ($i=2;$i<=16;$i++) {
            $days = $i * 7;
            $options[$days] = get_string('numweeks', '', $i);
        }
        $options[365] = get_string('numweeks', '', 52);
        $mform->addElement('select', 'days', get_string('daysavailable', 'journal'), $options);
        if ($COURSE->format == 'weeks') {
            $mform->setDefault('days', '7');
        } else {
            $mform->setDefault('days', '0');
        }

//-------------------------------------------------------------------------------
        $this->standard_coursemodule_elements();
//-------------------------------------------------------------------------------
// buttons
        $this->add_action_buttons();

    }



}
?>
