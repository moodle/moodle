<?php

require_once($CFG->libdir.'/formslib.php');

class book_chapter_edit_form extends moodleform {

    function definition() {
        global $CFG;
        $mform =& $this->_form;
        $cm    = $this->_customdata;

        $mform->addElement('header', 'general', get_string('edit'));

        $mform->addElement('text', 'title', get_string('chaptertitle', 'book'), array('size'=>'30'));
        $mform->setType('title', PARAM_RAW);
        $mform->addRule('title', null, 'required', null, 'client');

        $mform->addElement('advcheckbox', 'subchapter', get_string('subchapter', 'book'));

        $mform->addElement('htmleditor', 'content', get_string('content', 'book'), array('cols'=>50, 'rows'=>30));
        $mform->setType('content', PARAM_RAW);
        $mform->setHelpButton('content', array('reading', 'writing', 'questions', 'richtext'), false, 'editorhelpbutton');

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'cmid');
        $mform->setType('cmid', PARAM_INT);

        $mform->addElement('hidden', 'pagenum');
        $mform->setType('pagenum', PARAM_INT);

        $context = get_context_instance(CONTEXT_MODULE, $cm->id);
        if (has_capability('mod/book:import', $context)) {
            $mform->addElement('static', 'doimport', get_string('importingchapters', 'book').':', '<a href="import.php?id='.$cm->id.'">'.get_string('doimport', 'book').'</a>');
        }

        $this->add_action_buttons(true);
    }

    function definition_after_data() {
        global $CFG;
        $mform =& $this->_form;

        if ($mform->getElementValue('id')) {
            if ($mform->elementExists('doimport')) {
                $mform->removeElement('doimport');
            }
        }
    }
}
