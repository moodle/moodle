<?php

require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_form_class.php');

class feedback_textfield_form extends feedback_item_form {
    var $type = "textfield";
    var $requiredcheck;
    var $itemname;
    var $itemlabel;
    var $selectwith;
    var $selectheight;

    function definition() {
        $mform =& $this->_form;

        $mform->addElement('header', 'general', get_string($this->type, 'feedback'));
        $this->requiredcheck = $mform->addElement('checkbox', 'required', get_string('required', 'feedback'));

        $this->itemname = $mform->addElement('text', 'itemname', get_string('item_name', 'feedback'), array('size="'.FEEDBACK_ITEM_NAME_TEXTBOX_SIZE.'"','maxlength="255"'));
        $this->itemlabel = $mform->addElement('text', 'itemlabel', get_string('item_label', 'feedback'), array('size="'.FEEDBACK_ITEM_LABEL_TEXTBOX_SIZE.'"','maxlength="255"'));

        $this->selectwith = $mform->addElement('select',
                                            'itemsize',
                                            get_string('textfield_size', 'feedback').'&nbsp;',
                                            array_slice(range(0,255),5,255,true));

        $this->selectheight = $mform->addElement('select',
                                            'itemmaxlength',
                                            get_string('textfield_maxlength', 'feedback').'&nbsp;',
                                            array_slice(range(0,255),5,255,true));

    }
}

