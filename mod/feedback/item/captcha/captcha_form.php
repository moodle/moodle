<?php

require_once $CFG->libdir.'/formslib.php';

class feedback_captcha_form extends moodleform {
    var $type = "captcha";
    var $requiredcheck;
    var $itemname;
    var $select;
    
    function definition() {
        $mform =& $this->_form;
        
        $mform->addElement('header', 'general', get_string($this->type, 'feedback'));
        $this->requiredcheck = $mform->addElement('checkbox', 'required', get_string('required', 'feedback'));
        
        $this->itemname = $mform->addElement('text', 'itemname', get_string('item_name', 'feedback'), array('size="40"','maxlength="255"'));
        
        $this->select = $mform->addElement('select',
                                            'count_of_nums', 
                                            get_string('count_of_nums', 'feedback').'&nbsp;', 
                                            array_slice(range(0,10),3,10,true));
        
    }
}
?>
