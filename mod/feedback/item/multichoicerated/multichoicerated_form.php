<?php

require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_form_class.php');

class feedback_multichoicerated_form extends feedback_item_form {
    var $type = "multichoicerated";
    var $requiredcheck;
    var $itemname;
    var $itemlabel;
    var $selectadjust;
    var $selecttype;
    var $values;

    function definition() {
        $mform =& $this->_form;

        $mform->addElement('header', 'general', get_string($this->type, 'feedback'));

        $this->requiredcheck = $mform->addElement('checkbox', 'required', get_string('required', 'feedback'));

        $this->itemname = $mform->addElement('text', 'itemname', get_string('item_name', 'feedback'), array('size="'.FEEDBACK_ITEM_NAME_TEXTBOX_SIZE.'"','maxlength="255"'));
        $this->itemlabel = $mform->addElement('text', 'itemlabel', get_string('item_label', 'feedback'), array('size="'.FEEDBACK_ITEM_LABEL_TEXTBOX_SIZE.'"','maxlength="255"'));

        $this->selectadjust = $mform->addElement('select',
                                            'horizontal',
                                            get_string('adjustment', 'feedback').'&nbsp;',
                                            array(0 => get_string('vertical', 'feedback'), 1 => get_string('horizontal', 'feedback')));

        $this->selecttype = $mform->addElement('select',
                                            'subtype',
                                            get_string('multichoicetype', 'feedback').'&nbsp;',
                                            array('r'=>get_string('radio', 'feedback'),
                                                  'd'=>get_string('dropdown', 'feedback')));

        $mform->addElement('static', 'hint', get_string('multichoice_values', 'feedback'), get_string('use_one_line_for_each_value', 'feedback'));

        $this->values = $mform->addElement('textarea', 'itemvalues', '', 'wrap="virtual" rows="10" cols="65"');

    }
}

