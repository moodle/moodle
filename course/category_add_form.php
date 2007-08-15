<?php

require_once ($CFG->dirroot.'/course/moodleform_mod.php');
class category_add_form extends moodleform {

    // form definition
    function definition() {
        $mform =& $this->_form;
        $mform->addElement('header', 'general', get_string('addnewcategory')); // TODO: localize
        $mform->addElement('text', 'addcategory', get_string('categoryname'), array('size'=>'30'));    
        $mform->addRule('addcategory', get_string('required'), 'required', null);        
        $mform->addElement('htmleditor', 'description', get_string('description'));
        $mform->setType('description', PARAM_RAW);
        $mform->setHelpButton('description', array('writing', 'richtext'), false, 'editorhelpbutton');

        $this->add_action_buttons(false, get_string('submit'));
    }
}

class sub_category_add_form extends moodleform {

    // form definition
    function definition() {
        $mform =& $this->_form;
        $mform->addElement('header', 'general', get_string('addsubcategory')); // TODO: localize
        $mform->addElement('text', 'addcategory', get_string('categoryname'), array('size'=>'30'));    
        $mform->addRule('addcategory', get_string('required'), 'required', null);        
        $mform->addElement('htmleditor', 'description', get_string('description'));
        $mform->setType('description', PARAM_RAW);
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);
        $mform->setHelpButton('description', array('writing', 'richtext'), false, 'editorhelpbutton');
        
        $this->add_action_buttons(false, get_string('submit'));
    }
}
?>