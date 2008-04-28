<?php

require_once $CFG->libdir.'/formslib.php';

class feedback_numeric_form extends moodleform {
    var $type = "numeric";
    var $requiredcheck;
    var $itemname;
    var $selectfrom;
    var $selectto;
    
    function definition() {
        $mform =& $this->_form;
        
        $mform->addElement('header', 'general', get_string($this->type, 'feedback'));
        $this->requiredcheck = $mform->addElement('checkbox', 'required', get_string('required', 'feedback'));
        
        $this->itemname = $mform->addElement('text', 'itemname', get_string('item_name', 'feedback'), array('size="40"','maxlength="255"'));
        
        $this->selectfrom = $mform->addElement('text', 'numericrangefrom', get_string('numeric_range_from', 'feedback'), array('size="10"','maxlength="10"'));
        
        $this->selectto = $mform->addElement('text', 'numericrangeto', get_string('numeric_range_to', 'feedback'), array('size="10"','maxlength="10"'));

    }
}
?>
