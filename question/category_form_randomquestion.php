<?php

require_once($CFG->libdir.'/formslib.php');

class question_category_edit_form_randomquestion extends moodleform {

    function definition() {
        global $CFG, $DB;
        $mform =& $this->_form;

        $contexts = $this->_customdata['contexts'];
        $currentcat = $this->_customdata['currentcat'];
//--------------------------------------------------------------------------------
        $mform->addElement('header', 'categoryheader', get_string('createcategoryfornewrandomquestion', 'quiz'));

        $questioncategoryel = $mform->addElement('questioncategory', 'parent', get_string('parentcategory', 'question'),
                array('contexts'=>$contexts, 'top'=>true, 'currentcat'=>$currentcat, 'nochildrenof'=>$currentcat));
        $mform->setType('parent', PARAM_SEQUENCE);
        $mform->addHelpButton('parent', 'parentcategory', 'question');

        $mform->addElement('text','name', get_string('name'),'maxlength="254" size="50"');
        $mform->setDefault('name', '');
        $mform->addRule('name', get_string('categorynamecantbeblank', 'quiz'), 'required', null, 'client');
        $mform->setType('name', PARAM_MULTILANG);

        $mform->addElement('hidden', 'info', '');
        $mform->setType('info', PARAM_MULTILANG);

//--------------------------------------------------------------------------------
        $this->add_action_buttons(true, get_string('addrandomquestion', 'quiz'));
//--------------------------------------------------------------------------------
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'addonpage', 0, 'id="rform_qpage"');
        $mform->setType('addonpage', PARAM_SEQUENCE);
    }
}

