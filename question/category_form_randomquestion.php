<?php  //$Id$

require_once($CFG->libdir.'/formslib.php');

class question_category_edit_form_randomquestion extends moodleform {

    function definition() {
        global $CFG, $DB;
        $mform    =& $this->_form;

        $contexts   = $this->_customdata['contexts'];
        $currentcat   = $this->_customdata['currentcat'];
//--------------------------------------------------------------------------------
        $mform->addElement('header', 'categoryheader', get_string('createcategoryfornewrandomquestion', 'quiz'));

        $questioncategoryel = $mform->addElement('questioncategory', 'parent', get_string('parentcategory', 'quiz'),
                    array('contexts'=>$contexts, 'top'=>true, 'currentcat'=>$currentcat, 'nochildrenof'=>$currentcat));
        $mform->setType('parent', PARAM_SEQUENCE);
        if (1 == $DB->count_records_sql("SELECT count(*)
                                           FROM {question_categories} c1,
                                                {question_categories} c2
                                          WHERE c2.id = ?
                                            AND c1.contextid = c2.contextid", array($currentcat))){
            //TODO: Tim? why does the above evaluate true, breaking the form?
            // and more importantly, if this is a valid situation, how should we react,
            // that is, what does this mean?
            //$mform->hardFreeze('parent');
        }
        $mform->setHelpButton('parent', array('categoryparent', get_string('parent', 'quiz'), 'question'));

        $mform->addElement('text','name', get_string('name'),'maxlength="254" size="50"');
        $mform->setDefault('name', '');
        $mform->addRule('name', get_string('categorynamecantbeblank', 'quiz'), 'required', null, 'client');
        $mform->setType('name', PARAM_MULTILANG);

        $mform->addElement('hidden', 'info', '');
        $mform->setType('info', PARAM_MULTILANG);

//--------------------------------------------------------------------------------
        $this->add_action_buttons(false, get_string('addrandomquestion', 'quiz'));
//--------------------------------------------------------------------------------
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'addonpage', 0, 'id="rform_qpage"');
        $mform->setType('addonpage', PARAM_SEQUENCE);
    }
}
?>
