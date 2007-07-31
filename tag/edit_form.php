<?php  // $Id$

require_once($CFG->dirroot.'/lib/formslib.php');

class tag_edit_form extends moodleform {

function definition () {

    $mform =& $this->_form;
        
        $mform->addElement('header', 'tag', get_string('description','tag'));
         
        $mform->addElement('hidden', 'id');
        
        $mform->addElement('htmleditor', 'description', get_string('description', 'tag'), array('rows'=>20));
        $mform->setType('description', PARAM_CLEANHTML);
        
        $mform->addElement('format', 'descriptionformat', get_string('format'));
        
        $mform->addElement('html', '<br/><div id="relatedtags-autocomplete-container">');
        $mform->addElement('textarea', 'relatedtags', get_string('relatedtags','tag'), 'cols="50" rows="3"');
        $mform->setType('relatedtags', PARAM_MULTILANG);
        $mform->addElement('html', '</div>');
        $mform->addElement('html', '<div id="relatedtags-autocomplete"></div>');
        
        
        $this->add_action_buttons(false, get_string('updatetag', 'tag'));
        
    }

}

?>
