<?php

require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_form_class.php');

class feedback_label_form extends feedback_item_form {
    var $type = "label";
    var $area;

    function definition() {
        $mform =& $this->_form;

        $mform->addElement('header', 'general', get_string($this->type, 'feedback'));

        $mform->addElement('hidden', 'itemname', $this->type);
        $mform->setType('itemname', PARAM_INT);
        $this->area = $mform->addElement('htmleditor', 'presentation', '', array('rows'=>20));
    }
}

