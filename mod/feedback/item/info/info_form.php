<?php

require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_form_class.php');

class feedback_info_form extends feedback_item_form {
    var $type = "info";

    function definition() {
        
        $item = $this->_customdata['item'];
        $common = $this->_customdata['common'];
        $positionlist = $this->_customdata['positionlist'];
        $position = $this->_customdata['position'];
        
        $mform =& $this->_form;

        $mform->addElement('header', 'general', get_string($this->type, 'feedback'));
        $mform->addElement('hidden', 'required', 0);
        $mform->setType('required', PARAM_INT);

        $mform->addElement('text',
                            'name',
                            get_string('item_name', 'feedback'),
                            array('size'=>FEEDBACK_ITEM_NAME_TEXTBOX_SIZE, 'maxlength'=>255));
        $mform->addElement('text',
                            'label',
                            get_string('item_label', 'feedback'),
                            array('size'=>FEEDBACK_ITEM_LABEL_TEXTBOX_SIZE, 'maxlength'=>255));

        $options=array();
        $options[1]  = get_string('responsetime', 'feedback');
        $options[2]  = get_string('course');
        $options[3]  = get_string('coursecategory');
        $this->infotype = &$mform->addElement('select', 'presentation', get_string('infotype', 'feedback'), $options);

        parent::definition();
        $this->set_data($item);

    }
}

