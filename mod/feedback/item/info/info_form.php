<?php

require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_form_class.php');

class feedback_info_form extends feedback_item_form {
    var $type = "info";
    var $requiredcheck;
    var $itemname;
    var $itemlabel;
    var $infotype;

    function definition() {
        $mform =& $this->_form;

        $mform->addElement('header', 'general', get_string($this->type, 'feedback'));
        $this->requiredcheck = &$mform->addElement('hidden', 'required');
        $mform->setType('required', PARAM_INT);

        $this->itemname = &$mform->addElement('text', 'itemname', get_string('item_name', 'feedback'), array('size="'.FEEDBACK_ITEM_NAME_TEXTBOX_SIZE.'"','maxlength="255"'));
        $this->itemlabel = $mform->addElement('text', 'itemlabel', get_string('item_label', 'feedback'), array('size="'.FEEDBACK_ITEM_LABEL_TEXTBOX_SIZE.'"','maxlength="255"'));

        $options=array();
        $options[1]  = get_string('responsetime', 'feedback');
        $options[2]  = get_string('coursename', 'feedback');
        $options[3]  = get_string('coursecategory', 'feedback');
        $this->infotype = &$mform->addElement('select', 'infotype', get_string('infotype', 'feedback'), $options);

    }
}

