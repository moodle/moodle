<?php  //$Id$

require_once($CFG->libdir.'/formslib.php');

class question_category_edit_form extends moodleform {

    function definition() {
        global $CFG, $DB;
        $mform    =& $this->_form;

        $contexts   = $this->_customdata['contexts'];
        $currentcat   = $this->_customdata['currentcat'];
//--------------------------------------------------------------------------------
        $mform->addElement('header', 'categoryheader', get_string('addcategory', 'quiz'));

        $questioncategoryel = $mform->addElement('questioncategory', 'parent', get_string('parent', 'quiz'),
                    array('contexts'=>$contexts, 'top'=>true, 'currentcat'=>$currentcat, 'nochildrenof'=>$currentcat));
        $mform->setType('parent', PARAM_SEQUENCE);
        // This next test is actually looking to see if $currentcat is the id of
        // a category that already exists, and is the only top-level category in
        // it context. If so, we stop it from being moved.
        if (1 == $DB->count_records_sql("SELECT count(*)
                                           FROM {question_categories} c1,
                                                {question_categories} c2
                                          WHERE c2.id = ?
                                            AND c1.contextid = c2.contextid
                                            AND c1.parent = 0 AND c2.parent = 0", array($currentcat))){
            $mform->hardFreeze('parent');
        }
        $mform->setHelpButton('parent', array('categoryparent', get_string('parent', 'quiz'), 'question'));

        $mform->addElement('text','name', get_string('name'),'maxlength="254" size="50"');
        $mform->setDefault('name', '');
        $mform->addRule('name', get_string('categorynamecantbeblank', 'quiz'), 'required', null, 'client');
        $mform->setType('name', PARAM_MULTILANG);

        $mform->addElement('textarea', 'info', get_string('categoryinfo', 'quiz'), array('rows'=> '10', 'cols'=>'45'));
        $mform->setDefault('info', '');
        $mform->setType('info', PARAM_MULTILANG);
//--------------------------------------------------------------------------------
        $this->add_action_buttons(false, get_string('addcategory', 'quiz'));
//--------------------------------------------------------------------------------
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);
    }
}
?>
