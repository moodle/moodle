<?php

require_once $CFG->libdir.'/formslib.php';

class feedback_label_form extends moodleform {
    var $type = "label";
    var $area;
    
    function definition() {
        $mform =& $this->_form;
        
        $mform->addElement('header', 'general', get_string($this->type, 'feedback'));
        
        $mform->addElement('hidden', 'itemname', $this->type);
        $this->area = $mform->addElement('htmleditor', 'presentation', '', array('rows'=>20));
    }
}
?>
