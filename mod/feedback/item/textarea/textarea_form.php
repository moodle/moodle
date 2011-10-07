<?php

require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_form_class.php');

class feedback_textarea_form extends feedback_item_form {
    var $type = "textarea";

    function definition() {
        $item = $this->_customdata['item'];
        $common = $this->_customdata['common'];
        $positionlist = $this->_customdata['positionlist'];
        $position = $this->_customdata['position'];

        $mform =& $this->_form;

        $mform->addElement('header', 'general', get_string($this->type, 'feedback'));
        $mform->addElement('checkbox', 'required', get_string('required', 'feedback'));

        $mform->addElement('text',
                            'name',
                            get_string('item_name', 'feedback'),
                            array('size'=>FEEDBACK_ITEM_NAME_TEXTBOX_SIZE, 'maxlength'=>255));
        $mform->addElement('text',
                            'label',
                            get_string('item_label', 'feedback'),
                            array('size'=>FEEDBACK_ITEM_LABEL_TEXTBOX_SIZE, 'maxlength'=>255));

        $mform->addElement('select',
                            'itemwidth',
                            get_string('textarea_width', 'feedback').'&nbsp;',
                            array_slice(range(0,80),5,80,true));

        $mform->addElement('select',
                            'itemheight',
                            get_string('textarea_height', 'feedback').'&nbsp;',
                            array_slice(range(0,40),5,40,true));

        parent::definition();
        $this->set_data($item);

    }
    
    function get_data() {
        if(!$item = parent::get_data()) {
            return false;
        }
        
        $item->presentation = $item->itemwidth . '|'. $item->itemheight;
        return $item;
    }
}

