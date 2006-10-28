<?php //$Id$

// TODO - THE HELP BUTTON FOR FORMATTING TO BE Included and formatting drop down box.
require_once $CFG->libdir.'/formslib.php';

class glossary_comment_form extends moodleform {
    function definition() {
        $mform =& $this->_form;

        // visible elements
        $mform->addElement('htmleditor', 'entrycomment',get_string('comment', 'glossary'));
        $mform->addRule('entrycomment', get_string('required'), 'required', null, 'client');
        $mform->setType('entrycomment', PARAM_RAW); // processed by trusttext or cleaned before the display

        $mform->addElement('format', 'format', get_string('format'));

        // hidden optional params
        $mform->addElement('hidden', 'cid', 0);
        $mform->setType('cid', PARAM_INT);

        $mform->addElement('hidden', 'eid', 0);
        $mform->setType('eid', PARAM_INT);

        $mform->addElement('hidden', 'action', '');
        $mform->setType('action', PARAM_ACTION);

        // buttons
        $buttonarray[] = &MoodleQuickForm::createElement('submit', 'submit', get_string('savechanges'));
        $buttonarray[] = &MoodleQuickForm::createElement('reset', 'reset', get_string('revert'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
    }
}
?>