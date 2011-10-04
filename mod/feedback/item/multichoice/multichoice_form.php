<?php

require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_form_class.php');

class feedback_multichoice_form extends feedback_item_form {
    var $type = "multichoice";

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
                            'horizontal',
                            get_string('adjustment', 'feedback').'&nbsp;',
                            array(0 => get_string('vertical', 'feedback'), 1 => get_string('horizontal', 'feedback')));

        $mform->addElement('select',
                            'subtype',
                            get_string('multichoicetype', 'feedback').'&nbsp;',
                            array('r'=>get_string('radio', 'feedback'),
                                  'c'=>get_string('check', 'feedback'),
                                  'd'=>get_string('dropdown', 'feedback')));

        
        $mform->addElement('selectyesno', 'ignoreempty', get_string('do_not_analyse_empty_submits', 'feedback'));
        $mform->addElement('selectyesno', 'hidenoselect', get_string('hide_no_select_option', 'feedback'));
        
        $mform->addElement('static', 'hint', get_string('multichoice_values', 'feedback'), get_string('use_one_line_for_each_value', 'feedback'));

        $mform->addElement('textarea', 'values', '', 'wrap="virtual" rows="10" cols="65"');

        parent::definition();
        $this->set_data($item);

    }
    
    function set_data($item) {
        $info = $this->_customdata['info'];

        $item->horizontal = $info->horizontal;

        $item->subtype = $info->subtype;

        $itemvalues = str_replace(FEEDBACK_MULTICHOICE_LINE_SEP, "\n", $info->presentation);
        $itemvalues = str_replace("\n\n", "\n", $itemvalues);
        $item->values = $itemvalues;
        
        return parent::set_data($item); 
    }
    
    function get_data() {
        if(!$item = parent::get_data()) {
            return false;
        }
        
        $presentation = str_replace("\n", FEEDBACK_MULTICHOICE_LINE_SEP, trim($item->values));
        if(!isset($item->subtype)) {
            $subtype = 'r';
        }else {
            $subtype = substr($item->subtype, 0, 1);
        }
        if(isset($item->horizontal) AND $item->horizontal == 1 AND $subtype != 'd') {
            $presentation .= FEEDBACK_MULTICHOICE_ADJUST_SEP.'1';
        }
        
        $item->presentation = $subtype.FEEDBACK_MULTICHOICE_TYPE_SEP.$presentation;
        return $item;
    }
}

