<?php // $Id$
require_once ('moodleform_mod.php');

class mod_label_mod_form extends moodleform_mod {

    function definition() {

        $mform    =& $this->_form;

        $mform->addElement('htmleditor', 'content', get_string('labeltext', 'label'), array('size'=>'64'));
        $mform->setType('content', PARAM_RAW);
        $mform->addRule('content', get_string('required'), 'required', null, 'client');
        $mform->setHelpButton('content', array('questions', 'richtext'), false, 'editorhelpbutton');

        $this->standard_hidden_coursemodule_elements();

        $mform->addElement('modvisible', 'visible', get_string('visible'));

//-------------------------------------------------------------------------------
// buttons
        $this->add_action_buttons();

    }

}
?>
