<?php

require_once($CFG->libdir.'/formslib.php');

class quiz_add_random_form extends moodleform {

    function definition() {
        global $CFG, $DB;
        $mform =& $this->_form;

        $contexts = $this->_customdata;

//--------------------------------------------------------------------------------
        $mform->addElement('header', 'categoryheader', get_string('randomfromexistingcategory', 'quiz'));

        $mform->addElement('questioncategory', 'category', get_string('category'),
                array('contexts' => $contexts->all(), 'top' => false));

        $mform->addElement('checkbox', 'includesubcategories', '', get_string('recurse', 'quiz'));

        $mform->addElement('submit', 'existingcategory', get_string('addrandomquestion', 'quiz'));

//--------------------------------------------------------------------------------
        $mform->addElement('header', 'categoryheader', get_string('randomquestionusinganewcategory', 'quiz'));

        $mform->addElement('text', 'name', get_string('name'), 'maxlength="254" size="50"');
        $mform->setType('name', PARAM_MULTILANG);

        $mform->addElement('questioncategory', 'parent', get_string('parentcategory', 'question'),
                array('contexts' => $contexts->all(), 'top' => true));
        $mform->addHelpButton('parent', 'parentcategory', 'question');

        $mform->addElement('submit', 'newcategory', get_string('createcategoryandaddrandomquestion', 'quiz'));

//--------------------------------------------------------------------------------
        $mform->addElement('cancel');
        $mform->closeHeaderBefore('cancel');

        $mform->addElement('hidden', 'addonpage', 0, 'id="rform_qpage"');
        $mform->setType('addonpage', PARAM_SEQUENCE);
        $mform->addElement('hidden', 'cmid', 0);
        $mform->setType('cmid', PARAM_INT);
        $mform->addElement('hidden', 'returnurl', 0);
        $mform->setType('returnurl', PARAM_LOCALURL);
    }

    function validation($fromform, $files) {
        $errors = parent::validation($fromform, $files);

        if (!empty($fromform['newcategory']) && trim($fromform['name']) == '') {
            $errors['name'] = get_string('categorynamecantbeblank', 'quiz');
        }

        return $errors;
    }
}

