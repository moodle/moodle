<?php

require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_form_class.php');

class feedback_numeric_form extends feedback_item_form {
    var $type = "numeric";

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

        $mform->addElement('text',
                            'rangefrom',
                            get_string('numeric_range_from', 'feedback'),
                            array('size'=>10, 'maxlength'=>10));

        $mform->addElement('text',
                            'rangeto',
                            get_string('numeric_range_to', 'feedback'),
                            array('size'=>10,'maxlength'=>10));

        parent::definition();
        $this->set_data($item);

    }

    function get_data() {
        if(!$item = parent::get_data()) {
            return false;
        }
        
        $itemobj = new feedback_item_numeric();

        $num1 = str_replace($itemobj->sep_dec, FEEDBACK_DECIMAL, $item->rangefrom);
        if(is_numeric($num1)) {
            $num1 = floatval($num1);
        }else {
            $num1 = '-';
        }

        $num2 = str_replace($itemobj->sep_dec, FEEDBACK_DECIMAL, $item->rangeto);
        if(is_numeric($num2)) {
            $num2 = floatval($num2);
        }else {
            $num2 = '-';
        }

        if($num1 === '-' OR $num2 === '-') {
            $item->presentation = $num1 . '|'. $num2;
            return $item;
        }

        if($num1 > $num2) {
            $item->presentation =  $num2 . '|'. $num1;
        }else {
            $item->presentation = $num1 . '|'. $num2;
        }
        return $item;
    }

}

