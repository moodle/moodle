<?php //$Id$

require_once $CFG->libdir.'/formslib.php';

class glossary_comment_form extends moodleform {
    function definition() {
        $mform =& $this->_form;

        // visible elements
        $mform->addElement('htmleditor', 'entrycomment',get_string('comment', 'glossary'));
        $mform->addRule('entrycomment', get_string('required'), 'required', null, 'client');
        $mform->setType('entrycomment', PARAM_RAW); // processed by trusttext or cleaned before the display

        $mform->addElement('format', 'format', get_string('format'));
        $mform->setHelpButton('format', array('textformat', get_string('helpformatting')));

        // hidden optional params
        $mform->addElement('hidden', 'cid', 0);
        $mform->setType('cid', PARAM_INT);

        $mform->addElement('hidden', 'eid', 0);
        $mform->setType('eid', PARAM_INT);

        $mform->addElement('hidden', 'action', '');
        $mform->setType('action', PARAM_ACTION);

        // buttons
        $buttonarray[] = &MoodleQuickForm::createElement('submit', 'submitbutton', get_string('savechanges'));
        $buttonarray[] = &MoodleQuickForm::createElement('reset', 'reset', get_string('revert'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
    }
}
?>