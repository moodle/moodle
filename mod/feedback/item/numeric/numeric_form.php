<?php

require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_form_class.php');

class feedback_numeric_form extends feedback_item_form {
    var $type = "numeric";
    var $requiredcheck;
    var $itemname;
    var $itemlabel;
    var $selectfrom;
    var $selectto;

    function definition() {
        $mform =& $this->_form;

        $mform->addElement('header', 'general', get_string($this->type, 'feedback'));
        $this->requiredcheck = $mform->addElement('checkbox', 'required', get_string('required', 'feedback'));

        $this->itemname = $mform->addElement('text', 'itemname', get_string('item_name', 'feedback'), array('size="'.FEEDBACK_ITEM_NAME_TEXTBOX_SIZE.'"','maxlength="255"'));
        $this->itemlabel = $mform->addElement('text', 'itemlabel', get_string('item_label', 'feedback'), array('size="'.FEEDBACK_ITEM_LABEL_TEXTBOX_SIZE.'"','maxlength="255"'));

        $this->selectfrom = $mform->addElement('text', 'numericrangefrom', get_string('numeric_range_from', 'feedback'), array('size="10"','maxlength="10"'));

        $this->selectto = $mform->addElement('text', 'numericrangeto', get_string('numeric_range_to', 'feedback'), array('size="10"','maxlength="10"'));

    }
}

